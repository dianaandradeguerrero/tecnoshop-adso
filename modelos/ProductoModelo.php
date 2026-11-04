<?php
class ProductoModelo {
    private $bd;
    public function __construct($bd) { $this->bd = $bd; }

    public function obtenerTodos() {
        $sql = "SELECT p.*, c.nombre AS categoria
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 'activo' ORDER BY p.nombre ASC";
        return $this->bd->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->bd->prepare(
            "SELECT * FROM productos WHERE id=? AND estado='activo'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($d) {
        $stmt = $this->bd->prepare(
            "INSERT INTO productos
             (nombre,descripcion,precio,stock,marca,categoria_id,estado)
             VALUES (?,?,?,?,?,?,'activo')");
        $stmt->bind_param('ssdisi',
            $d['nombre'],$d['descripcion'],
            $d['precio'],$d['stock'],
            $d['marca'], $d['categoria_id']);
        return $stmt->execute();
    }

    public function actualizar($d) {
        $stmt = $this->bd->prepare(
            "UPDATE productos
             SET nombre=?,descripcion=?,precio=?,stock=?,marca=?,categoria_id=?
             WHERE id=?");
        $stmt->bind_param('ssdisii',
            $d['nombre'],$d['descripcion'],
            $d['precio'],$d['stock'],
            $d['marca'], $d['categoria_id'],$d['id']);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->bd->prepare(
            "UPDATE productos SET estado='inactivo' WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function stockBajo($limite = 5) {
        $stmt = $this->bd->prepare(
            "SELECT id,nombre,stock,precio,marca FROM productos
             WHERE stock < ? AND estado='activo' ORDER BY stock ASC");
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerCategorias() {
        return $this->bd->query(
            "SELECT * FROM categorias ORDER BY nombre ASC"
        )->fetch_all(MYSQLI_ASSOC);
    }
}
