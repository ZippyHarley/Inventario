<?php
require_once __DIR__ . '/../core/Database.php';

class ReportesController {
    public static function mensual($mes, $anio) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT h.id, h.fecha, h.cantidad, h.costo_unitario,
                   SUM(hi.cantidad * i.costo_unitario) as inversion
            FROM hechuras h
            LEFT JOIN hechura_ingredientes hi ON hi.hechura_id=h.id
            LEFT JOIN ingredientes i ON i.id=hi.ingrediente_id
            WHERE MONTH(h.fecha)=:m AND YEAR(h.fecha)=:y
            GROUP BY h.id
        ");
        $stmt->execute(['m'=>$mes,'y'=>$anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function anual($anio) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT MONTH(h.fecha) as mes,
                   SUM(hi.cantidad * i.costo_unitario) as inversion
            FROM hechuras h
            LEFT JOIN hechura_ingredientes hi ON hi.hechura_id=h.id
            LEFT JOIN ingredientes i ON i.id=hi.ingrediente_id
            WHERE YEAR(h.fecha)=:y
            GROUP BY mes
        ");
        $stmt->execute(['y'=>$anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
