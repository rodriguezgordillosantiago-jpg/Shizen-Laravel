<?php
declare(strict_types=1);

require_once __DIR__ . '/../clases/Usuario.php';
require_once __DIR__ . '/../funciones/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../php/login.php');
    exit;
}

// Verificar CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    header('Location: ../php/login.php?error=1');
    exit;
}

function mostrarErrorLogin(): void
{
    header('Location: ../php/login.php?error=1');
    exit;
}

$email    = limpiarTexto($_POST['email']    ?? '');
$password = (string)($_POST['password']    ?? '');

$emailValido = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
if (!$emailValido || strlen($email) > 254 || strlen($password) < 6 || strlen($password) > 255) {
    mostrarErrorLogin();
}

$usuario = Usuario::autenticar($email, $password);
if (!$usuario) {
    mostrarErrorLogin();
}

session_regenerate_id(true);
$_SESSION['id_usuario']     = $usuario['id_usuario'] ?? $usuario['id'] ?? null;
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_email']  = $usuario['email'];
$_SESSION['usuario_rol']    = strtolower(trim($usuario['rol'] ?? 'usuario'));

$rol = $_SESSION['usuario_rol'];
if ($rol === 'negocio' || $rol === 'cocina') {
    header('Location: ../index.php');
} elseif ($rol === 'repartidor') {
    header('Location: http://localhost/shizen_repartidor/');
} else {
    header('Location: ../index.php');
}
exit;
