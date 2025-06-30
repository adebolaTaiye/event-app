<x-mail::message>
# Registration Successful!

 <p>Thank you for registering for <strong>{{ $eventName }}</strong>.</p>

 <x-mail::panel>
    <strong>Event Date:</strong> {{ $eventDate }}<br>
    <strong>Tickets Booked:</strong> 1<br>
    <strong>Ticket Type:</strong>Free
 </x-mail::panel>
<x-mail::button :url="'/events/{{ $id }}'">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
