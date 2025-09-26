<?php /* Layout base para todo el sitio */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Arepas ERP' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="/arepas-erp2/public/assets/css/app.css" rel="stylesheet">
</head>
<body>
  <div class="navbar">
    <div class="brand">Arepas ERP</div>
    <div class="nav-right">
      <a href="/arepas-erp2/public/hechuras/index.php">Hechuras</a>
      <a href="/arepas-erp2/public/inversion/index.php">Inversión</a>
      <a href="/arepas-erp2/public/ventas/index.php">Ventas</a>
      <a href="/arepas-erp2/public/ganancias/index.php">Ganancias</a>
      <a href="/arepas-erp2/public/division/index.php">División</a>
      <a href="/arepas-erp2/public/reportes/mensual.php">Reportes</a>
      <a href="/arepas-erp2/public/admin/usuarios.php">Admin</a>
      <a class="logout" href="/arepas-erp2/public/logout.php">Salir</a>
    </div>
  </div>

  <div class="container">
    <?= $content ?? '' ?>
  </div>
</body>
</html>
