<?php

$nombre = $_GET['nombre'] ?? '';
$comentario = $_GET['comentario'] ?? '';


$dbFile = 'basedatos.db';
$db = new SQLite3($dbFile);

$db->exec("CREATE TABLE IF NOT EXISTS usuarios (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, comentario TEXT)");


if ($nombre !== '' && $comentario !== '') {
    $queryInsert = "INSERT INTO usuarios (nombre, comentario) VALUES ('$nombre', '$comentario')";
    $db->exec($queryInsert);
}

$querySelect = "SELECT * FROM usuarios WHERE nombre = '$nombre'";
$resultado = $db->query($querySelect);


$cmd = "ping -c 1 $nombre";
$output = shell_exec($cmd);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Portal Web Proyecto Seguridad en redes TCP/IP Grupo 3</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 700px;
      margin: 40px auto;
      background-color: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    img {
      width: 120px;
      margin-bottom: 20px;
    }

    h1,
    h2 {
      color: #2c3e50;
    }

    form {
      text-align: left;
      margin-top: 30px;
    }

    label {
      font-weight: bold;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 8px;
      margin-top: 6px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="submit"] {
      background-color: #3498db;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #2980b9;
    }

    pre {
      background: #f0f0f0;
      text-align: left;
      padding: 10px;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div class="container">
    <img src="https://umg.edu.gt/img/Umg.png" alt="Logo Universidad">
    <h1>Proyecto de Seguridad en Redes TCP/IP</h1>
    <h2>Grupo 3</h2>
    <p>Bienvenido al Portal de Pruebas. Este portal está diseñado para realizar pruebas de seguridad.</p>

    <h2>Formulario de Prueba</h2>
    <form action="" method="GET">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>">

      <label for="comentario">Comentario:</label>
      <textarea id="comentario" name="comentario" rows="4" cols="50"><?php echo $comentario; ?></textarea>

      <input type="submit" value="Enviar">
    </form>

    <h3>Resultado:</h3>
    <div>
      <?php
      echo "<p>Nombre recibido: $nombre</p>";             
      echo "<p>Comentario recibido: $comentario</p>";

      echo "<p><b>Consulta SQL generada:</b> $querySelect</p>";

      echo "<p><b>Resultados de la consulta:</b></p>";
      echo "<ul>";
      while ($fila = $resultado->fetchArray(SQLITE3_ASSOC)) {
          echo "<li>ID: {$fila['id']}, Nombre: {$fila['nombre']}, Comentario: {$fila['comentario']}</li>";
      }
      echo "</ul>";

      echo "<p><b>Comando ejecutado:</b> $cmd</p>";
      echo "<pre>$output</pre>";
      ?>
    </div>
  </div>
</body>

</html>
