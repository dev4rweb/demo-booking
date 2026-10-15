@component('emails.layout', ['title' => $title, 'locale' => $locale, 'dir' => $dir])
    <p style="margin:0 0 16px;font-size:16px;line-height:1.6;">{{ $hello }}</p>
    <p style="margin:0 0 20px;font-size:16px;line-height:1.6;">{{ $body }}</p>
    <p style="margin:0;font-size:15px;line-height:1.6;">{{ $thanks }}<br>{{ $business }}</p>
@endcomponent
