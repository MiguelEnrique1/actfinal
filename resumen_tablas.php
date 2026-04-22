<?php
// Conexión a PostgreSQL
$conn = pg_connect("
  host=dpg-d7k2ck2qqhas73bs3mhg-a
  port=5432
  dbname=aranceles
  user=aranceles_user
  password=XRnzqfID3rcBppX3TuGPiRq75I4DOwLC
");

if(!$conn){
  die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

// Obtener tablas (PostgreSQL)
$res = pg_query($conn, "
  SELECT table_name 
  FROM information_schema.tables 
  WHERE table_schema = 'public'
");

$tablas = [];

while($row = pg_fetch_assoc($res)){
  $tablas[] = ["nombre" => $row['table_name']];
}

// Respuesta en JSON
echo json_encode([
  "total" => count($tablas),
  "tablas" => $tablas
]);
?>
