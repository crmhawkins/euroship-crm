{{-- Logo Euroship empotrado en base64 para DomPDF (evita dependencia de imagen remota / enable_remote) --}}
@php
    $__euroshipLogoPath = public_path('images/euroship-logo.png');
@endphp
@if (is_file($__euroshipLogoPath))
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($__euroshipLogoPath)) }}" alt="Euroship" style="max-height: 80px; width: auto;">
@endif
