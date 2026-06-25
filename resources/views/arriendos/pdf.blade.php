<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .watermark {
            position: fixed;
            top: 35%;
            left: 20%;
            width: 60%;
            opacity: 0.08;
            z-index: -1;
        }

        h1, h2, h3, h4 {
            margin: 0 0 8px;
            padding: 0;
        }

        p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            background: #f2f2f2;
        }

        .no-border td {
            border: none;
            padding-top: 35px;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('img/LOGIN/logo_factura.jpeg');
    $obraTexto = $arriendo->obra
        ? trim(($arriendo->obra->direccion ?? '') . ' ' . ($arriendo->obra->detalle ?? ''))
        : '';
@endphp

@if(is_file($logoPath))
    <img src="{{ $logoPath }}" class="watermark" alt="TECA Arquitectos">
@endif

<h2>TECA ARQUITECTOS</h2>
<p><strong>Direccion:</strong> B/ JARDIN</p>
<p><strong>Telefono:</strong> 3138501801</p>
<p><strong>Email:</strong> tecaarquitectos@gmail.com</p>
<p><strong>NIT:</strong> 12345678-1</p>

<hr>

<h4>Informacion del Cliente</h4>
<p><strong>Nombre:</strong> {{ $arriendo->cliente->nombre ?? 'Cliente no registrado' }}</p>
<p><strong>Direccion Obra:</strong> {{ $obraTexto !== '' ? $obraTexto : 'Sin obra registrada' }}</p>
<p><strong>Fecha:</strong> {{ now()->format('Y-m-d') }}</p>
<p><strong>No. Factura:</strong> AR-{{ str_pad($arriendo->id, 5, '0', STR_PAD_LEFT) }}</p>

<hr>

<h4>Herramientas en Alquiler</h4>

<table>
    <thead>
        <tr>
            <th>Herramienta</th>
            <th>Cantidad</th>
            <th>Aprobado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($arriendo->items as $item)
            <tr>
                <td>{{ $item->producto->nombre ?? 'Producto no registrado' }}</td>
                <td>{{ $item->cantidad_inicial ?? $item->cantidad_actual ?? 0 }}</td>
                <td>SI</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Sin herramientas registradas.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<p><strong>Observaciones:</strong></p>
<p>
El alquiler inicia en la fecha acordada, con las herramientas en condiciones
excelentes. Cualquier dano ocasionado durante el periodo de alquiler sera
responsabilidad del cliente.
</p>

<br><br>

<table class="no-border">
    <tr>
        <td>Firma Responsable Bodega:<br><br>__________________________</td>
        <td>Firma Cliente:<br><br>__________________________</td>
    </tr>
</table>

<br>
<p>Gracias por su preferencia.</p>

</body>
</html>
