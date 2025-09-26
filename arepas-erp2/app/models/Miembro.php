<?php
require_once __DIR__ . "/../Core/Database.php";

class Miembro {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM miembros ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $porcentaje) {
        $stmt = $this->pdo->prepare("UPDATE miembros SET porcentaje=:p WHERE id=:id");
        return $stmt->execute(['id' => $id, 'p' => $porcentaje]);
    }

    public function createDefault() {
        // Si no hay miembros, creamos 7 con 0%
        $stmt = $this->pdo->query("SELECT COUNT(*) as c FROM miembros");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
        if ($count == 0) {
            for ($i=1; $i<=7; $i++) {
                $this->pdo->prepare("INSERT INTO miembros (nombre, porcentaje) VALUES (:n, 0)")
                          ->execute(['n'=>"Miembro $i"]);
            }
        }
    }
}
