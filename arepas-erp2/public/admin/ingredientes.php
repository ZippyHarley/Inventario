<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
if ($_SESSION['user']['rol'] !== 'admin') { 
    header("Location:/arepas-erp2/public/dashboard.php?error=forbidden"); 
    exit; 
}

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

// Crear o editar ingrediente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        // Actualizar
        $stmt = $pdo->prepare("UPDATE ingredientes SET nombre=:n, costo_unitario=:p, unidad=:u WHERE id=:id");
        $stmt->execute([
            'n'=>$_POST['nombre'], 
            'p'=>$_POST['precio'], 
            'u'=>$_POST['unidad'], 
            'id'=>$_POST['id']
        ]);
    } else {
        // Nuevo
        $stmt = $pdo->prepare("INSERT INTO ingredientes (nombre, costo_unitario, unidad) VALUES (:n, :p, :u)");
        $stmt->execute([
            'n'=>$_POST['nombre'], 
            'p'=>$_POST['precio'], 
            'u'=>$_POST['unidad']
        ]);
    }
    header("Location: ingredientes.php");
    exit;
}

// Eliminar ingrediente
if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("DELETE FROM ingredientes WHERE id=:id");
    $stmt->execute(['id'=>$_GET['del']]);
    header("Location: ingredientes.php");
    exit;
}

// Obtener lista
$stmt = $pdo->query("SELECT * FROM ingredientes ORDER BY id DESC");
$ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = "Ingredientes";
ob_start();
?>
<h1>Gestión de Ingredientes</h1>

<div class="card">
    <form method="post" class="grid grid-4">
        <input type="hidden" name="id" id="id">
        <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
        <input type="number" name="precio" id="precio" placeholder="Costo unitario" step="0.01" required>
        <input type="text" name="unidad" id="unidad" placeholder="Unidad (kg, libra...)" value="unidad">
        <button class="btn btn-primary">Guardar</button>
    </form>
</div>

<div class="card mt-3">
    <h2>Lista de Ingredientes</h2>
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Unidad</th><th>Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach($ingredientes as $i): ?>
            <tr>
                <td><?= $i['id'] ?></td>
                <td><?= htmlspecialchars($i['nombre']) ?></td>
                <td>$<?= number_format($i['costo_unitario'],2) ?></td>
                <td><?= htmlspecialchars($i['unidad']) ?></td>
                <td>
                    <button class="btn btn-warning" 
                        onclick="editarIngrediente('<?= $i['id'] ?>','<?= htmlspecialchars($i['nombre']) ?>','<?= $i['costo_unitario'] ?>','<?= htmlspecialchars($i['unidad']) ?>')">
                        Editar
                    </button>
                    <a href="?del=<?= $i['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar ingrediente?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editarIngrediente(id,nombre,precio,unidad){
    document.getElementById('id').value=id;
    document.getElementById('nombre').value=nombre;
    document.getElementById('precio').value=precio;
    document.getElementById('unidad').value=unidad;
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
