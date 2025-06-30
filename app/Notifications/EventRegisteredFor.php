<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRegisteredFor extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct( $event,$eventRegisteredFor, $ticketName)
    {
       // $this->user = $user;
        $this->event = $event;
        $this->eventRegisteredFor = $eventRegisteredFor;
        $this->ticketName = $ticketName;
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("You're Registered for {$this->event->name}")
                    ->line("Congratulations! You've successfully registered for {$this->event->name} on {$this->event->date}")
                    ->lineIf($this->eventRegisteredFor['quantity'] > 0, "Ticket number: {$this->eventRegisteredFor['quantity']} and Ticket Type: {$this->ticketName } ")
                    ->action('View Event', url('/events/'.$this->event->id))
                    ->line("If you have any questions or concerns please don't hesitate to to reach out to us at {$this->event->user->email}")
                    ->line("Thank you for registering we look forward to seeing you at {$this->event->name}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
