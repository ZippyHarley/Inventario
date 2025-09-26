<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

if (isset($_GET['delete'])) {
  $stmt = $pdo->prepare("DELETE FROM hechuras WHERE id=:id");
  $stmt->execute(['id'=>$_GET['delete']]);
  header("Location: /arepas-erp2/public/hechuras/index.php");
  exit;
}

$mes = $_GET['mes'] ?? date('m');
$anio = $_GET['anio'] ?? date('Y');

$stmt = $pdo->prepare("SELECT h.*, 
   (SELECT COUNT(*) FROM hechura_ingredientes hi WHERE hi.hechura_id=h.id) as ingredientes
   FROM hechuras h
   WHERE MONTH(h.fecha)=:m AND YEAR(h.fecha)=:y
   ORDER BY h.fecha DESC");
$stmt->execute(['m'=>$mes,'y'=>$anio]);
$hechuras = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Hechuras";
ob_start();
?>
<h1>Hechuras</h1>

<div class="card">
  <form method="get" class="grid grid-3">
    <div>
      <label>Mes</label>
      <select name="mes">
        <?php for($i=1;$i<=12;$i++): ?>
          <option value="<?= sprintf('%02d',$i) ?>" <?= $mes==sprintf('%02d',$i)?'selected':'' ?>><?= sprintf('%02d',$i) ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label>Año</label>
      <input type="number" name="anio" value="<?= $anio ?>">
    </div>
    <div style="align-self:end">
      <button class="btn btn-primary" type="submit">Filtrar</button>
      <a class="btn btn-ghost" href="/arepas-erp2/public/hechuras/form.php">Nueva Hechura</a>
    </div>
  </form>
</div>

<div class="card mt-3">
  <table class="table">
    <thead><tr><th>ID</th><th>Fecha</th><th>Nombre/Lote</th><th># Ingredientes</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($hechuras as $h): ?>
      <tr>
        <td><?= $h['id'] ?></td>
        <td><?= $h['fecha'] ?></td>
        <td><?= htmlspecialchars($h['nombre'] ?? '') ?></td>
        <td><?= $h['ingredientes'] ?></td>
        <td>
          <a class="btn btn-ghost" href="/arepas-erp2/public/hechuras/form.php?id=<?= $h['id'] ?>">Editar</a>
          <a class="btn btn-ghost" href="/arepas-erp2/public/hechuras/ingredientes.php?hechura=<?= $h['id'] ?>">Ingredientes</a>
          <a class="btn btn-danger" href="?delete=<?= $h['id'] ?>" onclick="return confirm('¿Eliminar hechura?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
