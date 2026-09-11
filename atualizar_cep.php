<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Não autenticado']);
    exit;
}

require_once __DIR__ . '/autoloader.php';
use Controllers\AuthController;

$controller = new AuthController();
$cep = trim((string) ($_POST['cep'] ?? ''));

if ($cep === '') {
    $controller->atualizarCep((int) $_SESSION['usuario_id'], null);
    $_SESSION['usuario_cep'] = null;
    echo json_encode(['sucesso' => true, 'cep' => null]);
    exit;
}

if (!preg_match('/^\d{5}-?\d{3}$/', $cep)) {
    echo json_encode(['sucesso' => false, 'erro' => 'CEP inválido']);
    exit;
}

$controller->atualizarCep((int) $_SESSION['usuario_id'], $cep);
$_SESSION['usuario_cep'] = $cep;
echo json_encode(['sucesso' => true, 'cep' => $cep]);
