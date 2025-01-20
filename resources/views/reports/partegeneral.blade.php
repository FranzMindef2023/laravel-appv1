<!DOCTYPE html>
<html>
<head>
    <title>Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Tamaño de fuente ajustado */
            margin: 0;
            padding: 0;
        }
        .header {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 2px solid black;
        }
        .left {
            text-align: left;
        }
        .left h4, .left p {
            margin: 0;
            line-height: 1.5;
        }
        .center {
            text-align: center;
            flex-grow: 1;
        }
        .center h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
        }
        .center p {
            margin: 0;
            font-size: 14px;
        }
        .right {
            position: absolute;
            top: 10px; /* Ajusta la distancia desde el top */
            right: 20px; /* Ajusta la distancia desde el borde derecho */
        }
        .circle {
            display: inline-block;
            background-color: #6c9bd2; /* Azul similar al de la imagen */
            color: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            line-height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .demostracion-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <h4>MINISTERIO DE DEFENSA NACIONAL</h4>
            <p>UNIDAD DE RECURSOS HUMANOS</p>
        </div>
        <div class="center">
            <p><em>{{ $date }}</em></p>
            <h1>PARTE DEL PERSONAL MILITAR DEL MINISTERIO DE DEFENSA</h1>
            <h1>{{ $user->nomorg }}</h1>
        </div>
        <div class="right">
            <div class="circle">{{ $parte->count() }}</div>
        </div>
    </div>

    <!-- Título de DEMOSTRACIÓN -->
    <!-- <div class="demostracion-title">
        DEMOSTRACIÓN
    </div> -->
    
    <table>
        <thead>
            <tr>
                <!-- <th>N°</th> -->
                <th>DETALLE</th>
                <th>OF. GENERALES</th>
                <th>OF. SUPERIORES</th>
                <th>OF. SUBALTERNOS</th>
                <th>SUBOFICIALES</th>
                <th>SARGENTOS</th>
                <th>CIVIL ADMIN</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($parte as $index => $novedad)
            <tr>
                <!-- <td>{{ $index + 1 }}</td> -->
                <td>{{ $novedad->descripcion }}</td>
                <td>{{ $novedad->oficiales_generales }}</td>
                <td>{{ $novedad->oficiales_superiores }}</td>
                <td>{{ $novedad->oficiales_subalternos }}</td>
                <td>{{ $novedad->suboficiales }}</td>
                <td>{{ $novedad->sargentos }}</td>
                <td>{{ $novedad->civiles }}</td>
                <td>{{ $novedad->total_general }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Sección de firma -->
    <div class="signature-section" style="margin-top: 50px; text-align: center;">
        <p style="margin: 0; font-weight: bold; ">
            {{ $user->nombre_completo }} 
        </p>
        <p style="margin: 0;">
            {{ $user->nompuesto }}
        </p>
    </div>
</body>
</html>
