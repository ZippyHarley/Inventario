<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$rol = $_SESSION['user']['rol'] ?? 'usuario';

/* --- (1) LISTA DE INGREDIENTES --- */
$ingredientes = $pdo->query("SELECT id, nombre, costo_unitario, unidad FROM ingredientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

/* --- (2) REGISTRAR INVERSIÓN: SIEMPRE USA EL PRECIO VIGENTE DEL INGREDIENTE --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['__accion']) && $_POST['__accion'] === 'registrar_inversion') {
    $ingrediente_id = (int)($_POST['ingrediente_id'] ?? 0);
    $cantidad = (float)($_POST['cantidad'] ?? 0);

    // Trae el precio actual del ingrediente
    $stmt = $pdo->prepare("SELECT costo_unitario FROM ingredientes WHERE id=:id");
    $stmt->execute(['id'=>$ingrediente_id]);
    $ing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ing && $cantidad > 0) {
        $stmt = $pdo->prepare("INSERT INTO inversiones (ingrediente_id, cantidad, costo_unitario, fecha) 
                               VALUES (:i, :c, :p, NOW())");
        $stmt->execute([
            'i'=>$ingrediente_id,
            'c'=>$cantidad,
            'p'=>$ing['costo_unitario']
        ]);
    }
    header("Location: /arepas-erp2/public/inversion/index.php");
    exit;
}

/* --- (3) CREAR/ACTUALIZAR INGREDIENTE (SOLO ADMIN) --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['__accion']) && $_POST['__accion'] === 'upsert_ingrediente' && $rol === 'admin') {
    $nombre = trim($_POST['nuevo_nombre'] ?? '');
    $precio = (float)($_POST['nuevo_precio'] ?? 0);
    $unidad = trim($_POST['nuevo_unidad'] ?? 'unidad');

    if ($nombre !== '' && $precio >= 0) {
        // Asegura índice único por nombre (si ya existe, ignora el error)
        try { $pdo->exec("CREATE UNIQUE INDEX uq_ingredientes_nombre ON ingredientes(nombre)"); } catch (Throwable $e) {}

        // UPSERT por nombre
        $sql = "INSERT INTO ingredientes(nombre, costo_unitario, unidad)
                VALUES(:n, :p, :u)
                ON DUPLICATE KEY UPDATE
                  costo_unitario = VALUES(costo_unitario),
                  unidad = VALUES(unidad)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['n'=>$nombre, 'p'=>$precio, 'u'=>$unidad]);
    }
    header("Location: /arepas-erp2/public/inversion/index.php");
    exit;
}

/* --- (4) HISTORIAL DE INVERSIONES --- */
$sql = "SELECT inv.id, inv.fecha, ing.nombre, inv.cantidad, inv.costo_unitario,
               (inv.cantidad * inv.costo_unitario) AS total
        FROM inversiones inv
        LEFT JOIN ingredientes ing ON ing.id = inv.ingrediente_id
        ORDER BY inv.fecha DESC, inv.id DESC";
$inversiones = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$total = 0; foreach($inversiones as $r){ $total += $r['total']; }

$title = "Inversión";
ob_start();
?>
<h1>Inversión</h1>

<div class="card">
  <h2>Registrar inversión</h2>
  <form method="post" class="grid grid-4">
    <input type="hidden" name="__accion" value="registrar_inversion">
    <select id="ingrediente" name="ingrediente_id" required>
      <option value="">-- Seleccione ingrediente --</option>
      <?php foreach($ingredientes as $ing): ?>
        <option 
          value="<?= $ing['id'] ?>"
          data-precio="<?= htmlspecialchars($ing['costo_unitario']) ?>"
          data-unidad="<?= htmlspecialchars($ing['unidad']) ?>"
        >
          <?= htmlspecialchars($ing['nombre']) ?> (<?= htmlspecialchars($ing['unidad']) ?>) - $<?= number_format($ing['costo_unitario'],2) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="cantidad" id="cantidad" placeholder="Cantidad" step="0.01" min="0" required>
    <input type="text" id="costo_mostrado" placeholder="Costo unitario" readonly>
    <button class="btn btn-primary">Agregar</button>
  </form>
  <small>El costo unitario se toma automáticamente del ingrediente vigente.</small>
</div>

<?php if ($rol === 'admin'): ?>
<div class="card mt-3">
  <h2>Nuevo/Actualizar ingrediente (solo admin)</h2>
  <form method="post" class="grid grid-4">
    <input type="hidden" name="__accion" value="upsert_ingrediente">
    <input type="text" name="nuevo_nombre" placeholder="Nombre (ej. Maíz)" required>
    <input type="number" name="nuevo_precio" placeholder="Precio predeterminado" step="0.01" min="0" required>
    <input type="text" name="nuevo_unidad" placeholder="Unidad (kg, libra, unidad...)" value="unidad">
    <button class="btn btn-warning">Guardar</button>
  </form>
  <small>Si escribes un nombre que ya existe, se <b>actualiza</b> su precio y unidad (no se duplica).</small>
</div>
<?php endif; ?>

<div class="card mt-3">
  <h2>Historial</h2>
  <table class="table">
    <thead><tr>
      <th>Fecha</th><th>Ingrediente</th><th>Cantidad</th><th>Costo unitario</th><th>Total</th>
    </tr></thead>
    <tbody>
      <?php foreach($inversiones as $r): ?>
        <tr>
          <td><?= $r['fecha'] ?></td>
          <td><?= htmlspecialchars($r['nombre']) ?></td>
          <td><?= $r['cantidad'] ?></td>
          <td>$<?= number_format($r['costo_unitario'],2) ?></td>
          <td>$<?= number_format($r['total'],2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="4" class="right">Total</th>
        <th>$<?= number_format($total,2) ?></th>
      </tr>
    </tbody>
  </table>
</div>

<script>
// Rellena el costo unitario visible al cambiar de ingrediente
const sel = document.getElementById('ingrediente');
const costo = document.getElementById('costo_mostrado');
sel?.addEventListener('change', e => {
  const opt = sel.options[sel.selectedIndex];
  costo.value = opt?.dataset?.precio ? Number(opt.dataset.precio).toFixed(2) : '';
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
