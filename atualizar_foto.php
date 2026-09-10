<?php
session_start();
require_once __DIR__ . '/autoloader.php';
use Controllers\AuthController;

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new AuthController();
$usuario = $controller->buscarPorId((int) $_SESSION['usuario_id']);
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $pasta = __DIR__ . '/assets/usuarios';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $nomeArquivo = uniqid('foto_') . '.' . $extensao;
    move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . '/' . $nomeArquivo);
    $caminho = './assets/usuarios/' . $nomeArquivo;
    $controller->atualizarFoto((int) $_SESSION['usuario_id'], $caminho);
    $_SESSION['usuario_foto'] = $caminho;
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
