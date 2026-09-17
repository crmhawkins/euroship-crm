<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; padding:24px; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-top:4px solid #1E3A8A;">
        <tr>
            <td style="padding:24px 28px; font-size:14px; line-height:1.6;">
                {!! nl2br(e($mensaje)) !!}
            </td>
        </tr>
        <tr>
            <td style="padding:16px 28px; border-top:1px solid #e5e7eb; font-size:11px; line-height:1.6; color:#6b7280;">
                <strong style="color:#1E3A8A;">{{ config('euroship.nombre') }}</strong><br>
                {{ config('euroship.direccion') }}<br>
                Phone 24h: {{ config('euroship.telefono') }} &nbsp;|&nbsp; {{ config('euroship.email') }}<br>
                <br>
                This message was sent from an unattended mailbox. Please reply to {{ config('euroship.email') }}.
            </td>
        </tr>
    </table>
</body>
</html>
