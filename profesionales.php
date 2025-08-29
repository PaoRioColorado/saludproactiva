<?php
// Array de profesionales
$profesionales = [
    ['nombre' => 'Guillermo Albizua', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000001'],
    ['nombre' => 'Guadalupe Alduvino Vacas', 'especialidad' => 'Ginecología y Obstetricia', 'telefono' => '2914000002'],
    ['nombre' => 'Luciano Caraballo', 'especialidad' => 'Ginecología y Uroginecología', 'telefono' => '2914000003'],
    ['nombre' => 'Victoria B. Gimenez', 'especialidad' => 'Psicología - Adolescentes y Adultos', 'telefono' => '2914000004'],
    ['nombre' => 'Osvaldo Giorgetti', 'especialidad' => 'Especialista Consultor en Cirugía', 'telefono' => '2914000005'],
    ['nombre' => 'María Laura Iturburu', 'especialidad' => 'Nutricionista', 'telefono' => '2914000006'],
    ['nombre' => 'Ricardo Javier Lucero', 'especialidad' => 'Especialista en Columna Adultos y Niños', 'telefono' => '2914000007'],
    ['nombre' => 'Juan Pedro Molina', 'especialidad' => 'Endocrinología y Diabetes', 'telefono' => '2914000008'],
    ['nombre' => 'Carlos Moyano', 'especialidad' => 'Especialista en Columna Adultos y Niños', 'telefono' => '2914000009'],
    ['nombre' => 'Juan Pedro Pesci', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000010'],
    ['nombre' => 'María Rosa Romero', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000011'],
    ['nombre' => 'Matías Sabbatini', 'especialidad' => 'Cirugía Gral. Adultos', 'telefono' => '2914000012'],
    ['nombre' => 'Valeria Salsi', 'especialidad' => 'Nutricionista', 'telefono' => '2914000013'],
    ['nombre' => 'Oliver Schamis', 'especialidad' => 'Ortopedia y Traumatología', 'telefono' => '2914000014'],
    ['nombre' => 'María Paula Sofio', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000015'],
    ['nombre' => 'Mauricio Traversaro', 'especialidad' => 'Cardiología - ECG - Riesgo Quirúrgico', 'telefono' => '2914000016'],
    ['nombre' => 'Gisela Urriaga', 'especialidad' => 'Medicina Gral. y Familiar', 'telefono' => '2914000017'],
    ['nombre' => 'Sonia Alejandra Vazquez', 'especialidad' => 'Clínica Médica', 'telefono' => '2914000018'],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Profesionales de la Salud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script>
function mostrarTelefono(numero) {
    alert("Teléfono de contacto: " + numero);
}
</script>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3">Profesionales de la Salud</h3>
    <table class="table table-bordered table-striped bg-white">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Contacto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($profesionales as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['especialidad']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="mostrarTelefono('<?= $p['telefono'] ?>')">
                        Contactar
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button class="btn btn-secondary mt-3" onclick="window.location.href='dashboard.php'">Volver al menú</button>
</div>
</body>
</html>
