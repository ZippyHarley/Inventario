<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location:/arepas-erp2/public/dashboard.php");
    exit;
}
$error = isset($_GET['error']) ? "Usuario o contraseña incorrectos" : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Login | Arepas ERP</title>
  <!-- Si tienes /public/assets/css/app.css lo puedes mantener;
       este CSS inline asegura que se vea bien aunque Tailwind no cargue -->
  <style>
    :root { color-scheme: dark; }
    body {
      margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center;
      background: #0f172a; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: #e2e8f0;
    }
    .card {
      background:#1e293b; width:100%; max-width:420px; padding:28px; border-radius:16px;
      box-shadow: 0 10px 30px rgba(0,0,0,.45);
    }
    h1 { margin:0 0 18px; text-align:center; color:#14b8a6; font-size:26px; font-weight:700; }
    label { display:block; margin:12px 0 6px; font-size:14px; color:#cbd5e1; }
    input {
      width:100%; padding:12px 12px; border-radius:10px; border:1px solid #334155; background:#0b1220;
      color:#e2e8f0; font-size:15px; outline:none; transition:border .15s, box-shadow .15s;
    }
    input:focus { border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.35); }
    button {
      width:100%; margin-top:16px; padding:12px 14px; border:0; border-radius:10px;
      background:#14b8a6; color:white; font-weight:700; cursor:pointer; transition:background .15s, transform .02s;
    }
    button:hover { background:#0ea5a3; }
    button:active { transform:translateY(1px); }
    .extra { margin-top:12px; text-align:center; }
    .extra a { color:#22d3ee; text-decoration:none; font-size:14px; }
    .extra a:hover { text-decoration:underline; }
    .error { color:#f87171; margin-top:10px; text-align:center; font-size:14px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Iniciar sesión</h1>
    <form method="post" action="/arepas-erp2/app/controllers/AuthController.php">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" required />
      <label for="password">Contraseña</label>
      <input id="password" type="password" name="password" required />
      <button type="submit">Ingresar</button>
    </form>
    <div class="extra">
      <a href="/arepas-erp2/public/change_password.php">¿Cambiar contraseña?</a>
    </div>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
