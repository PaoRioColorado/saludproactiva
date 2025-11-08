<?php
require 'config.php'; // tu API key

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['enfermedades'])) {
    $enfermedades = $_POST['enfermedades'];

    $prompt = "Genera una historia clínica resumida para un paciente que tiene las siguientes enfermedades: $enfermedades. 
Incluye cada cuánto deberían realizarse controles médicos o estudios preventivos y recomendaciones de seguimiento. 
Responde de manera clara y resumida para que un médico pueda usarlo.";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ]);

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role"=>"user", "content"=>$prompt],
        ],
        "temperature" => 0.7,
        "max_tokens" => 500
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Error al conectarse a la IA: " . curl_error($ch);
        exit;
    }
    curl_close($ch);

    $response = json_decode($result, true);
    if (isset($response['choices'][0]['message']['content'])) {
        echo $response['choices'][0]['message']['content'];
    } else {
        echo "No se obtuvo respuesta de la IA.";
    }
}
?>
