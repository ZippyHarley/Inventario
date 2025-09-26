<?php
require_once __DIR__ . "/../Core/Database.php";

class HechuraIngrediente {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function allByHechura($hechura_id) {
        $stmt = $this->pdo->prepare("SELECT hi.id, i.nombre, hi.cantidad, i.unidad, i.costo_unitario, 
                                            (hi.cantidad * i.costo_unitario) AS total
                                     FROM hechura_ingredientes hi
                                     JOIN ingredientes i ON hi.ingrediente_id = i.id
                                     WHERE hi.hechura_id = :id");
        $stmt->execute(['id' => $hechura_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($hechura_id, $ingrediente_id, $cantidad) {
        $stmt = $this->pdo->prepare("INSERT INTO hechura_ingredientes (hechura_id, ingrediente_id, cantidad)
                                     VALUES (:hechura_id, :ingrediente_id, :cantidad)");
        return $stmt->execute([
            'hechura_id' => $hechura_id,
            'ingrediente_id' => $ingrediente_id,
            'cantidad' => $cantidad
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM hechura_ingredientes WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
