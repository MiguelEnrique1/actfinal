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

// Revisar tabla seleccionada
$tablaSeleccionada = "";
$datos = [];
if(isset($_GET["revisar"])){
  $tablaSeleccionada = $_GET["revisar"];
  $resDatos = $conn->query("SELECT * FROM `$tablaSeleccionada`");
  while($fila = $resDatos->fetch_assoc()){
    $datos[] = $fila;
  }
}

// Eliminar registro
if(isset($_GET["eliminar"]) && isset($_GET["tabla"])){
  $id = intval($_GET["eliminar"]);
  $tabla = $_GET["tabla"];
  $conn->query("DELETE FROM `$tabla` WHERE id=$id");
  header("Location: consultar.php?revisar=$tabla");
  exit;
}

// Modificar registro
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["modificar"])){
  $tabla = $_POST["tabla"];
  $id = intval($_POST["id"]);
  $campo = $_POST["campo"];
  $nuevo = $_POST["nuevo"];

  $stmt = $conn->prepare("UPDATE `$tabla` SET $campo=? WHERE id=?");
  $stmt->bind_param("si",$nuevo,$id);
  $stmt->execute();
  header("Location: consultar.php?revisar=$tabla");
  exit;
}

// Obtener imágenes de la carpeta img y asignarlas de forma persistente
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
<title>Consultar Tablas</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="top-bar">
    <div class="left">
      <button onclick="window.location.href='añadir.php'">Agregar</button>
    </div>
    <div class="right">
      <button onclick="window.location.href='index.php'">Volver</button>
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
        echo "<button onclick=\"window.location.href='consultar.php?revisar=$t'\">Revisar</button>";
        echo "</div>";
        $contador++;
      } 
      ?>
    </div>
  </div>
</header>

<div class="cuadro">
  <?php if($tablaSeleccionada!=""){ ?>
    <h2>Datos de la tabla '<?php echo $tablaSeleccionada; ?>'</h2>
    <button onclick="window.location.href='añadir.php'">Añadir</button>
    <br><br>
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Valor</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
      <?php foreach($datos as $fila){ ?>
        <tr>
          <td><?php echo $fila["id"]; ?></td>
          <td><?php echo $fila["nombre"]; ?></td>
          <td><?php echo $fila["valor"]; ?></td>
          <td><?php echo $fila["fecha_registro"]; ?></td>
          <td>
            <button onclick="window.location.href='consultar.php?tabla=<?php echo $tablaSeleccionada; ?>&eliminar=<?php echo $fila['id']; ?>'">Eliminar</button>
            <button onclick="document.getElementById('form<?php echo $fila['id']; ?>').style.display='block'">Modificar</button>
          </td>
        </tr>
        <tr id="form<?php echo $fila['id']; ?>" style="display:none;">
          <td colspan="5">
            <form method="POST">
              <input type="hidden" name="modificar" value="1">
              <input type="hidden" name="tabla" value="<?php echo $tablaSeleccionada; ?>">
              <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
              <label>Campo a modificar:</label>
              <select name="campo" required>
                <option value="nombre">Nombre</option>
                <option value="valor">Valor</option>
                <option value="fecha_registro">Fecha Registro</option>
              </select>
              <br><br>
              <label>Nuevo valor:</label>
              <input type="text" name="nuevo" required>
              <br><br>
              <button type="submit">Guardar cambios</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
</div>

</body>
</html>
