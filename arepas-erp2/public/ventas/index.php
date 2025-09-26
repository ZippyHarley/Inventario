<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $producto_id = intval($_POST['producto_id']);
  $cantidad = intval($_POST['cantidad']);
  $fecha = $_POST['fecha'];

  $p = $pdo->prepare("SELECT * FROM productos WHERE id=:id");
  $p->execute(['id'=>$producto_id]);
  $prod = $p->fetch(PDO::FETCH_ASSOC);

  if ($prod && $prod['stock'] >= $cantidad){
    $pdo->prepare("INSERT INTO ventas (fecha, producto, cantidad, precio_unitario) VALUES (:f,:pr,:c,:pu)")
        ->execute(['f'=>$fecha,'pr'=>$prod['nombre'],'c'=>$cantidad,'pu'=>$prod['precio']]);
    $pdo->prepare("UPDATE productos SET stock = stock - :c WHERE id=:id")
        ->execute(['c'=>$cantidad,'id'=>$producto_id]);
  } else {
    $error="No hay suficiente stock para esta venta.";
  }
  header("Location:/arepas-erp2/public/ventas/index.php"); exit;
}

if (isset($_GET['delete'])){
  // Nota: si quieres devolver stock al eliminar venta, agrega lógica aquí.
  $pdo->prepare("DELETE FROM ventas WHERE id=:id")->execute(['id'=>$_GET['delete']]);
  header("Location:/arepas-erp2/public/ventas/index.php"); exit;
}

$productos = $pdo->query("SELECT * FROM productos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$ventas = $pdo->query("SELECT * FROM ventas ORDER BY fecha DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

$totalVentas = 0; foreach($ventas as $v){ $totalVentas += $v['cantidad']*$v['precio_unitario']; }

$title="Ventas";
ob_start();
?>
<h1>Ventas</h1>

<div class="card">
  <form method="post" class="grid grid-3">
    <div>
      <label>Fecha</label>
      <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
    </div>
    <div>
      <label>Producto</label>
      <select name="producto_id">
        <?php foreach($productos as $p): ?>
          <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?> (Stock: <?= $p['stock'] ?>, $<?= number_format($p['precio'],2) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Cantidad</label>
      <input type="number" name="cantidad" min="1" required>
    </div>
    <div style="grid-column:1/-1;text-align:right">
      <button class="btn btn-primary">Registrar venta</button>
      <a class="btn btn-ghost" href="/arepas-erp2/public/productos/index.php">Gestionar productos</a>
    </div>
  </form>
</div>

<div class="card mt-3">
  <h2>Historial</h2>
  <table class="table">
    <thead><tr><th>Fecha</th><th>Producto</th><th>Cantidad</th><th>Precio U.</th><th>Total</th><th></th></tr></thead>
    <tbody>
      <?php foreach($ventas as $v): ?>
      <tr>
        <td><?= $v['fecha'] ?></td>
        <td><?= $v['producto'] ?></td>
        <td class="right"><?= $v['cantidad'] ?></td>
        <td class="right">$<?= number_format($v['precio_unitario'],2) ?></td>
        <td class="right">$<?= number_format($v['cantidad']*$v['precio_unitario'],2) ?></td>
        <td><a class="btn btn-danger" href="?delete=<?= $v['id'] ?>" onclick="return confirm('¿Eliminar venta?')">Eliminar</a></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="4" class="right">Total</th>
        <th class="right">$<?= number_format($totalVentas,2) ?></th>
        <th></th>
      </tr>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
