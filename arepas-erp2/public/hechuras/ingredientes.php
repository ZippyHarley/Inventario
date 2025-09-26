<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$hechura_id = intval($_GET['hechura'] ?? 0);
if ($hechura_id<=0) { header("Location:/arepas-erp2/public/hechuras/index.php"); exit; }

$h = $pdo->prepare("SELECT * FROM hechuras WHERE id=:id");
$h->execute(['id'=>$hechura_id]);
$hechura = $h->fetch(PDO::FETCH_ASSOC);

if (!$hechura){ header("Location:/arepas-erp2/public/hechuras/index.php"); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO hechura_ingredientes (hechura_id, ingrediente_id, cantidad) VALUES (:h,:i,:c)");
    $stmt->execute([
      'h'=>$hechura_id,
      'i'=>$_POST['ingrediente_id'],
      'c'=>$_POST['cantidad']
    ]);
  }
  if (isset($_POST['del'])) {
    $stmt = $pdo->prepare("DELETE FROM hechura_ingredientes WHERE id=:id AND hechura_id=:h");
    $stmt->execute(['id'=>$_POST['id'],'h'=>$hechura_id]);
  }
  header("Location: ?hechura=".$hechura_id); exit;
}

$ingredientes = $pdo->query("SELECT * FROM ingredientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$listado = $pdo->prepare("SELECT hi.*, i.nombre, i.unidad, i.costo_unitario
                          FROM hechura_ingredientes hi 
                          JOIN ingredientes i ON i.id=hi.ingrediente_id
                          WHERE hi.hechura_id=:h");
$listado->execute(['h'=>$hechura_id]);
$det = $listado->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach($det as $d){ $total += $d['cantidad'] * $d['costo_unitario']; }

$title = "Ingredientes Hechura #{$hechura_id}";
ob_start();
?>
<h1>Ingredientes de la Hechura</h1>
<p><b>Hechura:</b> <?= htmlspecialchars($hechura['nombre'] ?? '') ?> — <b>Fecha:</b> <?= $hechura['fecha'] ?></p>

<div class="card">
  <form method="post" class="grid grid-3">
    <div>
      <label>Ingrediente</label>
      <select name="ingrediente_id" required>
        <?php foreach($ingredientes as $i): ?>
          <option value="<?= $i['id'] ?>"><?= $i['nombre'] ?> (<?= $i['unidad'] ?> @ $<?= number_format($i['costo_unitario'],2) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Cantidad</label>
      <input type="number" step="0.01" name="cantidad" required>
    </div>
    <div style="align-self:end">
      <button class="btn btn-primary" name="add">Agregar</button>
      <a class="btn btn-ghost" href="/arepas-erp2/public/hechuras/index.php">Volver</a>
    </div>
  </form>
</div>

<div class="card mt-3">
  <h2>Detalle</h2>
  <table class="table">
    <thead><tr><th>Ingrediente</th><th>Unidad</th><th>Costo</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
    <tbody>
      <?php foreach($det as $d): ?>
      <tr>
        <td><?= $d['nombre'] ?></td>
        <td><?= $d['unidad'] ?></td>
        <td class="right">$<?= number_format($d['costo_unitario'],2) ?></td>
        <td class="right"><?= $d['cantidad'] ?></td>
        <td class="right">$<?= number_format($d['cantidad']*$d['costo_unitario'],2) ?></td>
        <td>
          <form method="post" onsubmit="return confirm('¿Eliminar ingrediente?')" style="display:inline">
            <input type="hidden" name="id" value="<?= $d['id'] ?>">
            <button class="btn btn-danger" name="del">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="4" class="right">Total inversión hechura</th>
        <th class="right">$<?= number_format($total,2) ?></th>
        <th></th>
      </tr>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
