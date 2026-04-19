<?php
session_start();

// Conexión a la BD
$conn = new mysqli("localhost","root","","aranceles");
if($conn->connect_error){
  die("Error de conexión: " . $conn->connect_error);
}

// Obtener tablas existentes
$res = $conn->query("SHOW TABLES");
$tablas = [];
while($row = $res->fetch_array()){
  $tablas[] = $row[0];
}
$total = count($tablas);

$mensaje = "";
$nuevaTabla = "";

// Crear nueva tabla
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["crearTabla"])){
  if(isset($_POST["nuevaTabla"]) && $_POST["nuevaTabla"]!=""){
    $nuevaTabla = trim($_POST["nuevaTabla"]);
    $sql = "CREATE TABLE `$nuevaTabla` (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nombre VARCHAR(100),
      valor DECIMAL(10,2),
      fecha_registro DATETIME
    )";
    if($conn->query($sql)){
      $mensaje = "Tabla '$nuevaTabla' creada correctamente.";
      $tablas[] = $nuevaTabla;
      $total++;
    } else {
      $mensaje = "Error al crear tabla: " . $conn->error;
    }
  }
}

// Insertar objeto en tabla seleccionada
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["insertarObjeto"])){
  $tabla = $_POST["tabla"];
  $nombre = $_POST["nombre"];
  $valor = $_POST["valor"];
  $fecha = $_POST["fecha_registro"];

  $stmt = $conn->prepare("INSERT INTO `$tabla` (nombre, valor, fecha_registro) VALUES (?,?,?)");
  $stmt->bind_param("sds",$nombre,$valor,$fecha);
  if($stmt->execute()){
    $mensaje = "Objeto añadido correctamente en '$tabla'.";
  } else {
    $mensaje = "Error al insertar: " . $conn->error;
  }
}

// Asignar imágenes persistentes a las tablas
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
    <h1>Actualmente hay <?php echo $total; ?> tabla(s) en la base de datos</h1>
    <div class="imagenes-tablas">
      <?php 
      $contador = 1;
      foreach($tablas as $t){ 
        $img = $imagenes_tablas[$t];
        echo "<div class='item'>";
        echo "<img src='$img' alt='Tabla $contador'>";
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

  <!-- Formulario para crear nueva tabla -->
  <form method="POST">
    <input type="hidden" name="crearTabla" value="1">
    <label>
      <input type="checkbox" id="checkNueva" onclick="toggleCampo()"> Crear nueva tabla
    </label>
    <div id="campoNombre" style="display:none;">
      <input type="text" name="nuevaTabla" placeholder="Ingresa nombre de la nueva tabla">
    </div>
    <br>
    <button type="submit">Crear</button>
  </form>

  <!-- Opción para ingresar datos en tabla existente -->
  <form method="POST">
    <input type="hidden" name="insertarObjeto" value="1">
    <label>
      <input type="checkbox" id="checkIngresar" onclick="toggleIngresar()"> Ingresar datos en tabla existente
    </label>
    <div id="campoIngresar" style="display:none;">
      <label>Selecciona la tabla:</label><br>
      <select name="tabla" required>
        <option value="">-- Selecciona --</option>
        <?php foreach($tablas as $t){ echo "<option value='$t'>$t</option>"; } ?>
      </select><br><br>

      <label>Nombre:</label><br>
      <input type="text" name="nombre" required><br><br>

      <label>Valor:</label><br>
      <input type="number" step="0.01" name="valor" required><br><br>

      <label>Fecha de registro:</label><br>
      <input type="datetime-local" name="fecha_registro" required><br><br>

      <button type="submit">Guardar objeto</button>
    </div>
  </form>
</div>

</body>
</html>
