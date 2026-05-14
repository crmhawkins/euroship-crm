<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Entrega</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 30px; border-bottom: 3px solid #293C8E; margin-bottom: 20px; }
        .logo-area img { height: 60px; }
        .company-info { text-align: right; font-size: 10px; color: #555; }
        .company-info strong { font-size: 14px; color: #293C8E; }

        .title-bar { background: #293C8E; color: white; padding: 8px 30px; font-size: 14px; font-weight: bold; letter-spacing: 1px; margin-bottom: 20px; }

        .section { padding: 0 30px; margin-bottom: 18px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #293C8E; border-bottom: 1px solid #293C8E; padding-bottom: 3px; margin-bottom: 10px; }

        .grid-2 { display: flex; gap: 20px; }
        .grid-2 .col { flex: 1; }
        .field { margin-bottom: 6px; }
        .field label { font-size: 9px; color: #888; text-transform: uppercase; display: block; }
        .field span { font-size: 11px; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; }
        table th { background: #293C8E; color: white; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
        table td { padding: 6px 8px; border-bottom: 1px solid #e5e5e5; font-size: 10px; }
        table tr:nth-child(even) td { background: #f8f9ff; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-ok { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 10px 30px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; font-size: 9px; color: #888; }

        .sign-area { padding: 30px; display: flex; justify-content: space-between; margin-top: 30px; }
        .sign-box { text-align: center; width: 45%; }
        .sign-line { border-top: 1px solid #555; padding-top: 5px; font-size: 9px; color: #555; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo-area">
        <img src="https://euroshipspain.com/wp-content/uploads/2025/08/cropped-logo_euroship-1-270x270.png" alt="Euroship">
    </div>
    <div class="company-info">
        <strong>EUROSHIP SPAIN</strong><br>
        Puerto de Algeciras<br>
        info@euroshipspain.com
    </div>
</div>

<div class="title-bar">NOTA DE ENTREGA</div>

<div class="section">
    <div class="section-title">Datos de la escala</div>
    <div class="grid-2">
        <div class="col">
            <div class="field"><label>Cliente</label><span>{{ $servicio->escala?->barco?->cliente?->nombre ?? '—' }}</span></div>
            <div class="field"><label>Buque</label><span>{{ $servicio->escala?->barco?->nombre ?? '—' }}</span></div>
        </div>
        <div class="col">
            <div class="field"><label>Puerto / Escala</label><span>{{ $servicio->escala?->puerto ?? '—' }}</span></div>
            <div class="field"><label>Fecha escala</label><span>{{ $servicio->escala?->fecha?->format('d/m/Y') ?? '—' }}</span></div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Datos del conocimiento</div>
    <div class="grid-2">
        <div class="col">
            <div class="field"><label>Courier</label><span>{{ $servicio->courier?->nombre ?? '—' }}</span></div>
            <div class="field"><label>Number / Tracking</label><span>{{ $servicio->number ?? '—' }}</span></div>
            <div class="field"><label>Fecha llegada</label><span>{{ $servicio->llegada?->format('d/m/Y') ?? 'Pendiente' }}</span></div>
        </div>
        <div class="col">
            <div class="field"><label>Bultos (BX)</label><span>{{ $servicio->bx ?? '—' }}</span></div>
            <div class="field"><label>Peso (KG)</label><span>{{ $servicio->kg ?? '—' }}</span></div>
            <div class="field"><label>Estatus Aduanero</label><span>{{ $servicio->estatusAduanero?->nombre ?? '—' }}</span></div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Ubicación y observaciones</div>
    <div class="grid-2">
        <div class="col">
            <div class="field"><label>Ubicación</label><span>{{ $servicio->ubicacion?->nombre ?? '—' }}</span></div>
            <div class="field">
                <label>Estado</label>
                @if($servicio->incidencia)
                    <span class="badge badge-danger">INCIDENCIA</span>
                @elseif($servicio->llegada)
                    <span class="badge badge-ok">RECIBIDO</span>
                @else
                    <span class="badge badge-pending">PENDIENTE</span>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="field"><label>Comentarios</label><span>{{ $servicio->comentarios ?? '—' }}</span></div>
        </div>
    </div>
</div>

<div class="sign-area">
    <div class="sign-box">
        <div style="height: 40px;"></div>
        <div class="sign-line">Firma receptor</div>
    </div>
    <div class="sign-box">
        <div style="height: 40px;"></div>
        <div class="sign-line">Sello / Conforme</div>
    </div>
</div>

<div class="footer">
    <span>Nota generada: {{ now()->format('d/m/Y H:i') }}</span>
    <span>Euroship Spain — crm.euroshipspain.com</span>
</div>

</body>
</html>
