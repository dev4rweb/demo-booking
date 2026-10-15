@component('emails.layout', ['title' => $title, 'locale' => $locale, 'dir' => $dir])
    <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">{{ $hello }}</p>
    <p style="margin:0 0 20px;font-size:16px;line-height:1.6;">{{ $intro }}</p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 24px;background:#f4efe8;border-radius:14px;">
        <tr>
            <td style="padding:16px 18px;font-size:15px;line-height:1.7;">
                <p style="margin:0;"><strong>{{ $serviceLabel }}:</strong> {{ $service }}</p>
                <p style="margin:8px 0 0;"><strong>{{ $staffLabel }}:</strong> {{ $staff }}</p>
                <p style="margin:8px 0 0;"><strong>{{ $whenLabel }}:</strong> {{ $when }}</p>
                <p style="margin:8px 0 0;"><strong>{{ $priceLabel }}:</strong> {{ $price }}</p>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 24px;">
        <a href="{{ $manageUrl }}" style="display:inline-block;background:#94621c;color:#ffffff;text-decoration:none;border-radius:12px;padding:12px 18px;font-weight:600;">{{ $manage }}</a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.6;">{{ $thanks }}<br>{{ $business }}</p>
@endcomponent
