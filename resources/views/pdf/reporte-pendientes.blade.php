<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Pedidos Pendientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }


        .title-bar { background: #293C8E; color: white; padding: 7px 30px; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 15px; }

        .escala-info { padding: 0 30px; margin-bottom: 15px; background: #f0f4ff; border-left: 4px solid #293C8E; padding: 10px 15px; margin: 0 30px 15px; }
        .escala-info strong { color: #293C8E; }

        .section { padding: 0 30px; margin-bottom: 18px; }

        table { width: 100%; border-collapse: collapse; }
        table.data th { background: #293C8E; color: white; padding: 5px 7px; text-align: left; font-size: 9px; text-transform: uppercase; }
        table.data td { padding: 5px 7px; border-bottom: 1px solid #e5e5e5; font-size: 9px; vertical-align: top; }

        .pedido-header td { background: #e8eeff !important; font-weight: bold; font-size: 10px; }
        .pertrecho-row td { padding-left: 20px; color: #444; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-gray    { background: #e5e7eb; color: #374151; }
        .badge-primary { background: #dbeafe; color: #1e3a8a; }

        .empty { text-align: center; padding: 20px; color: #888; font-style: italic; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; border-top: 1px solid #ddd; }
        .footer td { padding: 8px 30px; font-size: 8px; color: #888; }
    </style>
</head>
<body>

@include('pdf.partials.header')

<div class="title-bar" style="margin-top: 0;">REPORTE — PEDIDOS PENDIENTES POR ESCALA</div>

<div class="escala-info">
    <strong>Escala #{{ $escala->id }}</strong> &nbsp;|&nbsp;
    <strong>Barco:</strong> {{ $escala->barco?->nombre ?? '—' }} &nbsp;|&nbsp;
    <strong>Cliente:</strong> {{ $escala->barco?->cliente?->nombre ?? '—' }} &nbsp;|&nbsp;
    <strong>Puerto:</strong> {{ $escala->puerto }} &nbsp;|&nbsp;
    <strong>Fecha:</strong> {{ $escala->fecha?->format('d/m/Y') ?? '—' }}
</div>

<div class="section">

    @if($pedidos->isEmpty())
        <p class="empty">No hay pedidos pendientes para esta escala.</p>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Fecha</th>
                    <th>Puerto entrega</th>
                    <th>Estado</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $pedido)
                    <tr class="pedido-header">
                        <td>{{ $pedido->numero_pedido }}</td>
                        <td>{{ $pedido->fecha_pedido?->format('d/m/Y') }}</td>
                        <td>{{ $pedido->puerto_entrega }}</td>
                        <td>
                            <span class="badge badge-{{ \App\Models\Estado::colorDe('pedido', $pedido->estado_general) }}">{{ \App\Models\Estado::etiqueta('pedido', $pedido->estado_general) }}</span>
                        </td>
                        <td>{{ $pedido->notas }}</td>
                    </tr>
                    @foreach($pedido->pertrechos as $p)
                        <tr class="pertrecho-row">
                            <td colspan="2">↳ {{ $p->descripcion }}</td>
                            <td>{{ $p->cantidad }} {{ $p->unidad }}</td>
                            <td colspan="2">
                                <span class="badge {{ $p->estado === 'entregado' ? 'badge-info' : 'badge-warning' }}">
                                    {{ ucfirst($p->estado) }}
                                </span>
                                {{ $p->notas }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 10px; font-size: 9px; color: #555;">
            Total pedidos: <strong>{{ $pedidos->count() }}</strong>
        </div>
    @endif
</div>

<div class="footer">
<table>
    <tr>
        <td>Generado: {{ now()->format('d/m/Y H:i') }}</td>
        <td style="text-align:right;">{{ config('euroship.nombre') }} &nbsp;|&nbsp; {{ config('euroship.web') }}</td>
    </tr>
</table>
</div>

</body>
</html>
