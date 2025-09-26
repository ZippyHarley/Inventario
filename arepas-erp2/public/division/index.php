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

// crear 7 por default si no existen
$c = $pdo->query("SELECT COUNT(*) c FROM miembros")->fetch(PDO::FETCH_ASSOC)['c'];
if (!$c){
  for($i=1;$i<=7;$i++){
    $pdo->prepare("INSERT INTO miembros (nombre, porcentaje) VALUES (:n,0)")->execute(['n'=>"Miembro $i"]);
  }
}

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $porcs = $_POST['porcentaje'] ?? [];
  $suma = array_sum($porcs);
  foreach($porcs as $id=>$p){
    $p = ($suma>100) ? ($p * 100 / $suma) : $p;
    $pdo->prepare("UPDATE miembros SET porcentaje=:p WHERE id=:id")->execute(['p'=>$p,'id'=>$id]);
  }
  header("Location:/arepas-erp2/public/division/index.php"); exit;
}

$miembros = $pdo->query("SELECT * FROM miembros ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$title="División de Ganancias";
ob_start();
?>
<h1>División de Ganancias</h1>
<p>Utilidad a repartir: <b>$<?= number_format($utilidad,2) ?></b></p>

<div class="card">
  <form method="post">
    <table class="table">
      <thead><tr><th>Miembro</th><th>%</th><th>Ganancia</th></tr></thead>
      <tbody>
        <?php foreach($miembros as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td style="width:160px">
              <input type="number" step="0.01" name="porcentaje[<?= $m['id'] ?>]" value="<?= $m['porcentaje'] ?>">
            </td>
            <td class="right">$<?= number_format($utilidad*$m['porcentaje']/100,2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="right mt-3"><button class="btn btn-primary">Guardar porcentajes</button></div>
  </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
