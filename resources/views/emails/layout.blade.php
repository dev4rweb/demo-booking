<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}" dir="{{ $dir ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f7f4ef;color:#1a1d23;font-family:Figtree,Georgia,serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f4ef;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #dcd5ca;border-radius:20px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 28px 8px;background:#0a0c10;color:#f1efeA;">
                            <p style="margin:0;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#e8c07a;">{{ trans('mail.brand', [], $locale ?? 'en') }}</p>
                            <h1 style="margin:10px 0 0;font-size:24px;line-height:1.3;font-weight:600;color:#f1efeA;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px 22px;border-top:1px solid #dcd5ca;font-size:12px;color:#525863;">
                            {{ trans('mail.footer', [], $locale ?? 'en') }} · noreply@dev4rweb.com · <a href="https://demo-booking.dev4rweb.com" style="color:#94621c;text-decoration:none;">demo-booking.dev4rweb.com</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
