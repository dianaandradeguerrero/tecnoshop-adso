
<?php class VentaModelo
{
  private $bd;

  public function __construct($bd)
  {
    $this->bd = $bd;
  }

  /**
   * Obtener todas las ventas con el nombre del cliente.
   */
  public function obtenerTodos()
  {
    $sql = "SELECT
                    v.id,
                    v.cliente_id,
                    v.fecha,
                    v.total,
                    v.estado,
                    c.nombre AS cliente
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                ORDER BY v.fecha DESC";

    $resultado = $this->bd->query($sql);

    if (!$resultado) {
      error_log('Error al consultar ventas: ' . $this->bd->error);
      return [];
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
  }

  /**
   * Registrar una venta.
   */
  public function crear($datos)
  {
    $clienteId = filter_var($datos['cliente_id'] ?? null, FILTER_VALIDATE_INT);

    $total = $datos['total'] ?? null;

    if (!$clienteId || !is_numeric($total) || (float) $total <= 0) {
      return false;
    }

    $total = (float) $total;
    $estado = $datos['estado'] ?? 'activa';

    if (!in_array($estado, ['activa', 'anulada'], true)) {
      $estado = 'activa';
    }

    $sql = "INSERT INTO ventas (cliente_id, total, estado)
                VALUES (?, ?, ?)";

    $stmt = $this->bd->prepare($sql);

    if (!$stmt) {
      error_log('Error al preparar venta: ' . $this->bd->error);
      return false;
    }

    $stmt->bind_param('ids', $clienteId, $total, $estado);

    if ($stmt->execute()) {
      $id = $this->bd->insert_id;
      $stmt->close();
      return $id;
    }

    error_log('Error al registrar venta: ' . $stmt->error);
    $stmt->close();

    return false;
  }

  /**
   * Anular una venta sin eliminar su registro.
   */
  public function cancelar($id)
  {
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
      return false;
    }

    $sql = "UPDATE ventas
                SET estado = 'anulada'
                WHERE id = ? AND estado = 'activa'";

    $stmt = $this->bd->prepare($sql);

    if (!$stmt) {
      error_log('Error al preparar anulación: ' . $this->bd->error);
      return false;
    }

    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();

    if (!$ok) {
      error_log('Error al anular venta: ' . $stmt->error);
    }

    $stmt->close();

    return $ok;
  }
}
