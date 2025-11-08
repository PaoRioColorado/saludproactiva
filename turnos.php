<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['medico_id'])) {
    header("Location: login.php");
    exit;
}

$medico_id = $_SESSION['medico_id'];
$medico_nombre = $_SESSION['medico_nombre'] ?? null;
$action = $_GET['action'] ?? '';
$turno_id = $_GET['id'] ?? null;

// ELIMINAR TURNO
if(isset($_GET['delete_id'])){
    $delete_id = (int)$_GET['delete_id'];
    $stmt_del = $conn->prepare("DELETE FROM turnos WHERE id=? AND medico_id=?");
    $stmt_del->bind_param("ii", $delete_id, $medico_id);
    $stmt_del->execute();
    header("Location: turnos.php");
    exit;
}

// AGREGAR O EDITAR TURNO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $datetime = $fecha . ' ' . $hora;

    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM turnos WHERE fecha=? AND medico_id=? AND id <> ?");
    $check_id = $_POST['turno_id'] ?? 0;
    $stmt_check->bind_param("sii", $datetime, $medico_id, $check_id);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if($count > 0){
        $error = "Ya existe un turno agendado en ese horario.";
    } else {
        if (!empty($_POST['turno_id'])) {
            $stmt = $conn->prepare("UPDATE turnos SET paciente_id=?, fecha=?, motivo=?, estado=? WHERE id=? AND medico_id=?");
            $stmt->bind_param("isssii", $paciente_id, $datetime, $motivo, $estado, $_POST['turno_id'], $medico_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO turnos (paciente_id, medico_id, fecha, motivo, estado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $paciente_id, $medico_id, $datetime, $motivo, $estado);
            $stmt->execute();
        }
        header("Location: turnos.php");
        exit;
    }
}

// LISTA DE TURNOS
$sql = "SELECT t.*, p.nombre, p.apellido, p.dni, p.email 
        FROM turnos t
        JOIN pacientes p ON p.id = t.paciente_id
        WHERE t.medico_id = ?
        ORDER BY t.fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $medico_id);
$stmt->execute();
$result = $stmt->get_result();
$turnos = $result->fetch_all(MYSQLI_ASSOC);

// PACIENTES
$sql_p = "SELECT id, nombre, apellido FROM pacientes ORDER BY apellido, nombre";
$res_p = $conn->query($sql_p);
$pacientes = $res_p->fetch_all(MYSQLI_ASSOC);

// TURNO A EDITAR
$editar_turno = null;
if ($action === 'editar' && $turno_id) {
    $stmt = $conn->prepare("SELECT * FROM turnos WHERE id=? AND medico_id=?");
    $stmt->bind_param("ii", $turno_id, $medico_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $editar_turno = $res->fetch_assoc();
}

// Array meses en español
$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Turnos | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html { height: 100%; margin:0; font-family: 'Segoe UI', sans-serif; display:flex; flex-direction:column; }
.navbar { background-color: #212529; padding-top: 0.2rem; padding-bottom: 0.2rem; }
.navbar-brand img { height: 50px; width: auto; }
.navbar .btn-menu { background-color: #212529; color: #fff; border: 1px solid #212529; border-radius: 5px; margin-right: 0.3rem; padding: 2px 8px; font-size: 0.85rem; transition: 0.3s; }
.navbar .btn-menu:hover { background-color: #6c757d; color: #fff; }
.navbar-text { color: #fff; background-color: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 5px; margin-right: 0.3rem; font-weight: 500; font-size: 0.85rem;}
.btn-salir { background-color: #dc3545; color: #fff; border: none; padding: 2px 10px; font-size: 0.85rem; }
.btn-salir:hover { background-color: #b02a37; }

/* Contenedor principal para que el footer quede abajo */
.container { flex: 1; margin-top:20px; }

/* Tablas modernas */
.table-modern { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
.table-modern thead { background-color: #e9ecef; }
.table-modern tbody tr:nth-child(odd) { background-color: #f9f9f9; }
.table-modern tbody tr:nth-child(even) { background-color: #ffffff; }
.table-modern tbody tr:hover { background-color: #f1f5f9; box-shadow: inset 0 0 5px rgba(0,0,0,0.05); transition: 0.2s; }
.table-modern th, .table-modern td { vertical-align: middle; }

/* Formulario */
.form-agenda { background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width:650px; margin:auto; }

/* Imagen estetoscopio */
#imagen-estetoscopio { text-align:center; margin-top:20px; margin-bottom:20px; }
#imagen-estetoscopio img { max-height:150px; opacity:0.3; filter: grayscale(50%); }

/* Footer */
footer { background-color:#212529; color:#fff; text-align:center; padding:12px 0; position: relative; bottom: 0; width: 100%; }
footer a { color:#0d6efd; text-decoration: none; font-weight: bold; }
footer a:hover { text-decoration: underline; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
        <img src="icons/logo.png" alt="Logo SaludProactiva">
    </a>
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if ($medico_nombre): ?>
          <li class="nav-item">
              <span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span>
          </li>
          <li class="nav-item">
              <a class="btn btn-salir btn-sm ms-2" href="logout.php">Salir</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <!-- FORMULARIO -->
    <h4 class="mb-2 text-center"><?= $editar_turno ? 'Editar Turno' : 'Nuevo Turno' ?></h4>
    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST" class="form-agenda row g-3" style="margin-top:10px;">
        <input type="hidden" name="turno_id" value="<?= $editar_turno['id'] ?? '' ?>">

        <div class="col-md-6">
            <label>Paciente</label>
            <select name="paciente_id" class="form-select" required>
                <option value="">Seleccione un paciente</option>
                <?php foreach($pacientes as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($editar_turno && $editar_turno['paciente_id']==$p['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($p['apellido'].' '.$p['nombre']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="nuevo">+ Agregar nuevo paciente</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" 
                value="<?= $editar_turno ? date('Y-m-d', strtotime($editar_turno['fecha'])) : '' ?>" required>
        </div>

        <div class="col-md-3">
            <label>Hora</label>
            <select name="hora" class="form-select" required>
                <?php
                $start = new DateTime('08:00');
                $end = new DateTime('17:00');
                $interval = new DateInterval('PT15M');
                $times = new DatePeriod($start, $interval, $end->add($interval));
                foreach ($times as $time) {
                    $hora_val = $time->format('H:i');
                    $selected = ($editar_turno && date('H:i', strtotime($editar_turno['fecha'])) == $hora_val) ? 'selected' : '';
                    echo "<option value='$hora_val' $selected>$hora_val</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Motivo</label>
            <select name="motivo" class="form-select" required>
                <?php
                $motivos = ['Consulta general','Control','Resultado laboratorio','Ecografía','Otros'];
                foreach($motivos as $m){
                    $sel = ($editar_turno && $editar_turno['motivo']==$m)?'selected':'' ;
                    echo "<option value='$m' $sel>$m</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <?php
                $estados = ['pendiente','confirmado','cancelado'];
                foreach($estados as $e){
                    $sel = ($editar_turno && $editar_turno['estado']==$e)?'selected':'' ;
                    echo "<option value='$e' $sel>".ucfirst($e)."</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-agenda"><?= $editar_turno ? 'Actualizar' : 'Agendar' ?></button>
        </div>
    </form>

    <!-- IMAGEN ESTETOSCOPIO -->
    <div id="imagen-estetoscopio">
        <img src="icons/estetoscopio.png" alt="Estetoscopio">
    </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fechaInput = document.querySelector('input[name="fecha"]');
fechaInput.addEventListener('input', function(){
    const dia = new Date(this.value).getDay();
    if(dia===0 || dia===6){
        alert("No se puede seleccionar sábado ni domingo.");
        this.value = '';
    }
});

document.querySelector('select[name="paciente_id"]').addEventListener('change', function(){
    if(this.value === 'nuevo'){
        window.location.href = 'pacientes.php?action=nuevo';
    }
});
</script>
</body>
</html>


