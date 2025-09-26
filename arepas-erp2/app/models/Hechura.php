<?php
require_once __DIR__ . "/../Core/Database.php";

class Hechura {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function all($fecha = null) {
        if ($fecha) {
            $stmt = $this->pdo->prepare("SELECT * FROM hechuras WHERE fecha = :fecha ORDER BY fecha DESC");
            $stmt->execute(['fecha' => $fecha]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM hechuras ORDER BY fecha DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM hechuras WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO hechuras (fecha, cantidad, costo_unitario) 
                                     VALUES (:fecha, :cantidad, :costo_unitario)");
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE hechuras 
                                     SET fecha=:fecha, cantidad=:cantidad, costo_unitario=:costo_unitario 
                                     WHERE id=:id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM hechuras WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
