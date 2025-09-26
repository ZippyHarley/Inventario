<?php
require_once __DIR__ . "/../Core/Database.php";

class Venta {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM ventas ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO ventas (fecha, producto, cantidad, precio_unitario) 
                                     VALUES (:fecha, :producto, :cantidad, :precio_unitario)");
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ventas WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    public function totalVentas() {
        $stmt = $this->pdo->query("SELECT SUM(cantidad * precio_unitario) as total FROM ventas");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}
