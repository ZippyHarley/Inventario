<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$ventasTotal = $pdo->query("SELECT COALESCE(SUM(cantidad*precio_unitario),0) AS t FROM ventas")->fetch(PDO::FETCH_ASSOC)['t'];

$inv = $pdo->query("SELECT COALESCE(SUM(hi.cantidad*i.costo_unitario),0) AS t
                    FROM hechura_ingredientes hi
                    JOIN ingredientes i ON i.id=hi.ingrediente_id")->fetch(PDO::FETCH_ASSOC)['t'];

$utilidad = $ventasTotal - $inv;

$title="Ganancias";
ob_start();
?>
<h1>Ganancias</h1>
<div class="grid grid-3">
  <div class="card"><h2>Ventas</h2><p class="right" style="font-size:20px">$<?= number_format($ventasTotal,2) ?></p></div>
  <div class="card"><h2>Inversión</h2><p class="right" style="font-size:20px">$<?= number_format($inv,2) ?></p></div>
  <div class="card"><h2>Utilidad</h2><p class="right" style="font-size:20px"><b>$<?= number_format($utilidad,2) ?></b></p></div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
