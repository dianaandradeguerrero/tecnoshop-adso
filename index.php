
<?php
/**
 * index.php — Front Controller de TecnoShop
 * Proyecto SENA ADSO.
 */

session_start();

require_once __DIR__ . '/conexion.php';

$modulo = $_GET['modulo'] ?? 'dashboard';
$accion = $_GET['accion'] ?? 'listar';

/**
 * Cerrar sesión PHP.
 */
if ($modulo === 'logout') {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $parametros['path'],
            $parametros['domain'],
            $parametros['secure'],
            $parametros['httponly'],
        );
    }

    session_destroy();

    header('Location: /tecnoshop_vscode/?modulo=login');
    exit();
}

/**
 * Establecer sesión después de validar el JWT de la API REST.
 */
if ($modulo === 'login' && $accion === 'establecerSesion') {
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(
            [
                'ok' => false,
                'mensaje' => 'Método no permitido.',
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    $entrada = json_decode(file_get_contents('php://input'), true);
    $token = $entrada['token'] ?? '';
    $secreto = getenv('JWT_SECRET');

    if (!is_string($token) || $token === '' || !$secreto) {
        http_response_code(401);
        echo json_encode(
            [
                'ok' => false,
                'mensaje' => 'Falta el token o la configuración de autenticación.',
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    $partes = explode('.', $token);

    if (count($partes) !== 3) {
        http_response_code(401);
        echo json_encode(
            [
                'ok' => false,
                'mensaje' => 'El token no tiene un formato válido.',
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    [$cabecera, $cuerpo, $firma] = $partes;

    $firmaEsperada = rtrim(
        strtr(
            base64_encode(hash_hmac('sha256', $cabecera . '.' . $cuerpo, $secreto, true)),
            '+/',
            '-_',
        ),
        '=',
    );

    if (!hash_equals($firmaEsperada, $firma)) {
        http_response_code(401);
        echo json_encode(
            [
                'ok' => false,
                'mensaje' => 'El token no es válido.',
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    $contenido = base64_decode(strtr($cuerpo, '-_', '+/'), true);

    $datosToken = $contenido !== false ? json_decode($contenido, true) : null;

    if (
        !is_array($datosToken) ||
        empty($datosToken['exp']) ||
        $datosToken['exp'] <= time() ||
        empty($datosToken['id'])
    ) {
        http_response_code(401);
        echo json_encode(
            [
                'ok' => false,
                'mensaje' => 'El token ha expirado o no contiene un usuario válido.',
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['tecnoshop_autenticado'] = true;
    $_SESSION['tecnoshop_usuario_id'] = (int) $datosToken['id'];

    echo json_encode(
        [
            'ok' => true,
            'mensaje' => 'Sesión iniciada correctamente.',
        ],
        JSON_UNESCAPED_UNICODE,
    );
    exit();
}

/**
 * Mostrar login sin exigir una sesión.
 */
if ($modulo === 'login') {
    header('Content-Type: text/html; charset=utf-8');
    require __DIR__ . '/vistas/login.php';
    exit();
}

/**
 * Cargar controladores existentes.
 */
require_once __DIR__ . '/controladores/ProductoControlador.php';
require_once __DIR__ . '/controladores/ClienteControlador.php';
require_once __DIR__ . '/controladores/VentaControlador.php';
require_once __DIR__ . '/controladores/ReporteControlador.php';
require_once __DIR__ . '/controladores/IAControlador.php';

$controladores = [
    'productos' => ProductoControlador::class,
    'clientes' => ClienteControlador::class,
    'ventas' => VentaControlador::class,
    'reportes' => ReporteControlador::class,
    'ia' => IAControlador::class,
];

$accionesJson = [
    'obtenerTodos',
    'obtenerPorId',
    'categorias',
    'guardar',
    'actualizar',
    'eliminar',
    'cancelar',
    'ventasPorMes',
    'ventasPorCategoria',
    'topProductos',
    'stockBajo',
];

/**
 * Bloquear las solicitudes de datos sin sesión.
 */
if (
    isset($controladores[$modulo]) &&
    in_array($accion, $accionesJson, true) &&
    empty($_SESSION['tecnoshop_autenticado'])
) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        [
            'ok' => false,
            'mensaje' => 'Sesión no autenticada. Inicia sesión nuevamente.',
        ],
        JSON_UNESCAPED_UNICODE,
    );
    exit();
}

/**
 * Bloquear todas las páginas privadas sin sesión.
 */
if (empty($_SESSION['tecnoshop_autenticado'])) {
    header('Location: /tecnoshop_vscode/?modulo=login');
    exit();
}

/**
 * Ejecutar módulos y acciones existentes.
 */
if (isset($controladores[$modulo])) {
    $claseControlador = $controladores[$modulo];
    $controlador = new $claseControlador($conexion);

    if (!method_exists($controlador, $accion)) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            [
                'ok' => false,
                'mensaje' => "Acción '$accion' no encontrada en el módulo '$modulo'.",
            ],
            JSON_UNESCAPED_UNICODE,
        );
        exit();
    }

    if (in_array($accion, $accionesJson, true)) {
        $controlador->$accion();
        exit();
    }

    if ($accion === 'listar' || $accion === 'dashboard') {
        $controlador->$accion();
        exit();
    }

    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        [
            'ok' => false,
            'mensaje' => "La acción '$accion' no está habilitada.",
        ],
        JSON_UNESCAPED_UNICODE,
    );
    exit();
}

/**
 * Dashboard principal.
 */
if ($modulo === 'dashboard') {
    header('Content-Type: text/html; charset=utf-8');

    require __DIR__ . '/vistas/layout/header.php';
    require __DIR__ . '/vistas/dashboard.php';
    require __DIR__ . '/vistas/layout/footer.php';
    exit();
}

http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
echo '<h1>Módulo no encontrado</h1>';
