<?php
include("conexion.php");

// Leer JSON
$data = json_decode(file_get_contents("php://input"), true);

// Validar datos
if(
  !$data ||
  !isset($data["tabla"]) ||
  !isset($data["nombre"]) ||
  !isset($data["valor"]) ||
  !isset($data["fecha_registro"])
){
  echo json_encode(["error"=>"Datos incompletos"]);
  exit;
}

$tabla = $data["tabla"];
$nombre = $data["nombre"];
$valor = $data["valor"];
$fecha = $data["fecha_registro"];

// Query segura
$sql = "INSERT INTO $tabla (nombre, valor, fecha_registro) VALUES ($1, $2, $3)";
$res = pg_query_params($conn, $sql, [$nombre, $valor, $fecha]);

if($res){
  echo json_encode(["mensaje"=>"Insertado correctamente"]);
}else{
  echo json_encode(["error"=>"Error al insertar"]);
}
?>
