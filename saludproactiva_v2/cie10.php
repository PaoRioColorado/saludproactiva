<?php
session_start();
$medico_nombre = $_SESSION['medico_nombre'] ?? null;

// Archivos CSV
$cie_file = __DIR__ . "/tabla-salud_cie10.csv";
$med_file = __DIR__ . "/vademecum 2018.csv";

// Cargar CIE-10
$cie_data = [];
if (($handle = fopen($cie_file, "r")) !== false) {
    $header = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $cie_data[] = [
            'codigo' => $row[0],
            'titulo' => $row[1],
            'sintomas' => isset($row[2]) ? explode(',', $row[2]) : []
        ];
    }
    fclose($handle);
}

// Cargar medicamentos
$med_data = [];
if (($handle = fopen($med_file, "r")) !== false) {
    $header = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $med_data[] = [
            'nombre' => $row[0],
            'principio' => $row[1],
            'presentacion' => $row[2],
            'posologia' => $row[3] ?? '',
            'aprobado' => $row[4] ?? ''
        ];
    }
    fclose($handle);
}

// Lista de síntomas únicos
$sintomas_set = [];
foreach ($cie_data as $cie) {
    foreach ($cie['sintomas'] as $s) {
        $s = trim($s);
        if ($s) $sintomas_set[$s] = true;
    }
}
$sintomas_list = array_keys($sintomas_set);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Buscador de Síntomas y CIE-10 | SaludProactiva</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
html, body { height:100%; margin:0; }
body { display:flex; flex-direction:column; font-family:sans-serif; }

/* NAVBAR */
.navbar { background-color: #212529; padding: 0.2rem 1rem; }
.navbar-brand img { height:50px; width:auto; }
.navbar .btn-menu { background-color:#212529; color:#fff; border-radius:5px; margin-right:0.5rem; padding:2px 10px; font-size:0.9rem;}
.navbar .btn-menu:hover { background-color:#6c757d; }
.navbar-text { color:#fff; background-color: rgba(255,255,255,0.1); padding:2px 6px; border-radius:5px; font-size:0.9rem;}
.btn-salir { background-color:#dc3545; color:#fff; border:none; padding:2px 10px; font-size:0.9rem;}
.btn-salir:hover { background-color:#b02a37; }

/* CONTENIDO */
.container-main { flex:1; display:flex; gap:20px; margin:20px; align-items:flex-start; }
.cie-img img { width:150px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
.resultados { flex:1; }

h1.page-title { margin-bottom:1rem; font-weight:600; color:#212529; }
#resultadoCIE .card, #resultadoMed .card { margin-bottom:10px; }

.autocomplete-suggestions {
    border:1px solid #ced4da;
    max-height:150px;
    overflow-y:auto;
    position:absolute;
    background:#fff;
    z-index:1000;
}
.autocomplete-suggestion {
    padding:5px 10px;
    cursor:pointer;
}
.autocomplete-suggestion:hover { background:#e9ecef; }

footer { background:#212529; color:#fff; text-align:center; padding:12px 0; }
footer a { color:#0d6efd; text-decoration:none; font-weight:bold; }
footer a:hover { text-decoration:underline; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="icons/logo.png" alt="Logo SaludProactiva"></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="btn btn-menu" href="dashboard.php">Inicio</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="buscar_paciente.php">Pacientes</a></li>
        <li class="nav-item"><a class="btn btn-menu" href="turnos.php">Turnos</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if ($medico_nombre): ?>
          <li class="nav-item"><span class="navbar-text">👨‍⚕️ <?= htmlspecialchars($medico_nombre) ?></span></li>
          <li class="nav-item"><a class="btn btn-salir btn-sm" href="logout.php">Salir</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-menu btn-sm" href="login.php">Ingresar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container-main">
  <div class="cie-img">
    <img src="icons/cie10_img.png" alt="CIE-10">
  </div>

  <div class="resultados">
    <h1 class="page-title">Buscador de síntomas y CIE-10</h1>
    <input type="text" id="sintomaInput" class="form-control mb-3" placeholder="Ingrese síntoma..." autocomplete="off">
    <div id="suggestions" class="autocomplete-suggestions"></div>
    <div id="resultadoCIE"></div>
    <div id="resultadoMed"></div>
  </div>
</div>

<footer>
  <small>© <?= date('Y') ?> SaludProactiva | <a href="mailto:paoladf.it@gmail.com">Desarrollado por Paola DF</a></small>
</footer>

<script>
const cieData = <?= json_encode($cie_data) ?>;
const medData = <?= json_encode($med_data) ?>;
const sintomasList = <?= json_encode($sintomas_list) ?>;

const input = document.getElementById('sintomaInput');
const resultadoCIE = document.getElementById('resultadoCIE');
const resultadoMed = document.getElementById('resultadoMed');
const suggestionsDiv = document.getElementById('suggestions');

function clearResults() {
    resultadoCIE.innerHTML = '';
    resultadoMed.innerHTML = '';
}

// Autocompletado
input.addEventListener('input', function() {
    const query = input.value.toLowerCase();
    suggestionsDiv.innerHTML = '';
    clearResults();

    if (!query) return;

    const matches = sintomasList.filter(s => s.toLowerCase().includes(query)).slice(0, 10);
    matches.forEach(s => {
        const div = document.createElement('div');
        div.className = 'autocomplete-suggestion';
        div.textContent = s;
        div.addEventListener('click', () => {
            input.value = s;
            suggestionsDiv.innerHTML = '';
            searchSintoma(s);
        });
        suggestionsDiv.appendChild(div);
    });
});

document.addEventListener('click', e => {
    if (e.target !== input) suggestionsDiv.innerHTML = '';
});

function searchSintoma(query) {
    clearResults();
    const matches = cieData.filter(cie => cie.sintomas.some(s => s.toLowerCase() === query.toLowerCase()));

    if (matches.length === 0) {
        resultadoCIE.innerHTML = "<p class='text-danger'>No se encontró CIE-10 para este síntoma.</p>";
        return;
    }

    matches.forEach(cie => {
        resultadoCIE.innerHTML += `<div class="card">
            <div class="card-body">
                <strong>Código:</strong> ${cie.codigo}<br>
                <strong>Título:</strong> ${cie.titulo}<br>
                <strong>Síntomas asociados:</strong> ${cie.sintomas.join(', ')}
            </div>
        </div>`;

        const medsSet = new Set();
        medData.forEach(m => {
            if (cie.titulo.toLowerCase().includes(m.nombre.toLowerCase()) || cie.titulo.toLowerCase().includes(m.principio.toLowerCase())) {
                medsSet.add(JSON.stringify(m));
            }
        });

        if (medsSet.size > 0) {
            let medHTML = '<ul>';
            medsSet.forEach(mStr => {
                const m = JSON.parse(mStr);
                medHTML += `<li><strong>${m.nombre}</strong> (${m.principio}), ${m.presentacion}, Posología: ${m.posologia}</li>`;
            });
            medHTML += '</ul>';
            resultadoMed.innerHTML += `<div class="card"><div class="card-body"><strong>Medicamentos sugeridos:</strong>${medHTML}</div></div>`;
        }
    });
}
</script>

</body>
</html>









