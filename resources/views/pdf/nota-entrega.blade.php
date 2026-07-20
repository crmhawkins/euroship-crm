<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }

        .header { padding: 18px 30px 14px; border-bottom: 3px solid #1E3A8A; }
        .header-inner { display: flex; justify-content: space-between; align-items: center; }
        .company-block { text-align: right; font-size: 9px; color: #555; line-height: 1.6; }
        .company-name { font-size: 13px; font-weight: bold; color: #1E3A8A; display: block; margin-bottom: 2px; }

        .title-bar { background: #1E3A8A; color: #fff; padding: 7px 30px; font-size: 14px; font-weight: bold; letter-spacing: 3px; margin-bottom: 16px; }

        .info-row { display: flex; margin: 0 30px 14px; border: 1.5px solid #cbd5e1; }
        .info-cell { flex: 1; padding: 8px 12px; border-right: 1px solid #cbd5e1; }
        .info-cell:last-child { border-right: none; }
        .info-label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .info-value { font-size: 12px; font-weight: bold; color: #111; }

        .remarks-block { margin: 0 30px 14px; padding: 8px 12px; border-left: 3px solid #1E3A8A; background: #f0f4ff; }
        .remarks-label { font-size: 8px; font-weight: bold; color: #1E3A8A; text-transform: uppercase; margin-bottom: 4px; }
        .remarks-text { font-size: 10px; color: #333; }

        .section { margin: 0 30px 16px; }
        .section-head { font-size: 8px; font-weight: bold; color: #1E3A8A; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #1E3A8A; padding-bottom: 3px; margin-bottom: 8px; }

        table { width: 100%; border-collapse: collapse; }
        table thead th { background: #1E3A8A; color: #fff; padding: 5px 8px; text-align: left; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        table thead th.center { text-align: center; }
        table thead th.right { text-align: right; }
        table tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; vertical-align: middle; }
        table tbody td.center { text-align: center; }
        table tbody td.right { text-align: right; }
        table tbody tr:nth-child(even) td { background: #f8faff; }
        table tfoot td { padding: 5px 8px; border-top: 2px solid #1E3A8A; font-size: 9px; font-weight: bold; background: #e8eeff; }
        table tfoot td.center { text-align: center; }
        table tfoot td.right { text-align: right; }

        .checkboxes { margin: 0 30px 20px; }
        .checkbox-row { display: flex; gap: 30px; margin-top: 8px; }
        .cb-item { display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .cb-box { width: 13px; height: 13px; border: 1.5px solid #374151; display: inline-block; flex-shrink: 0; }

        .sign-row { margin: 0 30px 20px; display: flex; justify-content: flex-end; }
        .sign-box { border: 1px solid #9ca3af; padding: 35px 20px 8px; text-align: center; width: 220px; }
        .sign-label { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #555; letter-spacing: 0.5px; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 5px 30px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-inner">
        <div>
            @include('pdf.partials.logo')
        </div>
        <div class="company-block">
            <span class="company-name">EUROSHIP SUPPLIES INTERNATIONAL S.L.</span>
            Calle Oc&eacute;ano Atl&aacute;ntico N&ordm; 6, Los Barrios (C&aacute;diz), Spain<br>
            NIF: B72035900 &nbsp;|&nbsp; info@euroshipspain.com
        </div>
    </div>
</div>

<div class="title-bar">DELIVERY NOTE</div>

<div class="info-row">
    <div class="info-cell" style="flex:2">
        <span class="info-label">Vessel Name / Company Name</span>
        <span class="info-value">{{ strtoupper($escala?->barco?->nombre ?? '&mdash;') }}</span>
    </div>
    <div class="info-cell">
        <span class="info-label">Delivery At</span>
        <span class="info-value">{{ strtoupper($escala?->puerto ?? '&mdash;') }}</span>
    </div>
    <div class="info-cell">
        <span class="info-label">Date</span>
        <span class="info-value">{{ $escala?->fecha?->format('d/m/Y') ?? '&mdash;' }}</span>
    </div>
</div>

@if(!empty($escala?->remarks))
<div class="remarks-block">
    <div class="remarks-label">Remarks</div>
    <div class="remarks-text">{{ $escala->remarks }}</div>
</div>
@endif

<div class="section">
    <div class="section-head">Shipment Details</div>
    <table>
        <thead>
            <tr>
                <th style="width:30%">Narrative</th>
                <th style="width:20%">Remark</th>
                <th style="width:12%">Courier</th>
                <th style="width:20%">AWB / Courier No.</th>
                <th class="center" style="width:9%">BX</th>
                <th class="right" style="width:9%">KG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($servicios as $servicio)
            <tr>
                <td>{{ $servicio->comentarios ?? '' }}</td>
                <td></td>
                <td>{{ $servicio->courier?->nombre ?? '' }}</td>
                <td style="font-size:8px;">{{ $servicio->number ?? '&mdash;' }}</td>
                <td class="center">{{ $servicio->bx ?? '&mdash;' }}</td>
                <td class="right">{{ $servicio->kg ?? '&mdash;' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#aaa;padding:14px;">No shipment records found for this port call.</td>
            </tr>
            @endforelse
        </tbody>
        @if($servicios->count() > 1)
        <tfoot>
            <tr>
                <td colspan="4">TOTAL</td>
                <td class="center">{{ $servicios->sum('bx') }}</td>
                <td class="right">{{ number_format((float)$servicios->sum('kg'), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<div class="checkboxes">
    <div class="section-head">Additional Services</div>
    <div class="checkbox-row">
        <div class="cb-item"><span class="cb-box"></span> Overtime</div>
        <div class="cb-item"><span class="cb-box"></span> Handling</div>
        <div class="cb-item"><span class="cb-box"></span> Express</div>
        <div class="cb-item"><span class="cb-box"></span> Crane Service</div>
    </div>
</div>

<div class="sign-row">
    <div class="sign-box">
        <div class="sign-label">Signed &amp; Stamped</div>
    </div>
</div>

<div class="footer">
    <span>Generated: {{ now()->format('d/m/Y H:i') }}</span>
    <span>Euroship Supplies International S.L. &nbsp;|&nbsp; crm.euroshipspain.com</span>
</div>

</body>
</html>
