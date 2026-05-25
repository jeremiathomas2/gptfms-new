@php
    $color = $color ?? 'primary';
    $base = match ($color) {
        'success' => '#10b981',
        'error' => '#ef4444',
        default => '#5b8cff',
    };
@endphp

<table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:20px 0;">
    <tr>
        <td align="center">
            <a href="{{ $url }}" target="_blank" rel="noopener" style="display:inline-block;padding:12px 18px;border-radius:12px;background:{{ $base }};background-image:linear-gradient(135deg,{{ $base }},#8b5cf6);color:#ffffff;font-family:'Nunito Sans',Segoe UI,Arial,sans-serif;font-weight:900;font-size:14px;text-decoration:none;box-shadow:0 10px 22px rgba(0,0,0,0.20);">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>

