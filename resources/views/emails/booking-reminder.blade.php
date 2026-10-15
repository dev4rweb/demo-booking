<x-mail::message>
# Reminder

Hi {{ $booking->client->name }},

This is a reminder for your appointment at **{{ $booking->tenant->name }}**.

- **Service:** {{ $booking->service->name }}
- **Staff:** {{ $booking->staffMember->name }}
- **When:** {{ $when }} ({{ $booking->timezone }})

See you soon,<br>
{{ $booking->tenant->name }}
</x-mail::message>
