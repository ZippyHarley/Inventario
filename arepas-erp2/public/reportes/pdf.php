<?php
// Genera HTML imprimible; si tienes PDF::render, descomenta al final
require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$mes  = $_GET['mes']  ?? null;
$anio = $_GET['anio'] ?? date('Y');

if ($mes){
  $titulo = "Reporte Mensual $mes/$anio";
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
} else {
  $titulo = "Reporte Anual $anio";
  $vstmt = $pdo->prepare("SELECT * FROM ventas WHERE YEAR(fecha)=:y ORDER BY fecha");
  $vstmt->execute(['y'=>$anio]);
  $ventas = $vstmt->fetchAll(PDO::FETCH_ASSOC);

  $istmt = $pdo->prepare("SELECT COALESCE(SUM(hi.cantidad*i.costo_unitario),0) t
    FROM hechura_ingredientes hi
    JOIN ingredientes i ON i.id=hi.ingrediente_id
    JOIN hechuras h ON h.id=hi.hechura_id
    WHERE YEAR(h.fecha)=:y");
  $istmt->execute(['y'=>$anio]);
  $inversion = $istmt->fetch(PDO::FETCH_ASSOC)['t'];
}

$totalVentas=0; foreach($ventas as $v){ $totalVentas += $v['cantidad']*$v['precio_unitario']; }
$utilidad = $totalVentas - $inversion;

// HTML imprimible (puedes Ctrl+P → Guardar como PDF)
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $titulo ?></title>
<link href="/arepas-erp2/public/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="container">
  <h1><?= $titulo ?></h1>
  <div class="grid grid-3">
    <div class="card"><h2>Inversión</h2><p class="right" style="font-size:20px">$<?= number_format($inversion,2) ?></p></div>
    <div class="card"><h2>Ventas</h2><p class="right" style="font-size:20px">$<?= number_format($totalVentas,2) ?></p></div>
    <div class="card"><h2>Utilidad</h2><p class="right" style="font-size:20px"><b>$<?= number_format($utilidad,2) ?></b></p></div>
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
    <div class="right mt-3">
      <button class="btn btn-ghost" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
  </div>
</div>
</body>
</html>
<?php
/* Si ya tienes una clase PDF::render con Dompdf, podrías hacer:
   ob_start();  [imprime el HTML anterior]  $html = ob_get_clean();
   require_once __DIR__ . "/../../app/core/PDF.php";
   PDF::render($html, $titulo);
*/
