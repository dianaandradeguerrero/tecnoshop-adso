<?php
/**
 * IAControlador.php — Inteligencia Artificial con Gemini API
 * TecnoShop · SENA ADSO · Sesión 11 · Juan Carlos Huertas Niño
 * Obtener API Key gratis en: https://aistudio.google.com/app/apikey
 */
class IAControlador {
    private $bd;
    public function __construct($bd) { $this->bd = $bd; }

    /** Sugerencias de productos para un cliente según su historial */
    public function sugerirProductos() {
        $idCliente = intval($_GET['id'] ?? 0);
        if (!$idCliente) {
            http_response_code(400);
            echo json_encode(['ok'=>false,'mensaje'=>'ID de cliente requerido']);
            return;
        }

        // Obtener historial de compras
        $sql = "SELECT p.nombre FROM detalle_ventas dv
                JOIN ventas v    ON dv.id_venta    = v.id
                JOIN productos p ON dv.id_producto = p.id
                WHERE v.id_cliente = $idCliente
                ORDER BY v.fecha DESC LIMIT 10";
        $res       = $this->bd->query($sql);
        $historial = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

        if (empty($historial)) {
            echo json_encode(['ok'=>true,
                'datos'=>['sugerencias'=>['Cable USB-C','Funda protectora','Power Bank']],
                'mensaje'=>'Sugerencias generales (sin historial)']);
            return;
        }

        $productos = implode(', ', array_map(fn($p) => $p['nombre'], $historial));
        $prompt    = "Cliente de tienda tech compró: $productos. " .
                     "Sugiere 3 productos complementarios. " .
                     'Responde SOLO JSON: {"sugerencias":["p1","p2","p3"]}';

        $apiKey  = getenv('GEMINI_API_KEY') ?: 'REEMPLAZA_CON_TU_API_KEY';
        $url     = "https://generativelanguage.googleapis.com/v1beta/" .
                   "models/gemini-1.5-flash:generateContent?key=$apiKey";
        $payload = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]]]);

        $ctx  = stream_context_create(['http'=>[
            'method'=>'POST',
            'header'=>"Content-Type: application/json\r\n",
            'content'=>$payload, 'timeout'=>10,
        ]]);
        $resp = @file_get_contents($url, false, $ctx);

        if (!$resp) {
            echo json_encode(['ok'=>true,
                'datos'=>['sugerencias'=>['Funda premium','Cargador rápido 65W','Cable USB-C']],
                'mensaje'=>'Sugerencias de respaldo (API no disponible)']);
            return;
        }

        $data  = json_decode($resp, true);
        $texto = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $texto = preg_replace('/```json|```/', '', $texto);
        $sugs  = json_decode(trim($texto), true);
        echo json_encode(['ok'=>true,
            'datos'=>$sugs ?: ['sugerencias'=>['Cable USB-C','Funda','Power Bank']],
            'mensaje'=>'Sugerencias generadas por IA Gemini']);
    }

    /** Predicción de agotamiento de stock basada en velocidad de ventas */
    public function predecirAgotamiento() {
        $sql = "SELECT p.id, p.nombre, p.stock, p.marca,
                       COUNT(dv.id) AS ventas_30d,
                       ROUND(p.stock / NULLIF(COUNT(dv.id),0), 1) AS dias_restantes
                FROM productos p
                LEFT JOIN detalle_ventas dv ON p.id = dv.id_producto
                LEFT JOIN ventas v ON dv.id_venta = v.id
                    AND v.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE p.estado = 'activo'
                GROUP BY p.id, p.nombre, p.stock, p.marca
                HAVING ventas_30d > 0
                ORDER BY dias_restantes ASC LIMIT 5";

        $datos = $this->bd->query($sql)->fetch_all(MYSQLI_ASSOC);
        $pred  = array_map(function($p) {
            $dias = floatval($p['dias_restantes']);
            return array_merge($p, [
                'dias_restantes' => $dias,
                'alerta' => $dias <= 7  ? 'CRÍTICO'   : ($dias <= 15 ? 'PRECAUCIÓN' : 'NORMAL'),
                'color'  => $dias <= 7  ? 'danger'    : ($dias <= 15 ? 'warning'    : 'success'),
            ]);
        }, $datos);
        echo json_encode(['ok'=>true,'datos'=>$pred,'mensaje'=>'Predicción calculada']);
    }

    /** Resumen inteligente del negocio para el dashboard */
    public function resumenNegocio() {
        $mesActual   = $this->bd->query(
            "SELECT COALESCE(SUM(total),0) AS t FROM ventas
             WHERE MONTH(fecha)=MONTH(NOW()) AND estado='completada'"
        )->fetch_assoc()['t'];
        $mesAnterior = $this->bd->query(
            "SELECT COALESCE(SUM(total),0) AS t FROM ventas
             WHERE MONTH(fecha)=MONTH(NOW())-1 AND estado='completada'"
        )->fetch_assoc()['t'];
        $crec = $mesAnterior > 0
            ? round((($mesActual - $mesAnterior) / $mesAnterior) * 100, 1)
            : 100;
        echo json_encode(['ok'=>true,'datos'=>[
            'ventas_mes'      => floatval($mesActual),
            'mes_anterior'    => floatval($mesAnterior),
            'crecimiento_pct' => $crec,
            'tendencia'       => $crec >= 0 ? 'subiendo' : 'bajando',
            'emoji'           => $crec >= 0 ? '📈' : '📉',
            'consejo_ia'      => $crec < 0
                ? 'Las ventas bajaron. Considera hacer promociones en los productos más vendidos.'
                : 'Excelente tendencia. Asegúrate de tener stock suficiente de los productos estrella.',
        ],'mensaje'=>'Resumen generado']);
    }
}
