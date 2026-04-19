<?php
// Datos de conexión
$host = "localhost";
$user = "root";       // tu usuario de MySQL
$password = "";       // tu contraseña de MySQL
$dbname = "aranceles";

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// ✅ No pongas echo aquí
// Si la conexión es correcta, simplemente no imprimas nada
?>
