<x-mail::message>
# Booking cancelled

Hi {{ $booking->client->name }},

Your appointment at **{{ $booking->tenant->name }}** on {{ $when }} has been cancelled.

Thanks,<br>
{{ $booking->tenant->name }}
</x-mail::message>
