<?php
session_start();

// Conexión a PostgreSQL
$conn = pg_connect("
  host=dpg-d7k2ck2qqhas73bs3mhg-a
  port=5432
  dbname=aranceles
  user=aranceles_user
  password=XRnzqfID3rcBppX3TuGPiRq75I4DOwLC
");

if(!$conn){
  die("Error de conexión a la base de datos");
}

// Obtener tablas existentes
$res = pg_query($conn, "
  SELECT table_name 
  FROM information_schema.tables 
  WHERE table_schema = 'public'
");

$tablas = [];
while($row = pg_fetch_assoc($res)){
  $tablas[] = $row['table_name'];
}
$total = count($tablas);

$mensaje = "";
$nuevaTabla = "";

// Crear nueva tabla
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["crearTabla"])){
  if(isset($_POST["nuevaTabla"]) && $_POST["nuevaTabla"]!=""){
    $nuevaTabla = trim($_POST["nuevaTabla"]);

    $sql = "
      CREATE TABLE $nuevaTabla (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(100),
        valor NUMERIC(10,2),
        fecha_registro TIMESTAMP
      )
    ";

    if(pg_query($conn, $sql)){
      $mensaje = "Tabla '$nuevaTabla' creada correctamente.";
      $tablas[] = $nuevaTabla;
      $total++;
    } else {
      $mensaje = "Error al crear tabla.";
    }
  }
}

// Insertar objeto
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["insertarObjeto"])){
  $tabla = $_POST["tabla"];
  $nombre = $_POST["nombre"];
  $valor = $_POST["valor"];
  $fecha = $_POST["fecha_registro"];

  $sql = "INSERT INTO $tabla (nombre, valor, fecha_registro) VALUES ($1, $2, $3)";
  $result = pg_query_params($conn, $sql, [$nombre, $valor, $fecha]);

  if($result){
    $mensaje = "Objeto añadido correctamente en '$tabla'.";
  } else {
    $mensaje = "Error al insertar datos.";
  }
}

// Imágenes
$imagenes = glob("img/*.{jpg,png,jpeg}", GLOB_BRACE);
if(!isset($_SESSION["imagenes_tablas"])){
  $_SESSION["imagenes_tablas"] = [];
}

foreach($tablas as $t){
  if(!isset($_SESSION["imagenes_tablas"][$t])){
    shuffle($imagenes);
    $_SESSION["imagenes_tablas"][$t] = $imagenes[0];
  }
}

$imagenes_tablas = $_SESSION["imagenes_tablas"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Añadir Tabla</title>
<link rel="stylesheet" href="style.css">
<script>
function toggleCampo(){
  const check = document.getElementById("checkNueva");
  const campo = document.getElementById("campoNombre");
  campo.style.display = check.checked ? "block" : "none";
}
function toggleIngresar(){
  const check = document.getElementById("checkIngresar");
  const campo = document.getElementById("campoIngresar");
  campo.style.display = check.checked ? "block" : "none";
}
</script>
</head>
<body>

<header>
  <div class="top-bar">
    <div class="left">
      <button onclick="window.location.href='index.php'">Volver</button>
    </div>
    <div class="right">
      <button onclick="window.location.href='consultar.php'">Consultar</button>
    </div>
  </div>

  <div class="hero">
    <h1>Actualmente hay <?php echo $total; ?> tabla(s)</h1>

    <div class="imagenes-tablas">
      <?php 
      $contador = 1;
      foreach($tablas as $t){ 
        $img = $imagenes_tablas[$t];
        echo "<div class='item'>";
        echo "<img src='$img'>";
        echo "<p>Tabla $contador: $t</p>";
        echo "</div>";
        $contador++;
      } 
      ?>
    </div>
  </div>
</header>

<div class="cuadro">
  <h2>Gestión de Tablas</h2>
  <?php if($mensaje) echo "<p style='color:green'>$mensaje</p>"; ?>

  <!-- Crear tabla -->
  <form method="POST">
    <input type="hidden" name="crearTabla" value="1">
    <label>
      <input type="checkbox" id="checkNueva" onclick="toggleCampo()"> Crear nueva tabla
    </label>
    <div id="campoNombre" style="display:none;">
      <input type="text" name="nuevaTabla" placeholder="Nombre de la tabla">
    </div>
    <br>
    <button type="submit">Crear</button>
  </form>

  <!-- Insertar datos -->
  <form method="POST">
    <input type="hidden" name="insertarObjeto" value="1">
    <label>
      <input type="checkbox" id="checkIngresar" onclick="toggleIngresar()"> Insertar datos
    </label>

    <div id="campoIngresar" style="display:none;">
      <label>Tabla:</label><br>
      <select name="tabla" required>
        <option value="">-- Selecciona --</option>
        <?php foreach($tablas as $t){ echo "<option value='$t'>$t</option>"; } ?>
      </select><br><br>

      <label>Nombre:</label><br>
      <input type="text" name="nombre" required><br><br>

      <label>Valor:</label><br>
      <input type="number" step="0.01" name="valor" required><br><br>

      <label>Fecha:</label><br>
      <input type="datetime-local" name="fecha_registro" required><br><br>

      <button type="submit">Guardar</button>
    </div>
  </form>
</div>

</body>
</html>
