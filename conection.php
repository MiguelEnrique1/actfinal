<?php


// Crear conexión
$conn = new mysqli("HOST","USER","PASSWORD","DB");

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// ✅ No pongas echo aquí
// Si la conexión es correcta, simplemente no imprimas nada
?>
