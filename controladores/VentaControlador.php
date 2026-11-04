
<?php

require_once __DIR__ . '/../modelos/VentaModelo.php';

class VentaControlador
{
    private $modelo;

    public function __construct($bd)
    {
        $this->modelo = new VentaModelo($bd);
    }

    /**
     * Mostrar la página de ventas.
     */
    public function listar()
    {
        header('Content-Type: text/html; charset=utf-8');

        require __DIR__ . '/../vistas/layout/header.php';
        require __DIR__ . '/../vistas/ventas/lista.php';
        require __DIR__ . '/../vistas/layout/footer.php';
    }

    /**
     * Devolver las ventas en formato JSON.
     */
    public function obtenerTodos()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = $this->modelo->obtenerTodos();

        if (!is_array($datos)) {
            $datos = [];
        }

        echo json_encode([
            'ok' => true,
            'datos' => $datos,
            'total' => count($datos)
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Registrar una venta.
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = json_decode(file_get_contents('php://input'), true) ?? [];

        $clienteId = $datos['cliente_id'] ?? $datos['id_cliente'] ?? null;

        if (!$clienteId || !filter_var($clienteId, FILTER_VALIDATE_INT)) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Debes seleccionar un cliente válido.'
            ], JSON_UNESCAPED_UNICODE);

            return;
        }

        $datos['cliente_id'] = (int) $clienteId;

        $id = $this->modelo->crear($datos);

        if (!$id) {
            http_response_code(500);
        } else {
            http_response_code(201);
        }

        echo json_encode([
            'ok' => (bool) $id,
            'id_venta' => $id ?: null,
            'mensaje' => $id
                ? 'Venta registrada correctamente.'
                : 'No fue posible registrar la venta.'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Anular una venta.
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

        $ok = $this->modelo->cancelar($id);

        if (!$ok) {
            http_response_code(400);
        }

        echo json_encode([
            'ok' => (bool) $ok,
            'mensaje' => $ok
                ? 'Venta anulada correctamente.'
                : 'No fue posible anular la venta.'
        ], JSON_UNESCAPED_UNICODE);
    }
}
