<?php
$conn = pg_connect("
  host=dpg-d7k2ck2qqhas73bs3mhg-a
  port=5432
  dbname=aranceles
  user=aranceles_user
  password=XRnzqfID3rcBppX3TuGPiRq75I4DOwLC
");

if(!$conn){
  die(json_encode(["error"=>"Error de conexión"]));
}

header('Content-Type: application/json');
?>
