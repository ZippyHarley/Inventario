<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (isset($_POST['crear'])){
    $pdo->prepare("INSERT INTO miembros (nombre,porcentaje) VALUES (:n,:p)")
        ->execute(['n'=>$_POST['nombre'],'p'=>$_POST['porcentaje']]);
  }
  if (isset($_POST['editar'])){
    $pdo->prepare("UPDATE miembros SET nombre=:n, porcentaje=:p WHERE id=:id")
        ->execute(['n'=>$_POST['nombre'],'p'=>$_POST['porcentaje'],'id'=>$_POST['id']]);
  }
  header("Location:/arepas-erp2/public/admin/miembros.php"); exit;
}
if (isset($_GET['delete'])){
  $pdo->prepare("DELETE FROM miembros WHERE id=:id")->execute(['id'=>$_GET['delete']]);
  header("Location:/arepas-erp2/public/admin/miembros.php"); exit;
}
$rows = $pdo->query("SELECT * FROM miembros ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$title="Admin • Miembros";
ob_start();
?>
<h1>Miembros</h1>
<div class="card">
  <form method="post" class="grid grid-3">
    <div><label>Nombre</label><input name="nombre" required></div>
    <div><label>%</label><input type="number" step="0.01" name="porcentaje" required></div>
    <div style="align-self:end"><button class="btn btn-primary" name="crear">Agregar</button></div>
  </form>
</div>

<div class="card mt-3">
  <table class="table">
    <thead><tr><th>ID</th><th>Nombre</th><th>%</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($rows as $m): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= $m['nombre'] ?></td>
        <td><?= $m['porcentaje'] ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= $m['id'] ?>">
            <input name="nombre" value="<?= $m['nombre'] ?>" style="width:160px">
            <input name="porcentaje" type="number" step="0.01" value="<?= $m['porcentaje'] ?>" style="width:90px">
            <button class="btn btn-ghost" name="editar">Guardar</button>
          </form>
          <a class="btn btn-danger" href="?delete=<?= $m['id'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
