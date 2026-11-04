
<?php
/**
 * ClienteControlador.php
 * Controlador de clientes de TecnoShop
 * Proyecto SENA ADSO
 */

require_once __DIR__ . '/../modelos/ClienteModelo.php';

class ClienteControlador
{
  private $modelo;

  public function __construct($bd)
  {
    $this->modelo = new ClienteModelo($bd);
  }

  public function listar()
  {
    header('Content-Type: text/html; charset=utf-8');

    $clientes = $this->modelo->obtenerTodos();

    require __DIR__ . '/../vistas/layout/header.php';
    require __DIR__ . '/../vistas/clientes/lista.php';
    require __DIR__ . '/../vistas/layout/footer.php';
  }

  public function obtenerTodos()
  {
    header('Content-Type: application/json; charset=utf-8');

    $datos = $this->modelo->obtenerTodos();

    if (!is_array($datos)) {
      $datos = [];
    }

    echo json_encode(
      [
        'ok' => true,
        'datos' => $datos,
        'total' => count($datos),
      ],
      JSON_UNESCAPED_UNICODE,
    );
  }

  public function obtenerPorId()
  {
    header('Content-Type: application/json; charset=utf-8');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
      http_response_code(400);
      echo json_encode(
        [
          'ok' => false,
          'mensaje' => 'Se requiere un identificador de cliente válido.',
        ],
        JSON_UNESCAPED_UNICODE,
      );
      return;
    }

    $cliente = $this->modelo->obtenerPorId($id);

    if (!$cliente) {
      http_response_code(404);
      echo json_encode(
        [
          'ok' => false,
          'mensaje' => 'Cliente no encontrado.',
        ],
        JSON_UNESCAPED_UNICODE,
      );
      return;
    }

    echo json_encode(
      [
        'ok' => true,
        'datos' => $cliente,
      ],
      JSON_UNESCAPED_UNICODE,
    );
  }

  public function guardar()
  {
    header('Content-Type: application/json; charset=utf-8');

    $datos = json_decode(file_get_contents('php://input'), true);

    if (
      !is_array($datos) ||
      empty($datos['nombre']) ||
      empty($datos['documento']) ||
      empty($datos['email'])
    ) {
      http_response_code(400);
      echo json_encode(
        [
          'ok' => false,
          'mensaje' => 'Nombre, documento y correo electrónico son obligatorios.',
        ],
        JSON_UNESCAPED_UNICODE,
      );
      return;
    }

    // El modelo compartido actualmente solo implementa consultas.
    http_response_code(501);
    echo json_encode(
      [
        'ok' => false,
        'mensaje' =>
        'El registro de clientes requiere implementar el método crear en ClienteModelo.',
      ],
      JSON_UNESCAPED_UNICODE,
    );
  }

  public function actualizar()
  {
    header('Content-Type: application/json; charset=utf-8');

    http_response_code(501);
    echo json_encode(
      [
        'ok' => false,
        'mensaje' => 'La actualización requiere implementar el método actualizar en ClienteModelo.',
      ],
      JSON_UNESCAPED_UNICODE,
    );
  }

  public function eliminar()
  {
    header('Content-Type: application/json; charset=utf-8');

    http_response_code(501);
    echo json_encode(
      [
        'ok' => false,
        'mensaje' => 'La eliminación requiere implementar el método eliminar en ClienteModelo.',
      ],
      JSON_UNESCAPED_UNICODE,
    );
  }
}
