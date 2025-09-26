<?php
require_once __DIR__ . "/../Core/Database.php";

class Ingrediente {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function all() {
        $stmt = $this->pdo->query("SELECT * FROM ingredientes ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
