
<?php
/**
 * ProductoControlador.php
 * Controlador de productos de TecnoShop
 * SENA ADSO
 */

require_once __DIR__ . '/../modelos/ProductoModelo.php';

class ProductoControlador
{
    private $modelo;

    public function __construct($bd)
    {
        $this->modelo = new ProductoModelo($bd);
    }

    /**
     * Mostrar listado de productos en la vista.
     */
    public function listar()
    {
        $productos = $this->modelo->obtenerTodos();

        if (!is_array($productos)) {
            $productos = [];
        }

        $categorias = $this->modelo->obtenerCategorias();

        if (!is_array($categorias)) {
            $categorias = [];
        }

        header('Content-Type: text/html; charset=utf-8');

        require __DIR__ . '/../vistas/layout/header.php';
        require __DIR__ . '/../vistas/productos/lista.php';
        require __DIR__ . '/../vistas/layout/footer.php';
    }

    /**
     * Consultar todos los productos mediante la API.
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
     * Registrar un producto.
     */
    public function guardar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($datos['nombre'])) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'El nombre del producto es obligatorio.'
            ]);

            return;
        }

        if (
            !isset($datos['precio']) || !is_numeric($datos['precio'])
            || (float) $datos['precio'] <= 0
        ) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'El precio debe ser mayor que cero.'
            ]);

            return;
        }

        $ok = $this->modelo->crear($datos);

        http_response_code($ok ? 201 : 500);

        echo json_encode([
            'ok' => (bool) $ok,
            'mensaje' => $ok
                ? 'Producto registrado correctamente.'
                : 'No fue posible registrar el producto.'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Actualizar un producto.
     */
    public function actualizar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($datos['id']) || empty($datos['nombre'])) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'El ID y el nombre son obligatorios.'
            ]);

            return;
        }

        $ok = $this->modelo->actualizar($datos);

        if (!$ok) {
            http_response_code(500);
        }

        echo json_encode([
            'ok' => (bool) $ok,
            'mensaje' => $ok
                ? 'Producto actualizado correctamente.'
                : 'No fue posible actualizar el producto.'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Eliminar un producto.
     */
    public function eliminar()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            http_response_code(400);

            echo json_encode([
                'ok' => false,
                'mensaje' => 'Se requiere un ID válido.'
            ]);

            return;
        }

        $ok = $this->modelo->eliminar($id);

        if (!$ok) {
            http_response_code(500);
        }

        echo json_encode([
            'ok' => (bool) $ok,
            'mensaje' => $ok
                ? 'Producto eliminado correctamente.'
                : 'No fue posible eliminar el producto.'
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Consultar categorías.
     */
    public function categorias()
    {
        header('Content-Type: application/json; charset=utf-8');

        $datos = $this->modelo->obtenerCategorias();

        if (!is_array($datos)) {
            $datos = [];
        }

        echo json_encode([
            'ok' => true,
            'datos' => $datos
        ], JSON_UNESCAPED_UNICODE);
    }
}
