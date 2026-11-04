<?php

/**
 * Dashboard principal de TecnoShop
 * Compatible con tecnoshop_db_restaurada
 */

if (!isset($conexion) || !($conexion instanceof mysqli)) {
  die('Error: no existe una conexión válida a MySQL.');
}

/**
 * Ejecuta consultas de conteo y devuelve cero
 * si la consulta falla.
 */
function obtenerTotal(mysqli $conexion, string $sql): int
{
  $resultado = $conexion->query($sql);

  if ($resultado === false) {
    error_log('Error en consulta del dashboard: ' . $conexion->error);
    return 0;
  }

  $fila = $resultado->fetch_assoc();

  return (int) ($fila['total'] ?? 0);
}

/**
 * Obtiene un valor monetario.
 */
function obtenerImporte(mysqli $conexion, string $sql): float
{
  $resultado = $conexion->query($sql);

  if ($resultado === false) {
    error_log('Error en consulta de ingresos: ' . $conexion->error);
    return 0;
  }

  $fila = $resultado->fetch_assoc();

  return (float) ($fila['total'] ?? 0);
}

// Indicadores principales.
$totalProductos = obtenerTotal(
  $conexion,
  "SELECT COUNT(*) AS total
     FROM productos
     WHERE estado = 'activo'",
);

// La tabla clientes no tiene columna estado.
$totalClientes = obtenerTotal($conexion, 'SELECT COUNT(*) AS total FROM clientes');

$totalVentas = obtenerTotal(
  $conexion,
  "SELECT COUNT(*) AS total
     FROM ventas
     WHERE MONTH(fecha) = MONTH(CURRENT_DATE())
       AND YEAR(fecha) = YEAR(CURRENT_DATE())",
);

$ingresosMes = obtenerImporte(
  $conexion,
  "SELECT COALESCE(SUM(total), 0) AS total
     FROM ventas
     WHERE MONTH(fecha) = MONTH(CURRENT_DATE())
       AND YEAR(fecha) = YEAR(CURRENT_DATE())
       AND estado = 'completada'",
);

$stockBajo = obtenerTotal(
  $conexion,
  "SELECT COUNT(*) AS total
     FROM productos
     WHERE stock < 5 AND estado = 'activo'",
);

// Últimas cinco ventas.
// La columna correcta es cliente_id, no id_cliente.
$sqlVentas = "
    SELECT
        v.id,
        v.fecha,
        v.total,
        v.estado,
        c.nombre AS cliente
    FROM ventas v
    LEFT JOIN clientes c ON v.cliente_id = c.id
    ORDER BY v.fecha DESC
    LIMIT 5
";

$resultadoVentas = $conexion->query($sqlVentas);
$ultimasVentas = [];

if ($resultadoVentas !== false) {
  $ultimasVentas = $resultadoVentas->fetch_all(MYSQLI_ASSOC);
} else {
  error_log('Error al consultar últimas ventas: ' . $conexion->error);
}
?>

<section class="stats-grid">

  <div class="stat-card">
    <div class="stat-icon">📦</div>
    <div class="stat-info">
      <p>Productos activos</p>
      <h3><?= $totalProductos ?></h3>
    </div>
  </div>

  <div class="stat-card azul">
    <div class="stat-icon">👥</div>
    <div class="stat-info">
      <p>Total de clientes</p>
      <h3><?= $totalClientes ?></h3>
    </div>
  </div>

  <div class="stat-card morado">
    <div class="stat-icon">🧾</div>
    <div class="stat-info">
      <p>Ventas del mes</p>
      <h3><?= $totalVentas ?></h3>
    </div>
  </div>

  <div class="stat-card naranja">
    <div class="stat-icon">💰</div>
    <div class="stat-info">
      <p>Ingresos del mes</p>
      <h3>$<?= number_format($ingresosMes, 0, ',', '.') ?></h3>
    </div>
  </div>

  <div class="stat-card rojo">
    <div class="stat-icon">⚠️</div>
    <div class="stat-info">
      <p>Stock bajo (menos de 5)</p>
      <h3><?= $stockBajo ?></h3>
    </div>
  </div>

</section>

<section class="card">
  <div class="card-header">
    <h2>📊 Resumen de TecnoShop</h2>
  </div>

  <div class="card-body">
    <p>
      Actualmente hay
      <strong><?= $totalProductos ?></strong>
      productos activos y
      <strong><?= $totalClientes ?></strong>
      clientes registrados.
    </p>

    <p>
      Ventas registradas este mes:
      <strong><?= $totalVentas ?></strong>
    </p>

    <p>
      Ingresos de ventas completadas:
      <strong>$<?= number_format($ingresosMes, 0, ',', '.') ?></strong>
    </p>

    <p>
      Productos con existencias inferiores a cinco unidades:
      <strong><?= $stockBajo ?></strong>
    </p>
  </div>
</section>

<section class="card">
  <div class="card-header">
    <h2>🧾 Últimas cinco ventas</h2>

    <a
      href="index.php?modulo=ventas&accion=listar"
      class="btn btn-secondary btn-sm">Ver todas</a>
  </div>

  <div class="card-body">
    <?php if (empty($ultimasVentas)): ?>

      <div class="empty-state">
        <p>No hay ventas registradas todavía.</p>
      </div>

    <?php else: ?>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>N.º</th>
              <th>Cliente</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($ultimasVentas as $venta): ?>
              <tr>
                <td>
                  #<?= (int) $venta['id'] ?>
                </td>

                <td>
                  <?= htmlspecialchars($venta['cliente'] ?? 'Sin cliente', ENT_QUOTES, 'UTF-8') ?>
                </td>

                <td>
                  <?= !empty($venta['fecha'])
                    ? date('d/m/Y H:i', strtotime($venta['fecha']))
                    : '—' ?>
                </td>

                <td>
                  $<?= number_format((float) $venta['total'], 0, ',', '.') ?>
                </td>

                <td>
                  <?= htmlspecialchars(
                    ucfirst($venta['estado'] ?? 'Sin estado'),
                    ENT_QUOTES,
                    'UTF-8',
                  ) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>
  </div>
</section>