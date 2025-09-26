<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../core/Database.php';

class UsuarioController {
    public static function getAll() {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT id, email, rol FROM usuarios ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($email, $password, $rol = 'user') {
        $pdo = Database::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios(email,password,rol) VALUES (:e,:p,:r)");
        $stmt->execute(['e'=>$email,'p'=>$hash,'r'=>$rol]);
    }

    public static function delete($id) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=:id");
        $stmt->execute(['id'=>$id]);
    }
}
