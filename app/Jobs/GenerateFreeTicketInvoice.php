<?php

namespace App\Jobs;

use App\Models\UserEventRegistration;
use App\Models\tickets;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBooked;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateFreeTicketInvoice implements ShouldQueue
{
    use Queueable;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 360;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;
    /**
     * Create a new job instance.
     */
    public function __construct(
        private UserEventRegistration $userEventRegistration
    ) {
        $this->userEventRegistration = $userEventRegistration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Generating invoice for user event registration ID: {$this->userEventRegistration->id}");
            $ticket = $this->userEventRegistration;
            $validationCode = tickets::where('user_event_registration_id', $this->userEventRegistration->id)->pluck('validation_code');
            $qrCode = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(300)->generate($validationCode));

            Log::info('validation codes' . json_encode($qrCode));
            $pdf = Pdf::loadView('pdf.invoiceFree', ['ticket' => $ticket, 'qrCode' => $qrCode]);
            $pdf->save(storage_path('app/public/invoices/invoice-' . $this->userEventRegistration->id . '.pdf'));
        } catch (\Exception $e) {
             Log::error("error generating pdf: " . $e->getMessage());
        }
         Mail::to($this->userEventRegistration->user->email)
            ->send(new TicketBooked($this->userEventRegistration));

        Log::info("Invoice generated and email sent for user event registration ID: {$this->userEventRegistration->id}");

    }
}
