{{-- Cabecera común de los PDF: logo | certificaciones (ISO + IMPA) | datos de empresa.
     Maquetado con tabla porque DomPDF no soporta flexbox. Imágenes en base64 para no depender de enable_remote. --}}
@php
    $__img = function (string $file): ?string {
        $path = public_path('images/' . $file);
        if (! is_file($path)) {
            return null;
        }
        $mime = str_ends_with($file, '.png') ? 'image/png' : 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    };
    $__logo = $__img('euroship-logo-2026.jpg') ?? $__img('euroship-logo.png');
@endphp
<table class="pdf-header" style="width:100%; border-collapse:collapse; border-bottom:3px solid #1E3A8A;">
    <tr>
        <td style="width:20%; padding:14px 0 12px 30px; vertical-align:middle; border:none; background:none;">
            @if ($__logo)
                <img src="{{ $__logo }}" alt="Euroship" style="height:88px; width:auto;">
            @endif
        </td>
        <td style="width:28%; padding:14px 0 12px 0; vertical-align:middle; text-align:center; border:none; background:none;">
            @if ($__img('iso-9001.jpg'))
                <img src="{{ $__img('iso-9001.jpg') }}" alt="ISO 9001" style="height:46px; width:auto;">
            @endif
            @if ($__img('iso-14001.jpg'))
                <img src="{{ $__img('iso-14001.jpg') }}" alt="ISO 14001" style="height:46px; width:auto; margin-left:8px;">
            @endif
            <br>
            @if ($__img('impa-logo.jpg'))
                <img src="{{ $__img('impa-logo.jpg') }}" alt="IMPA" style="height:34px; width:auto; margin-top:6px;">
            @endif
        </td>
        <td style="width:52%; padding:14px 30px 12px 0; vertical-align:middle; text-align:right; font-size:10px; color:#374151; line-height:1.6; border:none; background:none;">
            <span style="font-size:14px; font-weight:bold; color:#1E3A8A;">{{ config('euroship.nombre') }}</span><br>
            {{ config('euroship.direccion') }}<br>
            <strong>EORI:</strong> {{ config('euroship.eori') }}<br>
            <strong>Phone 24h:</strong> {{ config('euroship.telefono') }}<br>
            <strong>Email:</strong> {{ config('euroship.email') }}
        </td>
    </tr>
</table>
