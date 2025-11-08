<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? 'Dr. SaludProactiva';

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "saludproactiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener ID del paciente
$paciente_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($paciente_id <= 0) {
    die("ID de paciente no válido.");
}

// Buscar datos del paciente
$paciente = $conexion->query("SELECT * FROM pacientes WHERE id = $paciente_id")->fetch_assoc();
if (!$paciente) die("Paciente no encontrado.");

// Buscar el último estudio del paciente
$sql = "SELECT * FROM estudios WHERE paciente_id = $paciente_id ORDER BY fecha DESC LIMIT 1";
$result = $conexion->query($sql);
$estudio = $result->fetch_assoc();

// Simular si el paciente ya pidió turno (esto puede venir luego de una tabla 'turnos')
$turno_solicitado = rand(0,1); // 🔹 reemplazar luego por consulta real
$fecha_turno = $turno_solicitado ? date('Y-m-d', strtotime('-10 days')) : null; // ejemplo de hace 10 días
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Controles de <?= htmlspecialchars($paciente['nombre'] ?? '') ?> | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem;}
.navbar .btn-menu:hover { background-color: #6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; font-size:0.9rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }
.container-main { padding-top:20px; padding-bottom:20px; flex:1; }
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; margin-top:20px; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
body { display:flex; flex-direction:column; min-height:100vh; }
.btn-recordatorio { background-color:#0d6efd; color:white; border:none; padding:6px 12px; border-radius:5px; cursor:pointer;}
.btn-recordatorio:hover { background-color:#0b5ed7; }
.alert-info { font-size:0.95rem; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo"></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="vademecum.php">Vademécum</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container container-main">
<h3 class="mb-3">Controles de <?= htmlspecialchars($paciente['nombre']) ?></h3>
<p><strong>DNI:</strong> <?= htmlspecialchars($paciente['dni']) ?><br>
<strong>Edad:</strong> <?= htmlspecialchars($paciente['edad']) ?><br>
<strong>Sexo:</strong> <?= htmlspecialchars($paciente['sexo']) ?><br>
<strong>Obra Social:</strong> <?= htmlspecialchars($paciente['obra_social']) ?></p>

<?php if ($estudio): ?>
<table class="table table-bordered table-striped align-middle">
<thead class="table-dark">
<tr>
  <th>Tipo de Estudio</th>
  <th>Fecha</th>
  <th>Próximo Control</th>
  <th>Frecuencia (meses)</th>
  <th>Notas</th>
  <th>Turno solicitado</th>
  <th>Acción</th>
</tr>
</thead>
<tbody>
<tr>
  <td><?= htmlspecialchars($estudio['tipo']) ?></td>
  <td><?= htmlspecialchars($estudio['fecha']) ?></td>
  <td><?= htmlspecialchars($estudio['proximo_control']) ?></td>
  <td><?= htmlspecialchars($estudio['frecuencia']) ?></td>
  <td><?= nl2br(htmlspecialchars($estudio['notas'])) ?></td>
  <td>
    <?php if ($fecha_turno): ?>
        <span class="badge bg-success">✔ <?= htmlspecialchars($fecha_turno) ?></span>
    <?php else: ?>
        <span class="badge bg-secondary">Pendiente</span>
    <?php endif; ?>
  </td>
  <td>
    <?php if (!$fecha_turno): ?>
      <button type="button" class="btn-recordatorio" onclick="enviarRecordatorio()">📩 Enviar recordatorio</button>
    <?php else: ?>
      <span class="text-muted">—</span>
    <?php endif; ?>
  </td>
</tr>
</tbody>
</table>
<?php else: ?>
<div class="alert alert-info">No hay estudios registrados para este paciente.</div>
<?php endif; ?>

<a href="buscar_paciente.php" class="btn btn-secondary mt-3">⬅ Volver a la búsqueda</a>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
function enviarRecordatorio() {
    alert("📩 Recordatorio enviado al paciente para solicitar turno para su próximo control.");
}
</script>

</body>
</html>
<?php
$conexion->close();
?>





