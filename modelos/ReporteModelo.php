<?php
class ReporteModelo {
    private $bd;
    public function __construct($bd) { $this->bd = $bd; }

    public function ventasPorMes() {
        return $this->bd->query(
            "SELECT DATE_FORMAT(fecha,'%Y-%m') AS mes,
                    COUNT(*) AS cantidad, SUM(total) AS total
             FROM ventas
             WHERE YEAR(fecha)=YEAR(NOW()) AND estado!='cancelada'
             GROUP BY mes ORDER BY mes ASC"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function ventasPorCategoria() {
        return $this->bd->query(
            "SELECT c.nombre AS categoria, SUM(dv.subtotal) AS total
             FROM detalle_ventas dv
             JOIN productos p ON dv.id_producto=p.id
             LEFT JOIN categorias c ON p.categoria_id=c.id
             JOIN ventas v ON dv.id_venta=v.id
             WHERE v.estado!='cancelada'
             GROUP BY c.nombre ORDER BY total DESC"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function topProductos() {
        return $this->bd->query(
            "SELECT p.nombre, SUM(dv.cantidad) AS total_vendido,
                    SUM(dv.subtotal) AS total_ingresos
             FROM detalle_ventas dv
             JOIN productos p ON dv.id_producto=p.id
             JOIN ventas v ON dv.id_venta=v.id
             WHERE v.estado!='cancelada'
             GROUP BY p.id,p.nombre
             ORDER BY total_vendido DESC LIMIT 5"
        )->fetch_all(MYSQLI_ASSOC);
    }

    public function stockBajo() {
        return $this->bd->query(
            "SELECT id,nombre,stock,precio,marca FROM productos
             WHERE stock < 5 AND estado='activo' ORDER BY stock ASC"
        )->fetch_all(MYSQLI_ASSOC);
    }
}
