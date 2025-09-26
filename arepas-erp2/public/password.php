<?php
require_once __DIR__ . "/../app/core/Database.php";
$pdo = Database::getInstance();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $newPass = $_POST['new_password'];

    $stmt = $pdo->prepare("UPDATE usuarios SET password = :p WHERE email = :e");
    $ok = $stmt->execute([
        'p' => $newPass,  // 🔴 Texto plano solo pruebas
        'e' => $email
    ]);

    if ($ok && $stmt->rowCount() > 0) {
        $msg = "Contraseña actualizada correctamente ✅";
    } else {
        $msg = "No se encontró el usuario ❌";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Cambiar Contraseña</title>
  <link href="/arepas-erp2/public/assets/css/app.css" rel="stylesheet">
</head>
<body class="flex justify-center items-center h-screen bg-gray-900">
  <div class="bg-gray-800 p-6 rounded shadow w-96">
    <h1 class="text-center text-teal-400 text-2xl mb-4">Cambiar contraseña</h1>
    <form method="POST" class="space-y-3">
      <input type="email" name="email" placeholder="Email" required class="w-full">
      <input type="password" name="new_password" placeholder="Nueva contraseña" required class="w-full">
      <button type="submit" class="w-full">Actualizar</button>
    </form>
    <?php if($msg): ?>
      <p class="mt-3 text-center text-yellow-300"><?= $msg ?></p>
    <?php endif; ?>
    <div class="text-center mt-4">
      <a href="login.php" class="text-sm text-teal-300 hover:underline">Volver al login</a>
    </div>
  </div>
</body>
</html>
