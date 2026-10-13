<x-mail::message>
# Booking confirmed

Hi {{ $booking->client->name }},

Your appointment at **{{ $booking->tenant->name }}** is confirmed.

- **Service:** {{ $booking->service->name }}
- **Staff:** {{ $booking->staffMember->name }}
- **When:** {{ $when }} ({{ $booking->timezone }})
- **Price:** ${{ number_format($booking->service->price_cents / 100, 2) }} USD

<x-mail::button :url="$manageUrl">
Manage booking
</x-mail::button>

Thanks,<br>
{{ $booking->tenant->name }}
</x-mail::message>
