<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EventResource;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Auth;
use App\Filters\FiltersEventMonth;
use App\Http\Requests\UserEventRegistrationRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
//use App\Models\User;
use App\Models\TicketTypes;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBooked;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UserEventRegistration;
use App\Jobs\GenerateTicketInvoiceJob;
use App\Models\tickets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;



class UserEventController extends Controller
{
    public function index()
    {
        $event = QueryBuilder::for(Event::class)
            ->allowedFilters([
                AllowedFilter::custom('month', new FiltersEventMonth)
            ])
            ->allowedSorts('name')
            ->orderBy('updated_at', 'desc')
            ->paginate(6)
            ->appends(request()->query());

        return Inertia::render('User/Index', [
            'events' => EventResource::collection($event)
        ]);
    }

    public function show(string $id)
    {
        $event = Event::with(['ticketTypes'])->findOrFail($id);
        return Inertia::render('User/Show', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'image' => Storage::url($event->image),
                'date' => Carbon::parse($event->date)->format('jS F Y g:ia'),
                'registration_expires_at' => Carbon::parse($event->registration_expires_at)->format('jS F Y g:ia'),
                'ticket_types' => $event->ticketTypes ? $event->ticketTypes : null
            ]
        ]);
    }

    public function getPaymentCompletionPage()
    {
        return Inertia::render('User/Payment');
    }

    public function payForTickets(UserEventRegistrationRequest $request)
    {
        $data1 = $request->validated();

        $event = Event::findOrFail($data1['event_id']);
        if ($event->registration_expires_at < now()) {
            return redirect()->route('user.show', $data1['event_id'])->with('message2', 'Sorry the event registration has expired');
        }
        if ($event->date < now()) {
            return redirect()->route('user.show', $data1['event_id'])->with('message2', 'Sorry the event has already taken place');
        }
        $tickets = TicketTypes::findOrFail($data1['ticket_type_id']);
        $check =  DB::table('ticket_types')->where('id', $data1['ticket_type_id'])
            ->where('ticket_available', '>=', $data1['quantity'])->first();
        if (!$check) {
            return redirect()->route('user.show', $data1['event_id'])->with('message2', "Sorry we couldn't fulfil your request for {$data1['quantity']} {$tickets->ticket_type} tickets only {$tickets->ticket_available} {$tickets->ticket_type} tickets are available");
        }
        if ($tickets->ticket_available < $data1['quantity']) {
            return redirect()->route('user.show', $data1['event_id'])->with('message2', "Sorry we couldn't fulfil your request for {$data1['quantity']} {$tickets->ticket_type} tickets only {$tickets->ticket_available} {$tickets->ticket_type} tickets are available");
        }

        $user = Auth::user();

        $token = env('PAYSTACK_SECRET_KEY');

        $data2 = [
            'email' => $user->email,
            'amount' => $data1['total'] * 100,
            'callback_url' => ' https://4442-102-91-4-205.ngrok-free.app/payment/callback'
        ];

        $booking = new UserEventRegistration();
        $booking->user_id = $data1['user_id'];
        $booking->event_id =  $data1['event_id'];
        $booking->ticket_type_id = $data1['ticket_type_id'];
        $booking->quantity = $data1['quantity'];
        $booking->status = 'pending';
        $booking->save();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', $data2);

        dd($response);
        if ($response["data"]["access_code"]) {

            $booking->reference = $response["data"]["reference"];
            $booking->save();
            return Inertia::render('User/Payment', [
                'access_code' => $response["data"]["access_code"]
            ]);
        }
    }

    public function handleWebhook(Request $request)
    {

        Log::info("connected to webhook");
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');
        $key = env('PAYSTACK_SECRET_KEY');
        $isValid = $this->isValidWebhookSignature($payload, $signature, $key);
        if ($isValid) {
            $data = json_decode($payload, true);
            $event = $data['event'];
            $reference = $data['data']['reference'];
            $amount = $data["data"]["amount"];

            if ($event == 'charge.success') {
                $userEventRegistration = UserEventRegistration::where('reference', $reference)->first();
                if ($userEventRegistration) {

                    if ($userEventRegistration->status == 'paid') {
                        Log::info("Payment already processed for reference: {$reference}");
                    } else {
                        $userEventRegistration->status = 'paid';
                        $userEventRegistration->amount = $amount;
                        $userEventRegistration->save();
                        $i = 1;
                        $y = $userEventRegistration->quantity;

                        while ($i <= $y) {
                            $ticket = new tickets();
                            $ticket->user_event_registration_id = $userEventRegistration->id;
                            $ticket->validation_code = Str::random(20);
                            $ticket->status = 'booked';
                            $ticket->save();
                            $i++;
                        }
                        $event = Event::where('id', $userEventRegistration->event_id)->first();
                        if ($event) {
                            $event->available_ticket = $event->available_ticket - $userEventRegistration->quantity;
                            $event->save();
                        }

                        $ticketType = TicketTypes::where('id', $userEventRegistration->ticket_type_id)->first();
                        if ($ticketType) {
                            $ticketType->ticket_available = $ticketType->ticket_available - $userEventRegistration->quantity;
                            $ticketType->save();
                        }
                        $ticket = UserEventRegistration::where('reference', $reference)->first();

                        // Dispatch a job to generate and save the PDF asynchronously
                        GenerateTicketInvoiceJob::dispatch($userEventRegistration);
                    }
                }
            } else if ($event == 'charge.failed') {
                $userEventRegistration = UserEventRegistration::where('reference', $reference)->first();
                if ($userEventRegistration) {
                    $userEventRegistration->status = 'failed';
                    $userEventRegistration->save();
                }
            }
        }
    }

    public function handleCallback(Request $request)
    {

        $reference = $request->query('reference');
        $token =  env('PAYSTACK_SECRET_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.paystack.co/transaction/verify/{$reference}");

        $userEventRegistration = UserEventRegistration::where('reference', $reference)->first();

        if ($response["data"]["status"] == "success") {

            return Inertia::render('User/PaymentSuccess', [
                'message' => 'Payment was successful',
                'transaction' => [
                    'reference' => $response["data"]["reference"],
                    'amount' => $response["data"]["amount"],
                    'email' => $response["data"]['customer']["email"]

                ],
            ]);
        } else {
        }
    }

    public function paymentSuccess()
    {
        return Inertia::render('User/PaymentSuccess');
    }

    public function register(UserEventRegistrationRequest $request)
    {
        $data = $request->validated();

        $checkIfUserAlreadyHasRegistered = UserEventRegistration::where('user_id', $data['user_id'])
            ->where('event_id', $data['event_id'])
            ->first();

        if ($checkIfUserAlreadyHasRegistered) {

            return redirect()->route('user.index')->with('message', 'You are already registered for the event');
        } else {

            $register = UserEventRegistration::create($data);

            $ticket = new tickets();
            $ticket->user_event_registration_id = $register->id;
            $ticket->validation_code = Str::random(20);
            $ticket->status = 'booked';
            $ticket->save();

            try {
                Log::info("Generating invoice for user event registration ID: {$register->id}");
                $ticket = $register;
                $validationCode = tickets::where('user_event_registration_id', $register->id)->pluck('validation_code');
                $qrCode = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(300)->generate($validationCode));

                Log::info('validation codes' . json_encode($qrCode));
                $pdf = Pdf::loadView('pdf.invoiceFree', ['ticket' => $ticket, 'qrCode' => $qrCode]);
                $pdf->save(storage_path('app/public/invoices/invoice-' . $register->id . '.pdf'));
            } catch (\Exception $e) {
                Log::error("error generating pdf: " . $e->getMessage());
            }
            Mail::to($register->user->email)
                ->send(new TicketBooked($register));

            Log::info("Invoice generated and email sent for user event registration ID: {$register->id}");


            return redirect()->route('user.index')->with('message', 'event registered for successfully');
        }
    }

    private function isValidWebhookSignature($payload, $signature, $key)
    {

        $expectedSignature = hash_hmac('sha512', $payload, $key);
        return hash_equals($expectedSignature, $signature);
    }

    public function generateTicketInvoice($ticketId)
    {
        $ticket = UserEventRegistration::where('reference', $ticketId)->first();
        // dd($ticket);
        return Pdf::view('pdf.invoice', compact('ticket'))
            ->format('a4')
            ->name("invoice-{$ticket->reference}.pdf")
            ->download();
    }
}
