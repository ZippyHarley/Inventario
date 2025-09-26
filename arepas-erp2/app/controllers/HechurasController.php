<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . '/../models/Hechura.php';
require_once __DIR__ . '/../core/Database.php';

class HechurasController {
    public static function getByMesAnio($mes, $anio) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM hechuras WHERE MONTH(fecha)=:m AND YEAR(fecha)=:y ORDER BY fecha DESC");
        $stmt->execute(['m'=>$mes,'y'=>$anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($fecha, $cantidad, $costo_unitario) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("INSERT INTO hechuras(fecha, cantidad, costo_unitario) VALUES (:f,:c,:cu)");
        $stmt->execute(['f'=>$fecha,'c'=>$cantidad,'cu'=>$costo_unitario]);
        return $pdo->lastInsertId();
    }
}
