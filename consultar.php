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

// Obtener tablas
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

// Revisar tabla seleccionada
$tablaSeleccionada = "";
$datos = [];

if(isset($_GET["revisar"])){
  $tablaSeleccionada = $_GET["revisar"];

  $query = "SELECT * FROM $tablaSeleccionada";
  $resDatos = pg_query($conn, $query);

  while($fila = pg_fetch_assoc($resDatos)){
    $datos[] = $fila;
  }
}

// Eliminar registro
if(isset($_GET["eliminar"]) && isset($_GET["tabla"])){
  $id = intval($_GET["eliminar"]);
  $tabla = $_GET["tabla"];

  pg_query_params($conn, "DELETE FROM $tabla WHERE id = $1", [$id]);

  header("Location: consultar.php?revisar=$tabla");
  exit;
}

// Modificar registro
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST["modificar"])){
  $tabla = $_POST["tabla"];
  $id = intval($_POST["id"]);
  $campo = $_POST["campo"];
  $nuevo = $_POST["nuevo"];

  $query = "UPDATE $tabla SET $campo = $1 WHERE id = $2";
  pg_query_params($conn, $query, [$nuevo, $id]);

  header("Location: consultar.php?revisar=$tabla");
  exit;
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
    <h1>Actualmente hay <?php echo $total; ?> tabla(s)</h1>

    <div class="imagenes-tablas">
      <?php 
      $contador = 1;
      foreach($tablas as $t){ 
        $img = $imagenes_tablas[$t];
        echo "<div class='item'>";
        echo "<img src='$img'>";
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

              <label>Campo:</label>
              <select name="campo" required>
                <option value="nombre">Nombre</option>
                <option value="valor">Valor</option>
                <option value="fecha_registro">Fecha</option>
              </select>

              <br><br>

              <label>Nuevo valor:</label>
              <input type="text" name="nuevo" required>

              <br><br>

              <button type="submit">Guardar</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </table>
  <?php } ?>
</div>

</body>
</html>
