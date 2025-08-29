<?php
require 'config.php'; // tu archivo con la API key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $enfermedades = $_POST['enfermedades'] ?? [];

    // Convertimos el arreglo de enfermedades en texto
    $enfermedades_texto = implode(', ', $enfermedades);

    $prompt = "Genera una historia clínica resumida para un paciente:
Nombre: $nombre $apellido
DNI: $dni
Enfermedades: $enfermedades_texto
Incluye recomendaciones de controles, frecuencia y observaciones breves.";

    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 300
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $historia = $result['choices'][0]['message']['content'] ?? 'No se pudo generar la historia clínica.';

    echo $historia;
}
?>
