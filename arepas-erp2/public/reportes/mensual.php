<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

$vstmt = $pdo->prepare("SELECT * FROM ventas WHERE MONTH(fecha)=:m AND YEAR(fecha)=:y ORDER BY fecha");
$vstmt->execute(['m'=>$mes,'y'=>$anio]);
$ventas = $vstmt->fetchAll(PDO::FETCH_ASSOC);

$istmt = $pdo->prepare("SELECT COALESCE(SUM(hi.cantidad*i.costo_unitario),0) t
  FROM hechura_ingredientes hi
  JOIN ingredientes i ON i.id=hi.ingrediente_id
  JOIN hechuras h ON h.id=hi.hechura_id
  WHERE MONTH(h.fecha)=:m AND YEAR(h.fecha)=:y");
$istmt->execute(['m'=>$mes,'y'=>$anio]);
$inversion = $istmt->fetch(PDO::FETCH_ASSOC)['t'];

$totalVentas = 0; foreach($ventas as $v){ $totalVentas += $v['cantidad']*$v['precio_unitario']; }
$utilidad = $totalVentas - $inversion;

$title="Reporte Mensual";
ob_start();
?>
<h1>Reporte Mensual (<?= $mes ?>/<?= $anio ?>)</h1>

<div class="grid grid-3">
  <div class="card"><h2>Inversión</h2><p class="right" style="font-size:20px">$<?= number_format($inversion,2) ?></p></div>
  <div class="card"><h2>Ventas</h2><p class="right" style="font-size:20px">$<?= number_format($totalVentas,2) ?></p></div>
  <div class="card"><h2>Utilidad</h2><p class="right" style="font-size:20px"><b>$<?= number_format($utilidad,2) ?></b></p></div>
</div>

<div class="card mt-3">
  <form method="get" class="grid grid-3">
    <div><label>Mes</label>
      <select name="mes"><?php for($i=1;$i<=12;$i++): ?>
        <option value="<?= sprintf('%02d',$i) ?>" <?= $mes==sprintf('%02d',$i)?'selected':'' ?>><?= sprintf('%02d',$i) ?></option>
      <?php endfor; ?></select>
    </div>
    <div><label>Año</label><input type="number" name="anio" value="<?= $anio ?>"></div>
    <div style="align-self:end">
      <button class="btn btn-primary">Ver</button>
      <a class="btn btn-ghost" target="_blank" href="/arepas-erp2/public/reportes/pdf.php?mes=<?= $mes ?>&anio=<?= $anio ?>">Exportar PDF</a>
      <button class="btn btn-ghost" onclick="window.print();return false;">Imprimir</button>
    </div>
  </form>
</div>

<div class="card mt-3">
  <h2>Detalle ventas</h2>
  <table class="table">
    <thead><tr><th>Fecha</th><th>Producto</th><th>Cant.</th><th>Precio U.</th><th>Total</th></tr></thead>
    <tbody>
      <?php foreach($ventas as $v): ?>
      <tr>
        <td><?= $v['fecha'] ?></td>
        <td><?= $v['producto'] ?></td>
        <td class="right"><?= $v['cantidad'] ?></td>
        <td class="right">$<?= number_format($v['precio_unitario'],2) ?></td>
        <td class="right">$<?= number_format($v['cantidad']*$v['precio_unitario'],2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
