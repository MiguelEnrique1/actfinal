<?php
$conn = pg_connect("
  host=dpg-d7k2ck2qqhas73bs3mhg-a
  port=5432
  dbname=aranceles
  user=aranceles_user
  password=TU_PASSWORD_NUEVA
");

if(!$conn){
  die(json_encode(["error"=>"Error de conexión"]));
}

header('Content-Type: application/json');
?>
