<?php
session_start();
require_once __DIR__ . "/../app/core/Database.php";
$pdo = Database::getInstance();

$ok = $msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $new  = trim($_POST['new_password'] ?? '');

    if ($email !== '' && $new !== '') {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = :p WHERE email = :e");
        $stmt->execute(['p' => $hash, 'e' => $email]);
        if ($stmt->rowCount() > 0) {
            $ok  = "Contraseña actualizada correctamente.";
        } else {
            $msg = "No se encontró el usuario con ese email.";
        }
    } else {
        $msg = "Completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Cambiar contraseña | Arepas ERP</title>
  <style>
    :root { color-scheme: dark; }
    body {
      margin:0; height: 100vh; display:flex; align-items:center; justify-content:center;
      background:#0f172a; color:#e2e8f0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }
    .card { background:#1e293b; width:100%; max-width:460px; padding:28px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.45); }
    h1 { margin:0 0 18px; text-align:center; color:#22d3ee; font-size:24px; font-weight:700; }
    label { display:block; margin:12px 0 6px; font-size:14px; color:#cbd5e1; }
    input { width:100%; padding:12px; border-radius:10px; border:1px solid #334155; background:#0b1220; color:#e2e8f0; outline:none; }
    input:focus { border-color:#22d3ee; box-shadow:0 0 0 3px rgba(34,211,238,.35); }
    button { width:100%; margin-top:16px; padding:12px; border:0; border-radius:10px; background:#22d3ee; color:#0b1220; font-weight:700; cursor:pointer; }
    .msg-ok { margin-top:10px; color:#86efac; text-align:center; }
    .msg-err { margin-top:10px; color:#f87171; text-align:center; }
    .extra { text-align:center; margin-top:10px; }
    .extra a { color:#22d3ee; text-decoration:none; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Cambiar contraseña</h1>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required />
      <label>Nueva contraseña</label>
      <input type="password" name="new_password" required />
      <button type="submit">Actualizar</button>
    </form>
    <?php if ($ok): ?><div class="msg-ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
    <?php if ($msg): ?><div class="msg-err"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="extra"><a href="/arepas-erp2/public/login.php">Volver al login</a></div>
  </div>
</body>
</html>
