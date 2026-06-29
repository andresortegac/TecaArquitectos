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

        /* =========================
           MARCA DE AGUA
        ========================== */
        .watermark {
            position: fixed;
            top: 35%;
            left: 20%;
            width: 60%;
            opacity: 0.08;
            z-index: -1;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 15px;
        }

        .line {
            border-bottom: 1px solid #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            background: #f2f2f2;
        }

        .footer {
            margin-top: 40px;
        }

        .firma {
            margin-top: 50px;
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .firma-linea {
            margin-top: 40px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>

{{-- =========================
     MARCA DE AGUA
========================== --}}
<img src="{{ public_path('img/LOGIN/logo_factura.jpeg') }}" class="watermark">
</head>

<body>

<h2>TECA ARQUITECTOS</h2>
<p><strong>Dirección:</strong> B/ JARDIN</p>
<p><strong>Teléfono:</strong> 3138501801</p>
<p><strong>Email:</strong> tecaarquitectos@gmail.com</p>
<p><strong>NIT:</strong> 12345678-1</p>

<hr>

<h4>Información del Cliente</h4>
<p><strong>Nombre:</strong> {{ $arriendo->cliente->nombre }}</p>
<p><strong>Dirección Obra:</strong> {{ $arriendo->obra->direccion }}</p>
<p><strong>Fecha:</strong> {{ now()->format('Y-m-d') }}</p>
<p><strong>N° Factura:</strong> AR-{{ str_pad($arriendo->id, 5, '0', STR_PAD_LEFT) }}</p>

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
        @foreach($arriendo->items as $item)
            <tr>
                <td>{{ $item->producto->nombre }}</td>
                <td>{{ $item->cantidad_inicial }}</td>
                <td>SÍ</td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<p><strong>Observaciones:</strong></p>
<p>
El alquiler inicia en la fecha acordada, con las herramientas en condiciones
excelentes. Cualquier daño ocasionado durante el período de alquiler será
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
