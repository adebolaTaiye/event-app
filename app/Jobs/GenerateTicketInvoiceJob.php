<?php

namespace App\Jobs;

use App\Models\UserEventRegistration;
use App\Models\tickets;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketBought;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateTicketInvoiceJob implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
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

    public function __construct(
        private UserEventRegistration $userEventRegistration
    )
    {
        $this->userEventRegistration = $userEventRegistration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Generating invoice for user event registration ID: {$this->userEventRegistration->id}");
            $ticket= $this->userEventRegistration;
         $validationCodes= tickets::where('user_event_registration_id', $this->userEventRegistration->id)
                ->pluck('validation_code')
                ->toArray();
             $qrCodes = [];
             foreach ($validationCodes as $code) {
                // This will work with the GD extension, as SimpleSoftwareIO\QrCode uses BaconQrCode which supports GD.
                // The generated QR code will be in PNG format.
                $qrCodes[] = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(300)->generate($code));
             }

        Log::info('validation codes'.json_encode($qrCodes));
        $pdf = Pdf::loadView('pdf.invoice', ['ticket' => $ticket, 'qrCodes' => $qrCodes]);
        $pdf->save(storage_path('app/public/invoices/invoice-' . $ticket->reference . '.pdf'));
        } catch (\Exception $e) {
          Log::error("error generating pdf: " . $e->getMessage());
        }

      Mail::to($this->userEventRegistration->user->email)
            ->send(new TicketBought($this->userEventRegistration));

        Log::info("Invoice generated and email sent for user event registration ID: {$this->userEventRegistration->id}");
    }
}

