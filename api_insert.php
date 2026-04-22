<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$tabla = $data["tabla"];
$nombre = $data["nombre"];
$valor = $data["valor"];
$fecha = $data["fecha_registro"];

$sql = "INSERT INTO $tabla (nombre, valor, fecha_registro) VALUES ($1,$2,$3)";
$res = pg_query_params($conn, $sql, [$nombre,$valor,$fecha]);

if($res){
  echo json_encode(["mensaje"=>"Insertado correctamente"]);
}else{
  echo json_encode(["error"=>"Error al insertar"]);
}
?>
