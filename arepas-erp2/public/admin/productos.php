<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
if ($_SESSION['user']['rol'] !== 'admin') { header("Location:/arepas-erp2/public/dashboard.php?error=forbidden"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

// Crear producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['precio'])) {
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio_unitario) VALUES (:n, :p)");
    $stmt->execute(['n'=>$_POST['nombre'], 'p'=>$_POST['precio']]);
    header("Location: productos.php");
    exit;
}

// Obtener productos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Productos";
ob_start();
?>
<h1>Productos (Arepas)</h1>
<div class="card">
    <form method="post" class="grid grid-3">
        <input type="text" name="nombre" placeholder="Nombre producto" required>
        <input type="number" name="precio" placeholder="Precio unitario" required>
        <button class="btn btn-primary">Agregar</button>
    </form>
</div>

<div class="card mt-3">
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th></tr></thead>
        <tbody>
        <?php foreach($productos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td>$<?= number_format($p['precio_unitario'], 0) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
