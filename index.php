<?php
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

// Obtener imágenes de la carpeta img
$imagenes = glob("img/*.{jpg,png,jpeg}", GLOB_BRACE); 
shuffle($imagenes); // Mezclar aleatoriamente

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Arancelario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="left">
                <button class="btn" onclick="window.location.href='añadir.php'">Agregar</button>
            </div>
            <div class="right">
                <button onclick="window.location.href='consultar.php'">Consultar y modificar</button>
            </div>
        </div>
        <div class="hero">
            <h1>BIENVENIDO</h1>
            <p id="textoEditable">Este es tu sistema arancelario. Aquí podrás añadir, consultar y modificar registros fácilmente.</p>
        </div>
    </header>


   <section class="carousel">
        <div class="track">
            <span>Estas son las tablas que hay disponibles....</span>
            <span>Estas son las tablas que hay disponibles....</span>
        </div>
    </section>

    <!-- Carrusel dinámico con tablas -->
    <section class="carousel">
        <div class="track">
            <?php 
            $contador = 0;
            foreach($tablas as $t){ 
                // Asignar imagen distinta mientras haya disponibles
                $img = isset($imagenes[$contador]) ? $imagenes[$contador] : $imagenes[$contador % count($imagenes)];
                $contador++;
            ?>
                <div class="item">
                    <img src="<?php echo $img; ?>" alt="Tabla <?php echo $t; ?>">
                    <div class="buttons">
                        <button onclick="window.location.href='consultar.php?revisar=<?php echo $t; ?>'">Revisar</button>
                        <button onclick="window.location.href='añadir.php'">Añadir datos</button>
                    </div>
                    <p><?php echo $t; ?></p>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Resumen de tablas -->
    <section id="resumenTablas">
      <div class="cuadro-blanco" id="cuadroTablas">
        <h2>Resumen de Tablas</h2>
        <p>Actualmente hay <b><?php echo $total; ?></b> tabla(s) en la base de datos.</p>
        <ul style="list-style:none;padding:0;">
          <?php foreach($tablas as $t){ echo "<li><b>$t</b></li>"; } ?>
        </ul>
      </div>
    </section>
</body>
</html>
