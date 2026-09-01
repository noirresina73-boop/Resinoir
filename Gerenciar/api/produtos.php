<?php
require_once __DIR__ . '/../auth.php';

use Controllers\infosController;
include __DIR__ . '/../autoloader.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$acao = $_POST['acao'] ?? '';
$id = (int) ($_POST['id'] ?? 0);

if ($acao !== 'excluir' || $id <= 0) {
    echo json_encode(['erro' => 'Ação ou ID inválidos.']);
    exit;
}

$Controller = new infosController();
if ($Controller->excluir($id)) {
    echo json_encode(['sucesso' => true]);
    exit;
}

echo json_encode(['erro' => 'Não foi possível excluir o produto.']);
