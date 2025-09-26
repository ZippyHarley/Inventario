<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD']==='POST'){
  if (isset($_POST['crear'])){
    $stmt=$pdo->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (:n,:p,:s)");
    $stmt->execute(['n'=>$_POST['nombre'],'p'=>$_POST['precio'],'s'=>$_POST['stock']]);
  }
  if (isset($_POST['editar'])){
    $stmt=$pdo->prepare("UPDATE productos SET nombre=:n, precio=:p, stock=:s WHERE id=:id");
    $stmt->execute(['n'=>$_POST['nombre'],'p'=>$_POST['precio'],'s'=>$_POST['stock'],'id'=>$_POST['id']]);
  }
  header("Location:/arepas-erp2/public/productos/index.php"); exit;
}
if (isset($_GET['delete'])){
  $pdo->prepare("DELETE FROM productos WHERE id=:id")->execute(['id'=>$_GET['delete']]);
  header("Location:/arepas-erp2/public/productos/index.php"); exit;
}
$productos=$pdo->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$title="Productos";
ob_start();
?>
<h1>Productos</h1>
<div class="card">
  <form method="post" class="grid grid-3">
    <div><label>Nombre</label><input name="nombre" required></div>
    <div><label>Precio</label><input type="number" step="0.01" name="precio" required></div>
    <div><label>Stock</label><input type="number" name="stock" required></div>
    <div style="grid-column:1/-1;text-align:right">
      <button class="btn btn-primary" name="crear">Agregar</button>
    </div>
  </form>
</div>

<div class="card mt-3">
  <table class="table">
    <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($productos as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['nombre'] ?></td>
        <td>$<?= number_format($p['precio'],2) ?></td>
        <td><?= $p['stock'] ?></td>
        <td>
          <form method="post" style="display:inline">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input name="nombre" value="<?= $p['nombre'] ?>" style="width:140px">
            <input name="precio" type="number" step="0.01" value="<?= $p['precio'] ?>" style="width:110px">
            <input name="stock" type="number" value="<?= $p['stock'] ?>" style="width:90px">
            <button class="btn btn-ghost" name="editar">Guardar</button>
          </form>
          <a class="btn btn-danger" href="?delete=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
