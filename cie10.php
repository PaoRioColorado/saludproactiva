<?php
// Archivo: buscador.php

$cie10File = "C:/xampp/htdocs/saludproactiva/tabla-salud_cie10.csv";
$vademecumFile = "C:/xampp/htdocs/saludproactiva/VADEMECUM_sanidad.csv";

// Función para buscar CIE10 por palabra en síntomas
function buscarCIE10PorSintoma($sintoma, $cie10File){
    if (!file_exists($cie10File)) return [];
    $resultados = [];
    $sintoma = strtolower($sintoma);

    if (($handle = fopen($cie10File, "r")) !== false) {
        $header = fgetcsv($handle, 0, ","); // encabezado
        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            $sintomas = strtolower($row[2] ?? '');
            if(stripos($sintomas, $sintoma) !== false){
                $resultados[] = [
                    'codigo' => $row[0],
                    'titulo' => $row[1],
                    'sintomas' => $row[2]
                ];
            }
        }
        fclose($handle);
    }
    return $resultados;
}

// Función para buscar medicamentos por palabra clave
function buscarMedicamentos($palabra, $vademecumFile){
    if (!file_exists($vademecumFile)) return [];
    $resultados = [];
    $palabra = strtolower($palabra);
    $lineas = file($vademecumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach($lineas as $linea){
        if(stripos($linea, 'Nombre Comercial') !== false) continue;
        $partes = preg_split('/\s{2,}/', trim($linea));
        if(count($partes) < 5) continue;
        $nombre = $partes[0];
        $presentacion = $partes[1];
        $accion = $partes[2];
        $principio = $partes[3];
        $laboratorio = $partes[4];
        $texto = strtolower("$nombre $principio $accion $laboratorio");
        if(stripos($texto, $palabra) !== false){
            $resultados[] = [
                'laboratorio' => ucfirst($laboratorio),
                'medicamento' => "$nombre ($principio)",
                'presentacion' => $presentacion,
                'accion' => ucfirst($accion)
            ];
        }
    }
    $resultados = array_unique($resultados, SORT_REGULAR);
    return array_slice($resultados,0,50);
}

// Procesar input
$sintoma = $_GET['sintoma'] ?? '';
$principio = $_GET['principio'] ?? '';
$cieResults = [];
$medResults = [];

if($sintoma){
    $cieResults = buscarCIE10PorSintoma($sintoma, $cie10File);
}
if($principio){
    $medResults = buscarMedicamentos($principio, $vademecumFile);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Buscador | SaludProactiva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body, html { height:100%; margin:0; font-family:'Segoe UI', sans-serif; display:flex; flex-direction:column; background:#f2f4f7; }
.navbar { background-color:#212529; padding:0.2rem 0; }
.navbar-brand img { height:50px; }
.navbar .btn-menu { background-color:#212529; color:#fff; border:1px solid #212529; margin-right:0.3rem; padding:2px 8px; font-size:0.85rem; transition:.3s; }
.navbar .btn-menu:hover { background-color:#6c757d; color:#fff; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; margin-right:0.3rem; font-weight:500; font-size:0.85rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.85rem; }
.btn-salir:hover { background-color:#b02a37; }

.container { flex:1; margin-top:20px; }

.table-modern { border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);}
.table-modern thead { background-color:#e9ecef; }
.table-modern tbody tr:nth-child(odd) { background-color:#f9f9f9; }
.table-modern tbody tr:hover { background-color:#f1f5f9; }
.table-modern th, .table-modern td { vertical-align:middle; }

form { max-width:700px; margin:auto; background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); position:relative; }
.autocomplete-list { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #ccc; border-radius:0 0 6px 6px; max-height:150px; overflow-y:auto; z-index:1000; display:none; }
.autocomplete-list div { padding:5px 10px; cursor:pointer; }
.autocomplete-list div:hover { background:#f1f1f1; }

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
        <li class="nav-item"><span class="navbar-text">👨‍⚕️ Médico</span></li>
        <li class="nav-item"><a class="btn btn-salir btn-sm ms-2" href="logout.php">Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="text-center mb-3">🔍 Buscador de Síntomas y Principio Activo</h2>
    <form method="GET" id="buscadorForm" autocomplete="off">
        <div class="row g-2 mb-2 position-relative">
            <div class="col-md-6">
                <input type="text" class="form-control" name="sintoma" placeholder="Ingrese síntoma" value="<?= htmlspecialchars($sintoma) ?>" id="sintomaInput">
                <div class="autocomplete-list" id="sintomasList"></div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="principio" placeholder="Principio activo" value="<?= htmlspecialchars($principio) ?>" id="principioInput">
                <div class="autocomplete-list" id="principiosList"></div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
        <button type="button" class="btn btn-secondary" onclick="window.location='<?= $_SERVER['PHP_SELF'] ?>'">Limpiar</button>
    </form>

    <?php if($sintoma && $cieResults): ?>
        <?php foreach($cieResults as $cie): ?>
            <div class="mt-4 p-3 border rounded">
                <p><b>Código:</b> <?= $cie['codigo'] ?></p>
                <p><b>Título:</b> <?= $cie['titulo'] ?></p>
                <p><b>Síntomas asociados:</b> <?= $cie['sintomas'] ?></p>
            </div>
        <?php endforeach; ?>
    <?php elseif($sintoma): ?>
        <div class="alert alert-info mt-3">No se encontraron resultados para este síntoma.</div>
    <?php endif; ?>

    <?php if($principio && $medResults): ?>
        <div class="mt-4">
            <h4>💊 Medicamentos sugeridos</h4>
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Laboratorio</th>
                        <th>Medicamento</th>
                        <th>Presentación</th>
                        <th>Acción farmacológica</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($medResults as $m): ?>
                        <tr>
                            <td><?= $m['laboratorio'] ?></td>
                            <td><?= $m['medicamento'] ?></td>
                            <td><?= $m['presentacion'] ?></td>
                            <td><?= $m['accion'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif($principio): ?>
        <div class="alert alert-info mt-3">No se encontraron medicamentos para este principio activo.</div>
    <?php endif; ?>
</div>

<footer style="background-color:#212529; color:#fff; text-align:center; padding:12px 0;">
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com" style="color:#0d6efd; font-weight:bold;">Desarrollado por Paola DF</a></small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Función genérica de autocompletado moderno
function setupAutocomplete(inputId, listId, apiUrl){
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);

    input.addEventListener('input', async function(){
        const val = this.value.trim();
        list.innerHTML = '';
        if(val.length < 1){
            list.style.display = 'none';
            return;
        }
        try {
            const resp = await fetch(apiUrl+'?q='+encodeURIComponent(val));
            const data = await resp.json();
            if(data.length === 0){ list.style.display = 'none'; return; }
            data.forEach(item=>{
                const div = document.createElement('div');
                div.textContent = item;
                div.addEventListener('click', ()=>{ input.value = item; list.style.display='none'; });
                list.appendChild(div);
            });
            list.style.display = 'block';
        } catch(e){ console.error(e); }
    });

    // Cerrar lista al hacer clic fuera
    document.addEventListener('click', (e)=>{
        if(!input.contains(e.target) && !list.contains(e.target)){
            list.style.display='none';
        }
    });
}

setupAutocomplete('sintomaInput','sintomasList','autocomplete_sintomas.php');
setupAutocomplete('principioInput','principiosList','autocomplete_principio.php');
</script>
</body>
</html>






