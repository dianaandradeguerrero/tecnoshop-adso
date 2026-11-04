
<?php

class ClienteModelo
{
    private $bd;

    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT id, nombre, documento, email, telefono, fecha_creacion
                FROM clientes
                ORDER BY nombre ASC";

        $resultado = $this->bd->query($sql);

        if (!$resultado) {
            error_log('Error al consultar clientes: ' . $this->bd->error);
            return [];
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->bd->prepare(
            "SELECT id, nombre, documento, email, telefono, fecha_creacion
             FROM clientes
             WHERE id = ?"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $cliente = $resultado->fetch_assoc();

        $stmt->close();

        return $cliente ?: null;
    }
}
