<!DOCTYPE html>
<html>
<head>
    <title>Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            <p>UNIDAD DE SISTEMAS E INFORMATICA</p>
        </div>
        <div class="right">
            <div class="circle">16</div>
        </div>
    </div>
</body>
</html>
