<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note</title>
    <style>
        /* DomPDF no soporta flexbox: toda la maquetación en columnas va con tablas. */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; line-height: 1.4; padding-bottom: 40px; }

        table { width: 100%; border-collapse: collapse; }

        .title-bar { background: #1E3A8A; color: #fff; margin-bottom: 16px; }
        .title-bar td { padding: 8px 30px; font-weight: bold; }
        .title-bar .title { font-size: 15px; letter-spacing: 3px; }
        .title-bar .number { text-align: right; font-size: 12px; letter-spacing: 1px; }

        .block { margin: 0 30px 14px; }

        .info td { width: 33.33%; padding: 9px 14px; border: 1.5px solid #cbd5e1; vertical-align: top; }
        .info-label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
        .info-value { font-size: 13px; font-weight: bold; color: #111; }

        .remarks-block { padding: 9px 14px; border-left: 3px solid #1E3A8A; background: #f0f4ff; }
        .remarks-label { font-size: 8px; font-weight: bold; color: #1E3A8A; text-transform: uppercase; margin-bottom: 5px; }
        .remarks-text { font-size: 11px; color: #333; line-height: 1.5; }

        .section-head { font-size: 8px; font-weight: bold; color: #1E3A8A; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #1E3A8A; padding-bottom: 3px; margin-bottom: 8px; }

        .data thead th { background: #1E3A8A; color: #fff; padding: 6px 9px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .data tbody td { padding: 6px 9px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: middle; line-height: 1.4; }
        .data tbody tr.even td { background: #f8faff; }
        .data tfoot td { padding: 6px 9px; border-top: 2px solid #1E3A8A; font-size: 10px; font-weight: bold; background: #e8eeff; }
        .data .center { text-align: center; }
        .data .right { text-align: right; }

        .bottom td { vertical-align: top; }
        .cb td { padding: 4px 0; font-size: 11px; font-weight: bold; text-transform: uppercase; vertical-align: middle; }
        .cb td.box-cell { width: 22px; }
        .cb-box { display: block; width: 14px; height: 14px; border: 1.5px solid #374151; text-align: center; line-height: 10px; font-size: 12px; font-weight: bold; color: #1E3A8A; }

        .sign-box { border: 1px solid #9ca3af; height: 125px; text-align: center; }
        .sign-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #555; letter-spacing: 0.5px; padding-top: 106px; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #e5e7eb; }
        .footer td { padding: 6px 30px; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>

@include('pdf.partials.header')

@php
    $deliveryNoteNo = $escala
        ? 'DN-' . str_pad((string) $escala->id, 5, '0', STR_PAD_LEFT)
            . (($soloServicio ?? null) ? '-' . $soloServicio->id : '')
        : '—';

    // Dos columnas: los 3 servicios originales a la izquierda, los 4 nuevos a la derecha.
    $additionalServices = [
        [['Overtime', 'overtime'], ['Riggers', 'riggers']],
        [['Handling Express', 'handling_express'], ['Transport Trucks', 'transport_trucks']],
        [['Crane Service', 'crane_service'], ['Assistants', 'assistants']],
        [null, ['Escort', 'escort']],
    ];
@endphp

<table class="title-bar">
    <tr>
        <td class="title">DELIVERY NOTE</td>
        <td class="number">Nº {{ $deliveryNoteNo }}</td>
    </tr>
</table>

<div class="block">
    <table class="info">
        <tr>
            <td>
                <span class="info-label">Vessel Name / Company Name</span>
                <span class="info-value">{{ strtoupper($escala?->barco?->nombre ?? '—') }}</span>
            </td>
            <td>
                <span class="info-label">Delivery At</span>
                <span class="info-value">{{ strtoupper($escala?->puerto ?? '—') }}</span>
            </td>
            <td>
                <span class="info-label">Date</span>
                <span class="info-value">{{ $escala?->fecha?->format('d/m/Y') ?? '—' }}</span>
            </td>
        </tr>
    </table>
</div>

@if(!empty($escala?->remarks))
<div class="block remarks-block">
    <div class="remarks-label">Remarks</div>
    <div class="remarks-text">{!! nl2br(e($escala->remarks)) !!}</div>
</div>
@endif

<div class="block">
    <div class="section-head">Shipment Details</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width:14%">Courier</th>
                <th style="width:22%">AWB / Courier No.</th>
                <th class="center" style="width:8%">BX</th>
                <th class="right" style="width:12%">KG</th>
                <th style="width:44%">Narrative</th>
            </tr>
        </thead>
        <tbody>
            @forelse($servicios as $servicio)
            <tr class="{{ $loop->even ? 'even' : '' }}">
                <td>{{ $servicio->courier?->nombre ?? '' }}</td>
                <td style="font-size:9px;">{{ $servicio->number ?? '—' }}</td>
                <td class="center">{{ $servicio->bx ?? '—' }}</td>
                <td class="right">{{ $servicio->kg ?? '—' }}</td>
                <td>{!! nl2br(e($servicio->comentarios ?? '')) !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#aaa;padding:16px;">No shipment records found for this port call.</td>
            </tr>
            @endforelse
        </tbody>
        @if($servicios->count() > 1)
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td class="center">{{ $servicios->sum('bx') }}</td>
                <td class="right">{{ number_format((float)$servicios->sum('kg'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<div class="block" style="page-break-inside: avoid;">
    <table class="bottom">
        <tr>
            <td style="width:58%; padding-right:24px;">
                <div class="section-head">Additional Services</div>
                <table class="cb">
                    @foreach($additionalServices as $row)
                    <tr>
                        @foreach($row as $item)
                            @if($item)
                                <td class="box-cell"><span class="cb-box">{{ ($escala?->{$item[1]} ?? false) ? 'X' : '' }}</span></td>
                                <td style="width:{{ $loop->first ? '44%' : 'auto' }}">{{ $item[0] }}</td>
                            @else
                                <td class="box-cell"></td>
                                <td style="width:44%"></td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </table>
            </td>
            <td style="width:42%;">
                <div class="sign-box">
                    <div class="sign-label">Signed &amp; Stamped</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
<table>
    <tr>
        <td>Generated: {{ now()->format('d/m/Y H:i') }}</td>
        <td style="text-align:right;">{{ config('euroship.nombre') }} &nbsp;|&nbsp; {{ config('euroship.web') }}</td>
    </tr>
</table>
</div>

</body>
</html>
