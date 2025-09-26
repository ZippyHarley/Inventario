<?php
session_start();

// 🔹 Ya NO llamamos a Usuario.php porque no existe ni lo necesitamos
require_once __DIR__ . '/../core/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        header("Location: /arepas-erp2/public/login.php?error=1");
        exit;
    }

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            header("Location: /arepas-erp2/public/login.php?error=1");
            exit;
        }

        $stored = $user['password'];
        $isValid = false;

        // Caso 1: contraseña ya hasheada
        if (password_get_info($stored)['algo'] !== 0) {
            $isValid = password_verify($password, $stored);
        } else {
            // Caso 2: contraseña guardada en texto plano
            if ($password === $stored) {
                $isValid = true;
                // 🔹 Se migra automáticamente a hash seguro
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $up = $pdo->prepare("UPDATE usuarios SET password = :p WHERE id = :id");
                $up->execute(['p' => $newHash, 'id' => $user['id']]);
            }
        }

        if ($isValid) {
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'email' => $user['email'],
                'rol'   => $user['rol'] ?? 'user'
            ];
            header("Location: /arepas-erp2/public/dashboard.php");
            exit;
        } else {
            header("Location: /arepas-erp2/public/login.php?error=1");
            exit;
        }
    } catch (Exception $e) {
        error_log("Auth error: " . $e->getMessage());
        header("Location: /arepas-erp2/public/login.php?error=1");
        exit;
    }
}

header("Location: /arepas-erp2/public/login.php");
exit;
