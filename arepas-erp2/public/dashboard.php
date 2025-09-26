<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

$title = "Dashboard";
ob_start();
?>
<div class="grid grid-3">
  <div class="card">
    <h2>Bienvenido</h2>
    <p>Usuario: <b><?= htmlspecialchars($_SESSION['user']['email']) ?></b></p>
    <p class="mt-2">Usa el menú superior para navegar.</p>
  </div>
  <div class="card">
    <h2>Atajos</h2>
    <p class="mt-2">
      <a class="btn btn-primary" href="/arepas-erp2/public/hechuras/index.php">Hechuras</a>
      <a class="btn btn-ghost" href="/arepas-erp2/public/ventas/index.php">Ventas</a>
    </p>
  </div>
  <div class="card">
    <h2>Reportes</h2>
    <p class="mt-2">
      <a class="btn btn-ghost" href="/arepas-erp2/public/reportes/mensual.php">Mensual</a>
      <a class="btn btn-ghost" href="/arepas-erp2/public/reportes/anual.php">Anual</a>
    </p>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../app/views/layout.php";
