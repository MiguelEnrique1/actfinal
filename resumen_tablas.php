<?php
// Conexión a la BD
$conn = new mysqli("localhost","root","","aranceles");

// Obtener tablas
$res = $conn->query("SHOW TABLES");
$tablas = [];
while($row = $res->fetch_array()){
  $tablas[] = ["nombre"=>$row[0]];
}

echo json_encode([
  "total"=>count($tablas),
  "tablas"=>$tablas
]);
?>
