<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$tabla = $data["tabla"];
$id = intval($data["id"]);
$campo = $data["campo"];
$valor = $data["valor"];

$sql = "UPDATE $tabla SET $campo = $1 WHERE id = $2";
$res = pg_query_params($conn, $sql, [$valor, $id]);

if($res){
  echo json_encode(["mensaje"=>"Actualizado"]);
}else{
  echo json_encode(["error"=>"Error al actualizar"]);
}
?>
