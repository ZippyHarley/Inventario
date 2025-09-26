<?php
require_once __DIR__ . "/../Core/Database.php";

class Producto {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM productos ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre, stock, precio) VALUES (:nombre, :stock, :precio)");
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE productos SET nombre=:nombre, stock=:stock, precio=:precio WHERE id=:id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    public function updateStock($id, $cantidad, $tipo = 'sumar') {
        $op = $tipo === 'sumar' ? '+' : '-';
        $stmt = $this->pdo->prepare("UPDATE productos SET stock = stock $op :cantidad WHERE id=:id AND stock + (CASE WHEN :op = '-' THEN -:cantidad ELSE :cantidad END) >= 0");
        return $stmt->execute([
            'id' => $id,
            'cantidad' => $cantidad,
            'op' => $op
        ]);
    }
}
