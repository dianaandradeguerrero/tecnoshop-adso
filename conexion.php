
<?php
/**
 * conexion.php — Conexión MySQL para TecnoShop
 */

mysqli_report(MYSQLI_REPORT_OFF);

/**
 * Cargar variables desde el archivo .env
 */
$rutaEnv = __DIR__ . '/.env';

if (file_exists($rutaEnv)) {
  $variables = parse_ini_file($rutaEnv, false, INI_SCANNER_RAW);

  if ($variables !== false) {
    foreach ($variables as $nombre => $valor) {
      if (getenv($nombre) === false) {
        putenv($nombre . '=' . $valor);
      }
    }
  }
}

/**
 * Configuración de conexión
 */
$host = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$clave = getenv('DB_PASS');
$clave = $clave === false ? '' : $clave;
$base = getenv('DB_NAME') ?: 'tecnoshop_db_restaurada';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

/**
 * Conectar con MySQL
 */
$conexion = new mysqli($host, $usuario, $clave, $base);

if ($conexion->connect_error) {
  http_response_code(500);
  die('Error de conexión a MySQL: ' . htmlspecialchars($conexion->connect_error, ENT_QUOTES, 'UTF-8'));
}

if (!$conexion->set_charset($charset)) {
  http_response_code(500);
  die('Error al configurar el conjunto de caracteres.');
}
