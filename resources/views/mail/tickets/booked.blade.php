<x-mail::message>
# Registration Successful!

 <p>Thank you for registering for <strong>{{ $eventName }}</strong>.</p>

 <x-mail::panel>
    <strong>Event Date:</strong> {{ $eventDate }}<br>
    <strong>Tickets Booked:</strong> {{ $ticketQuantity }}<br>
    <strong>Ticket Type:</strong> {{ $ticketType }}
 </x-mail::panel>
<x-mail::button :url="'/events/'.{{ $id }}">
View Event
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

{{-- <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Registration Successful</title>
</head>
<body>
    <h2>Registration Successful!</h2>
    <p>Thank you for registering for <strong>{{ $eventName }}</strong>.</p>
    <p>
        <strong>Event Date:</strong> {{ $eventDate }}<br>
        <strong>Tickets Booked:</strong> {{ $ticketQuantity }}<br>
        <strong>Ticket Type:</strong> {{ $ticketType }}
    </p>
    <p>We look forward to seeing you at the event!</p>
</body>
</html> --}}
