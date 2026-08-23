<?php
// api/colecao.php
use Controllers\CatalogoAuxController;

include __DIR__ . '/../autoloader.php';
header('Content-Type: application/json; charset=utf-8');

$Controller = new CatalogoAuxController;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($Controller->listarColecoes());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome é obrigatório']);
        exit;
    }

    $id = $Controller->criarColecao($nome, $descricao);
    echo json_encode(['id' => $id, 'nome' => $nome]);
}