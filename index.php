<?php
session_start();


header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header_remove("X-Powered-By"); 

if (!function_exists('hash_equals')) {
  function hash_equals($known_string, $user_string) {
      if (!is_string($known_string) || !is_string($user_string)) {
          return false;
      }

      if (strlen($known_string) !== strlen($user_string)) {
          return false;
      }

      $res = $known_string ^ $user_string;
      $ret = 0;

      for ($i = strlen($res) - 1; $i >= 0; $i--) {
          $ret |= ord($res[$i]);
      }

      return !$ret;
  }
}

function limpiarEntrada($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generarToken($length = 32) {
  $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $token = '';
  for ($i = 0; $i < $length; $i++) {
      $token .= $chars[mt_rand(0, strlen($chars) - 1)];
  }
  return $token;
}

$_SESSION['csrf_token'] = generarToken(64);

$tokenValido = true;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $tokenValido = false;
    }
}

$nombre = isset($_POST['nombre']) ? limpiarEntrada($_POST['nombre']) : '';
$comentario = isset($_POST['comentario']) ? limpiarEntrada($_POST['comentario']) : '';

$dbFile = 'basedatos.db';
$db = new SQLite3($dbFile);

$db->exec("CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    comentario TEXT
)");

if ($tokenValido && $nombre !== '' && $comentario !== '') {
    $stmt = $db->prepare("INSERT INTO usuarios (nombre, comentario) VALUES (:nombre, :comentario)");
    $stmt->bindValue(':nombre', $nombre, SQLITE3_TEXT);
    $stmt->bindValue(':comentario', $comentario, SQLITE3_TEXT);
    $stmt->execute();
}

$stmtSelect = $db->prepare("SELECT * FROM usuarios WHERE nombre = :nombre");
$stmtSelect->bindValue(':nombre', $nombre, SQLITE3_TEXT);
$resultado = $stmtSelect->execute();

if (preg_match('/^[a-zA-Z0-9\.\-]{1,30}$/', $nombre)) {
    $cmd = "ping -c 1 " . escapeshellarg($nombre);
    $output = shell_exec($cmd);
} else {
    $cmd = "Comando bloqueado por seguridad.";
    $output = "Entrada no válida para el comando ping.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' https://umg.edu.gt;">
  <title>Portal Web Proyecto Seguridad en redes TCP/IP Grupo 3</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <div class="container">
    <img src="Umg.png" alt="Logo Universidad">
    <h1>Proyecto de Seguridad en Redes TCP/IP</h1>
    <h2>Grupo 3</h2>
    <p>Bienvenido al Portal de Pruebas. Este portal está diseñado para realizar pruebas de seguridad.</p>

    <h2>Formulario de Prueba</h2>
    <form action="" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <label for="nombre">Nombre:</label><br>
      <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>"> <br><br>

      <label for="comentario">Comentario:</label><br>
      <textarea id="comentario" name="comentario" rows="4" cols="50"><?php echo $comentario; ?></textarea><br><br>

      <input type="submit" value="Enviar">
    </form>

    <h3>Resultado:</h3>
    <div>
      <p><b>Nombre recibido:</b> <?php echo $nombre; ?></p>
      <p><b>Comentario recibido:</b> <?php echo $comentario; ?></p>

      <p><b>Resultados de la consulta:</b></p>
      <ul>
      <?php
      while ($fila = $resultado->fetchArray(SQLITE3_ASSOC)) {
          $id = htmlspecialchars($fila['id'], ENT_QUOTES, 'UTF-8');
          $nombreDB = htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8');
          $comentarioDB = htmlspecialchars($fila['comentario'], ENT_QUOTES, 'UTF-8');
          echo "<li>ID: $id, Nombre: $nombreDB, Comentario: $comentarioDB</li>";
      }
      ?>
      </ul>

      <p><b>Comando ejecutado:</b> <?php echo htmlspecialchars($cmd, ENT_QUOTES, 'UTF-8'); ?></p>
      <pre><?php echo htmlspecialchars($output, ENT_QUOTES, 'UTF-8'); ?></pre>
    </div>
  </div>
</body>
</html>
