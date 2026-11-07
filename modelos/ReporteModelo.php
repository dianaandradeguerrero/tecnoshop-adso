
<?php
class ReporteModelo
{
    private $bd;

    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    /**
     * Ejecuta una consulta y devuelve sus resultados.
     * Evita errores fatales cuando MySQL rechaza una consulta.
     */
    private function consultar($sql)
    {
        $resultado = $this->bd->query($sql);

        if (!$resultado) {
            error_log('Error en ReporteModelo: ' . $this->bd->error);
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function ventasPorMes()
    {
        return $this->consultar(
            "SELECT
                DATE_FORMAT(fecha, '%Y-%m') AS mes,
                COUNT(*) AS cantidad,
                SUM(total) AS total
             FROM ventas
             WHERE YEAR(fecha) = YEAR(NOW())
               AND estado = 'activa'
             GROUP BY mes
             ORDER BY mes ASC"
        );
    }

    public function ventasPorCategoria()
    {
        return $this->consultar(
            "SELECT
                c.nombre AS categoria,
                SUM(dv.subtotal) AS total
             FROM detalle_ventas dv
             JOIN productos p ON dv.producto_id = p.id
             LEFT JOIN categorias c ON p.categoria_id = c.id
             JOIN ventas v ON dv.venta_id = v.id
             WHERE v.estado = 'activa'
             GROUP BY c.nombre
             ORDER BY total DESC"
        );
    }

    public function topProductos()
    {
        return $this->consultar(
            "SELECT
                p.nombre,
                SUM(dv.cantidad) AS total_vendido,
                SUM(dv.subtotal) AS total_ingresos
             FROM detalle_ventas dv
             JOIN productos p ON dv.producto_id = p.id
             JOIN ventas v ON dv.venta_id = v.id
             WHERE v.estado = 'activa'
             GROUP BY p.id, p.nombre
             ORDER BY total_vendido DESC
             LIMIT 5"
        );
    }

    public function stockBajo()
    {
        return $this->consultar(
            "SELECT
                id,
                nombre,
                stock,
                precio,
                marca
             FROM productos
             WHERE stock < 5
               AND estado = 'activo'
             ORDER BY stock ASC"
        );
    }
}
