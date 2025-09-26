<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location:/arepas-erp2/public/login.php"); exit; }
if (($_SESSION['user']['rol'] ?? 'usuario') !== 'admin') {
  header("Location:/arepas-erp2/public/dashboard.php?error=forbidden");
  exit;
}

require_once __DIR__ . "/../../app/core/Database.php";
$pdo = Database::getInstance();

/* Crear / Editar */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $username = trim($_POST['username'] ?? '');
  $rol = $_POST['rol'] ?? 'usuario';
  $password = $_POST['password'] ?? '';

  if ($id > 0) {
    // Actualizar
    $stmt = $pdo->prepare("UPDATE usuarios SET username=:u, rol=:r WHERE id=:id");
    $stmt->execute(['u'=>$username, 'r'=>$rol, 'id'=>$id]);
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("UPDATE usuarios SET password=:p WHERE id=:id");
      $stmt->execute(['p'=>$hash, 'id'=>$id]);
    }
  } else {
    // Nuevo
    if ($username !== '' && $password !== '') {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("INSERT INTO usuarios(username,password,rol) VALUES(:u,:p,:r)");
      $stmt->execute(['u'=>$username,'p'=>$hash,'r'=>$rol]);
    }
  }
  header("Location: /arepas-erp2/public/admin/usuarios.php");
  exit;
}

/* Eliminar */
if (isset($_GET['del'])) {
  $id = (int)$_GET['del'];
  if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=:id");
    $stmt->execute(['id'=>$id]);
  }
  header("Location: /arepas-erp2/public/admin/usuarios.php");
  exit;
}

/* Listar */
$usuarios = [];
try {
  $usuarios = $pdo->query("SELECT id, username, rol FROM usuarios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
  // Si todavía hay problemas de columnas, muéstralo claro
  die("La tabla 'usuarios' no tiene las columnas esperadas (id, username, password, rol). 
       Corrige la estructura en phpMyAdmin y recarga. Detalle técnico: ".$e->getMessage());
}

$title = "Usuarios";
ob_start();
?>
<h1>Gestión de usuarios</h1>

<div class="card">
  <form method="post" class="grid grid-4">
    <input type="hidden" name="id" id="id">
    <input type="text" name="username" id="username" placeholder="Usuario" required>
    <input type="password" name="password" id="password" placeholder="Contraseña (dejar vacío para no cambiar)">
    <select name="rol" id="rol" required>
      <option value="usuario">Usuario</option>
      <option value="admin">Administrador</option>
    </select>
    <button class="btn btn-primary">Guardar</button>
  </form>
</div>

<div class="card mt-3">
  <h2>Usuarios</h2>
  <table class="table">
    <thead><tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Acciones</th></tr></thead>
    <tbody>
      <?php foreach($usuarios as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= $u['rol'] ?></td>
          <td>
            <button class="btn btn-warning" onclick="editUser('<?= $u['id'] ?>','<?= htmlspecialchars($u['username']) ?>','<?= $u['rol'] ?>')">Editar</button>
            <a href="?del=<?= $u['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function editUser(id, username, rol){
  document.getElementById('id').value = id;
  document.getElementById('username').value = username;
  document.getElementById('rol').value = rol;
  document.getElementById('password').value = '';
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . "/../../app/views/layout.php";
