<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $config = include __DIR__ . '/../../config/config.php';
        $db = $config['db'];
        try {
            $this->pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}",
                $db['user'],
                $db['pass']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error DB: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
