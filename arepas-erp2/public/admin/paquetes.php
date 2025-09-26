<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (isset($_POST['crear'])){
    $pdo->prepare("INSERT INTO paquetes (nombre,unidades,precio) VALUES (:n,:u,:p)")
        ->execute(['n'=>$_POST['nombre'],'u'=>$_POST['unidades'],'p'=>$_POST['precio']]);
  }
  if (isset($_POST['editar'])){
    $pdo->prepare("UPDATE paquetes SET nombre=:n, unidades=:u, precio=:p WHERE id=:id")
        ->execute(['n'=>$_POST['nombre'],'u'=>$_POST['unidades'],'p'=>$_POST['precio'],'id'=>$_POST['id']]);
  }
  header("Location:/arepas-erp2/public/admin/paquetes.php"); exit;
}
if (isset($_GET['delete'])){
  $pdo->prepare("DELETE FROM paquetes WHERE id=:id")->execute(['id'=>$_GET['delete']]);
  header("Location:/arepas-erp2/public/admin/paquetes.php"); exit;
}
$rows = $pdo->query("SELECT * FROM paquetes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$title="Admin • Paquetes";
ob_start();
?>
<h1>Paquetes</h1>
<div class="card">
  <form method="post" class="grid grid-3">
    <div><label>Nombre</label><input name="nombre" required></div>
    <div><label>Unidades</label><input type="number" name="unidades" required></div>
    <div><label>Precio</label><input type="number" step="0.01" name="precio" required></div>
    <div style="grid-column:1/-1;text-align:right"><button class="btn btn-primary" name="crear">Agregar</button></div>
  </form>
</div>

<div class="card mt-3">
  <table class="table">
    <thead><tr><th>ID</th><th>Nombre</th><th>Unidades</th><th>Precio</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= $r['nombre'] ?></td>
        <td><?= $r['unidades'] ?></td>
        <td>$<?= number_format($r['precio'],2) ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <input name="nombre" value="<?= $r['nombre'] ?>" style="width:140px">
            <input name="unidades" type="number" value="<?= $r['unidades'] ?>" style="width:90px">
            <input name="precio" type="number" step="0.01" value="<?= $r['precio'] ?>" style="width:110px">
            <button class="btn btn-ghost" name="editar">Guardar</button>
          </form>
          <a class="btn btn-danger" href="?delete=<?= $r['id'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
