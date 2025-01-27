<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Permiso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Fuente de 12 píxeles aplicada a todo el documento */
            margin: 0;
            padding: 0;
        }

        .boleta-container {
            width: 100%;
            max-width: 800px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 80px;
        }

        .title {
            text-align: center;
            flex-grow: 1;
        }

        .title h1 {
            font-size: 20px;
            margin: 0;
        }
        
        .right-text {
            text-align: right;
            font-size: 12px;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 5px 0;
        }

        .details {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .details th, .details td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        .signatures {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .signatures th, .signatures td {
            padding: 15px;
            text-align: center;
        }

        .note {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }

        /* Línea segmentada */
        .dashed-line {
            border-top: 1px dashed #000;
            width: 100%;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="boleta-container">
        <!-- Contenido superior -->
        <div class="header">
            <img src="logo.png" alt="Logo">
            <div class="title">
                <h1>Solicitud de Permiso</h1>
                <p>{{$data->tipo_novedad}}</p>
            </div>
        </div>

        <div class="right-text">
            <p>No. 27003</p>
            <p>Copia Interesado</p>
        </div>

        <div class="info">
            <p><strong>Código:</strong> {{$data->ci}}</p>
            <p><strong>Nombre:</strong> {{$data->nombre_completo}}</p>
            <p><strong>Cargo:</strong> {{$data->puesto}}</p>
            <p><strong>Repartición:</strong> {{$data->organizacion}}</p>
            <p><strong>Fecha Impresión:</strong> 27/11/2024</p>
        </div>

        <table class="details">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th>Fecha</th>
                   
                    <th>Hasta</th>
                   
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$data->descripcion}}</td>
                    <td>{{$data->desde}}</td>
                    
                    <td>{{$data->hasta}}</td>
                    
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <!-- Firma sin bordes -->
        <table class="signatures">
            <tbody>
                <tr>
                    <td>Solicitante</td>
                    <td>Firma Inmediato Superior</td>
                    <td>Firma del Director de Área</td>
                </tr>
            </tbody>
        </table>

        <p class="note">
            Nota: Si el funcionario sobrepasa el tiempo solicitado se considera como ABANDONO DE FUNCIONES.
        </p>

        <!-- Línea segmentada -->
        <div class="dashed-line"></div>

        <!-- Contenido duplicado abajo -->
        <div class="header">
            <img src="logo.png" alt="Logo">
            <div class="title">
                <h1>Solicitud de Permiso</h1>
                <p>{{$data->tipo_novedad}}</p>
            </div>
        </div>

        <div class="right-text">
            <p>No. 27003</p>
            <p>Copia Interesado</p>
        </div>

        <div class="info">
            <p><strong>Código:</strong> {{$data->ci}}</p>
            <p><strong>Nombre:</strong> {{$data->nombre_completo}}</p>
            <p><strong>Cargo:</strong> {{$data->puesto}}</p>
            <p><strong>Repartición:</strong> {{$data->organizacion}}</p>
            <p><strong>Fecha Impresión:</strong> {{$date}}</p>
        </div>

        <table class="details">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th>Fecha</th>
                    
                    <th>Hasta</th>
                    
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$data->descripcion}}</td>
                    <td>{{$data->desde}}</td>
                    <td>{{$data->hasta}}</td>
                    
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <br>
        <!-- Firma sin bordes duplicada -->
        <table class="signatures">
            <tbody>
                <tr>
                    <td>Solicitante</td>
                    <td>Firma Inmediato Superior</td>
                    <td>Firma del Director de Área</td>
                </tr>
            </tbody>
        </table>

        <p class="note">
            Nota: Si el funcionario sobrepasa el tiempo solicitado se considera como ABANDONO DE FUNCIONES.
        </p>
    </div>
</body>
</html>
