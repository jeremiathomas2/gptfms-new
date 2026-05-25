<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background: #0b1020; }
        table { border-collapse: collapse; }
        a { color: #5b8cff; }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .card { border-radius: 16px !important; }
            .pad { padding: 18px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#0b1020;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#0b1020;">
    <tr>
        <td align="center" style="padding:34px 12px;">
            <table class="container" width="600" cellpadding="0" cellspacing="0" role="presentation" style="width:600px;max-width:600px;">
                <tr>
                    <td>
                        {{ $header ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td class="card" style="background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid rgba(255,255,255,0.10);box-shadow:0 18px 40px rgba(0,0,0,0.35);">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td style="height:6px;background:linear-gradient(90deg,#5b8cff,#8b5cf6,#ff5ab4);font-size:0;line-height:0;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="pad" style="padding:26px 28px;font-family:'Nunito Sans',Segoe UI,Arial,sans-serif;color:#0b1020;font-size:15px;line-height:1.55;">
                                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                                </td>
                            </tr>
                            @isset($subcopy)
                                <tr>
                                    <td style="padding:0 28px 26px;">
                                        {{ $subcopy }}
                                    </td>
                                </tr>
                            @endisset
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:16px;">
                        {{ $footer ?? '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

