<?php
header("Content-Type: application/json; charset=utf-8");
include("conection.php"); // importante: nombre correcto

$sql = "SELECT id, nombre, valor FROM aranceles";
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
