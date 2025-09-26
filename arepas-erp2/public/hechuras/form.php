<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

$id = $_GET['id'] ?? null;
$hechura = ['fecha'=>date('Y-m-d'),'nombre'=>''];

if ($id) {
  $stmt = $pdo->prepare("SELECT * FROM hechuras WHERE id=:id");
  $stmt->execute(['id'=>$id]);
  $hechura = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $data = ['f'=>$_POST['fecha'],'n'=>$_POST['nombre']];
  if ($id) {
    $stmt = $pdo->prepare("UPDATE hechuras SET fecha=:f, nombre=:n WHERE id=:id");
    $stmt->execute(['f'=>$data['f'],'n'=>$data['n'],'id'=>$id]);
  } else {
    $stmt = $pdo->prepare("INSERT INTO hechuras (fecha, nombre) VALUES (:f, :n)");
    $stmt->execute($data);
    $id = $pdo->lastInsertId();
  }
  header("Location: /arepas-erp2/public/hechuras/ingredientes.php?hechura=".$id);
  exit;
}

$title = $id ? "Editar Hechura #$id" : "Nueva Hechura";
ob_start();
?>
<h1><?= $title ?></h1>

<div class="card">
  <form method="post" class="grid grid-3">
    <div>
      <label>Fecha</label>
      <input type="date" name="fecha" value="<?= $hechura['fecha'] ?>" required>
    </div>
    <div>
      <label>Nombre / Lote</label>
      <input name="nombre" value="<?= htmlspecialchars($hechura['nombre'] ?? '') ?>" placeholder="Ej: Lote Queso #15">
    </div>
    <div style="align-self:end">
      <button class="btn btn-primary" type="submit">Guardar</button>
      <a class="btn btn-ghost" href="/arepas-erp2/public/hechuras/index.php">Volver</a>
    </div>
  </form>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
