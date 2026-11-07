<?php
require_once __DIR__ . '/../modelos/VentaModelo.php';

class VentaControlador
{
  private $modelo;

  public function __construct($bd)
  {
    $this->modelo = new VentaModelo($bd);
  }

  public function listar()
  {
    header('Content-Type: text/html; charset=utf-8');

    require __DIR__ . '/../vistas/layout/header.php';
    require __DIR__ . '/../vistas/ventas/lista.php';
    require __DIR__ . '/../vistas/layout/footer.php';
  }

  public function obtenerTodos()
  {
    header('Content-Type: application/json; charset=utf-8');

    $datos = $this->modelo->obtenerTodos();

    echo json_encode([
      'ok' => true,
      'datos' => $datos,
      'total' => count($datos)
    ], JSON_UNESCAPED_UNICODE);
  }

  public function guardar()
  {
    header('Content-Type: application/json; charset=utf-8');

    $datos = json_decode(file_get_contents('php://input'), true);

    if (!is_array($datos)) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => 'Los datos enviados no son válidos.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    $clienteId = filter_var(
      $datos['cliente_id'] ?? null,
      FILTER_VALIDATE_INT
    );

    if (!$clienteId || $clienteId < 1) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => 'Debes seleccionar un cliente válido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    if (
      !isset($datos['productos']) ||
      !is_array($datos['productos']) ||
      empty($datos['productos'])
    ) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => 'Debes agregar al menos un producto.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    $resultado = $this->modelo->crear([
      'cliente_id' => $clienteId,
      'productos' => $datos['productos']
    ]);

    if (!$resultado) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => 'No se pudo registrar la venta. Verifica los productos y sus existencias.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    http_response_code(201);

    echo json_encode([
      'ok' => true,
      'id_venta' => $resultado['id'],
      'total' => $resultado['total'],
      'mensaje' => 'Venta registrada correctamente.'
    ], JSON_UNESCAPED_UNICODE);
  }

  /**
   * Anular una venta y devolver sus productos al inventario.
   */
  public function cancelar()
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => 'El identificador de la venta no es válido.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    $resultado = $this->modelo->cancelar($id);

    if (!is_array($resultado) || empty($resultado['ok'])) {
      http_response_code(400);

      echo json_encode([
        'ok' => false,
        'mensaje' => is_array($resultado)
          ? ($resultado['mensaje'] ?? 'No fue posible anular la venta.')
          : 'No fue posible anular la venta.'
      ], JSON_UNESCAPED_UNICODE);

      return;
    }

    echo json_encode([
      'ok' => true,
      'mensaje' => $resultado['mensaje']
    ], JSON_UNESCAPED_UNICODE);
  }
}
