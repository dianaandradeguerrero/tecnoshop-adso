<?php
class VentaModelo
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
   * Registrar una venta con sus productos y cantidades.
   * Calcula el total en el servidor y descuenta el inventario.
   */
  public function crear($datos)
  {
    $clienteId = filter_var(
      $datos['cliente_id'] ?? null,
      FILTER_VALIDATE_INT
    );

    $productos = $datos['productos'] ?? [];

    if (!$clienteId || !is_array($productos) || empty($productos)) {
      return false;
    }

    // Consolidar productos repetidos y validar cantidades.
    $cantidades = [];

    foreach ($productos as $producto) {
      $productoId = filter_var(
        $producto['producto_id'] ?? null,
        FILTER_VALIDATE_INT
      );

      $cantidad = filter_var(
        $producto['cantidad'] ?? null,
        FILTER_VALIDATE_INT
      );

      if (!$productoId || !$cantidad || $cantidad < 1) {
        return false;
      }

      if (!isset($cantidades[$productoId])) {
        $cantidades[$productoId] = 0;
      }

      $cantidades[$productoId] += $cantidad;
    }

    $this->bd->begin_transaction();

    try {
      // Verificar que el cliente exista.
      $stmtCliente = $this->bd->prepare(
        "SELECT id FROM clientes WHERE id = ?"
      );

      if (!$stmtCliente) {
        throw new Exception($this->bd->error);
      }

      $stmtCliente->bind_param('i', $clienteId);
      $stmtCliente->execute();

      $cliente = $stmtCliente->get_result()->fetch_assoc();
      $stmtCliente->close();

      if (!$cliente) {
        throw new Exception('El cliente seleccionado no existe.');
      }

      $total = 0;
      $detalles = [];

      // Consultar y bloquear cada producto mientras se registra.
      $stmtProducto = $this->bd->prepare(
        "SELECT id, nombre, precio, stock
                 FROM productos
                 WHERE id = ? AND estado = 'activo'
                 FOR UPDATE"
      );

      if (!$stmtProducto) {
        throw new Exception($this->bd->error);
      }

      foreach ($cantidades as $productoId => $cantidad) {
        $stmtProducto->bind_param('i', $productoId);
        $stmtProducto->execute();

        $producto = $stmtProducto->get_result()->fetch_assoc();

        if (!$producto) {
          throw new Exception(
            "El producto ID {$productoId} no existe o está inactivo."
          );
        }

        if ((int) $producto['stock'] < $cantidad) {
          throw new Exception(
            "Stock insuficiente para: " . $producto['nombre']
          );
        }

        $precio = (float) $producto['precio'];
        $subtotal = $precio * $cantidad;

        $total += $subtotal;

        $detalles[] = [
          'producto_id' => (int) $productoId,
          'cantidad' => $cantidad,
          'precio' => $precio
        ];
      }

      $stmtProducto->close();

      if ($total <= 0) {
        throw new Exception('El total de la venta debe ser mayor que cero.');
      }

      // Crear la venta con el total calculado en el servidor.
      $estado = 'activa';

      $stmtVenta = $this->bd->prepare(
        "INSERT INTO ventas (cliente_id, total, estado)
                 VALUES (?, ?, ?)"
      );

      if (!$stmtVenta) {
        throw new Exception($this->bd->error);
      }

      $stmtVenta->bind_param('ids', $clienteId, $total, $estado);

      if (!$stmtVenta->execute()) {
        throw new Exception($stmtVenta->error);
      }

      $ventaId = $this->bd->insert_id;
      $stmtVenta->close();

      // Registrar los detalles de la venta.
      $stmtDetalle = $this->bd->prepare(
        "INSERT INTO detalle_ventas
                    (venta_id, producto_id, cantidad, precio_unit)
                 VALUES (?, ?, ?, ?)"
      );

      if (!$stmtDetalle) {
        throw new Exception($this->bd->error);
      }

      // Descontar las unidades vendidas.
      $stmtStock = $this->bd->prepare(
        "UPDATE productos
                 SET stock = stock - ?
                 WHERE id = ? AND stock >= ?"
      );

      if (!$stmtStock) {
        throw new Exception($this->bd->error);
      }

      foreach ($detalles as $detalle) {
        $productoId = $detalle['producto_id'];
        $cantidad = $detalle['cantidad'];
        $precio = $detalle['precio'];

        $stmtDetalle->bind_param(
          'iiid',
          $ventaId,
          $productoId,
          $cantidad,
          $precio
        );

        if (!$stmtDetalle->execute()) {
          throw new Exception($stmtDetalle->error);
        }

        $stmtStock->bind_param(
          'iii',
          $cantidad,
          $productoId,
          $cantidad
        );

        if (!$stmtStock->execute() || $stmtStock->affected_rows !== 1) {
          throw new Exception(
            'No fue posible actualizar el inventario.'
          );
        }
      }

      $stmtDetalle->close();
      $stmtStock->close();

      $this->bd->commit();

      return [
        'id' => $ventaId,
        'total' => $total
      ];
    } catch (Throwable $error) {
      $this->bd->rollback();

      error_log('Error al registrar venta: ' . $error->getMessage());

      return false;
    }
  }

  /**
   * Anular una venta y devolver al inventario los productos vendidos.
   * La operación es transaccional y solo permite anular ventas activas.
   */
  public function cancelar($id)
  {
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
      return [
        'ok' => false,
        'mensaje' => 'El identificador de la venta no es válido.'
      ];
    }

    try {
      $this->bd->begin_transaction();

      // Bloquear la venta para impedir dos anulaciones simultáneas.
      $stmtVenta = $this->bd->prepare(
        "SELECT id, estado
         FROM ventas
         WHERE id = ?
         FOR UPDATE"
      );

      if (!$stmtVenta) {
        throw new Exception($this->bd->error);
      }

      $stmtVenta->bind_param('i', $id);

      if (!$stmtVenta->execute()) {
        throw new Exception($stmtVenta->error);
      }

      $venta = $stmtVenta->get_result()->fetch_assoc();
      $stmtVenta->close();

      if (!$venta) {
        throw new Exception('La venta que intentas anular no existe.');
      }

      if ($venta['estado'] !== 'activa') {
        throw new Exception('Esta venta ya está anulada o no se encuentra activa.');
      }

      // Obtener cantidades agrupadas por producto.
      $stmtDetalles = $this->bd->prepare(
        "SELECT producto_id, SUM(cantidad) AS cantidad
         FROM detalle_ventas
         WHERE venta_id = ?
         GROUP BY producto_id
         ORDER BY producto_id"
      );

      if (!$stmtDetalles) {
        throw new Exception($this->bd->error);
      }

      $stmtDetalles->bind_param('i', $id);

      if (!$stmtDetalles->execute()) {
        throw new Exception($stmtDetalles->error);
      }

      $detalles = $stmtDetalles->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmtDetalles->close();

      if (empty($detalles)) {
        throw new Exception(
          'La venta no tiene productos registrados; no se puede devolver inventario.'
        );
      }

      // Devolver al inventario las cantidades de cada producto.
      // Se actualizan por ID, incluso si el producto fue marcado inactivo.
      $stmtDevolver = $this->bd->prepare(
        "UPDATE productos
         SET stock = stock + ?
         WHERE id = ?"
      );

      if (!$stmtDevolver) {
        throw new Exception($this->bd->error);
      }

      foreach ($detalles as $detalle) {
        $productoId = (int) $detalle['producto_id'];
        $cantidad = (int) $detalle['cantidad'];

        if ($productoId < 1 || $cantidad < 1) {
          throw new Exception('Se encontró un detalle de venta inválido.');
        }

        $stmtDevolver->bind_param('ii', $cantidad, $productoId);

        if (!$stmtDevolver->execute() || $stmtDevolver->affected_rows !== 1) {
          throw new Exception(
            "No fue posible devolver al inventario el producto ID {$productoId}."
          );
        }
      }

      $stmtDevolver->close();

      // Cambiar el estado solo después de devolver correctamente el inventario.
      $stmtActualizar = $this->bd->prepare(
        "UPDATE ventas
         SET estado = 'anulada'
         WHERE id = ? AND estado = 'activa'"
      );

      if (!$stmtActualizar) {
        throw new Exception($this->bd->error);
      }

      $stmtActualizar->bind_param('i', $id);

      if (!$stmtActualizar->execute() || $stmtActualizar->affected_rows !== 1) {
        throw new Exception('No fue posible actualizar el estado de la venta.');
      }

      $stmtActualizar->close();

      $this->bd->commit();

      return [
        'ok' => true,
        'mensaje' => 'Venta anulada correctamente y existencias devueltas al inventario.'
      ];
    } catch (Throwable $error) {
      $this->bd->rollback();

      error_log('Error al anular venta ID ' . $id . ': ' . $error->getMessage());

      return [
        'ok' => false,
        'mensaje' => $error->getMessage()
      ];
    }
  }
}
