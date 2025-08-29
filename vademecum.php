<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vademécum Alfa Beta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        .back-button {
            position: absolute;
            left: 15px;
            top: 15px;
            background-color: white;
            color: #007BFF;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }
        iframe {
            width: 100%;
            height: 90vh; /* ocupa casi toda la pantalla */
            border: none;
        }
    </style>
</head>
<body>
    <header>
        <a href="dashboard.php" class="back-button">← Regresar al Menú</a>
        <h1>Vademécum Alfa Beta</h1>
        <p>Acceso gratuito al vademécum</p>
    </header>

    <!-- iframe del vademécum -->
    <iframe src="https://www.alfabeta.net/medicamento/index-ar.jsp"></iframe>
</body>
</html>
