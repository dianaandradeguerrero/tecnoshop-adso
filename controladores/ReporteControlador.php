<?php
require_once __DIR__ . '/../modelos/ReporteModelo.php';
class ReporteControlador {
    private $modelo;
    public function __construct($bd) { $this->modelo = new ReporteModelo($bd); }

    public function dashboard() {
        header('Content-Type: text/html; charset=utf-8');
        require_once __DIR__ . '/../vistas/layout/header.php';
        require_once __DIR__ . '/../vistas/reportes/dashboard.php';
        require_once __DIR__ . '/../vistas/layout/footer.php';
    }

    public function ventasPorMes()      { echo json_encode(['ok'=>true,'datos'=>$this->modelo->ventasPorMes()]); }
    public function ventasPorCategoria(){ echo json_encode(['ok'=>true,'datos'=>$this->modelo->ventasPorCategoria()]); }
    public function topProductos()      { echo json_encode(['ok'=>true,'datos'=>$this->modelo->topProductos()]); }
    public function stockBajo()         { echo json_encode(['ok'=>true,'datos'=>$this->modelo->stockBajo()]); }
}
