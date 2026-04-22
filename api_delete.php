<?php
include("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$tabla = $data["tabla"];
$id = intval($data["id"]);

$res = pg_query_params($conn, "DELETE FROM $tabla WHERE id=$1", [$id]);

if($res){
  echo json_encode(["mensaje"=>"Eliminado"]);
}else{
  echo json_encode(["error"=>"Error al eliminar"]);
}
?>
