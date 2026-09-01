<?php
use Controllers\CatalogoAuxController;

include __DIR__ . '/../autoloader.php';
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json; charset=utf-8');

$Controller = new CatalogoAuxController;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($Controller->listarColecoes());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'criar';

    if ($acao === 'excluir') {
        $id = (int) ($_POST['id'] ?? 0);
        $resultado = $Controller->excluirColecao($id);

        if ($resultado === 'em_uso') {
            http_response_code(409);
            echo json_encode(['erro' => 'Essa coleção ainda tem produtos vinculados. Mude a coleção desses produtos antes de excluir.']);
            exit;
        }

        echo json_encode(['sucesso' => true]);
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome === '') {
        http_response_code(400);
        echo json_encode(['erro' => 'Nome é obrigatório']);
        exit;
    }

    $capa = null;
    if (isset($_FILES['capa']) && $_FILES['capa']['error'] == UPLOAD_ERR_OK) {
        $capa = $Controller->salvarImagemCapa($nome, $_FILES['capa']);
    }

    if ($acao === 'editar') {
        $id = (int) ($_POST['id'] ?? 0);
        $Controller->atualizarColecao($id, $nome, $descricao, $capa);
        echo json_encode(['id' => $id, 'nome' => $nome]);
        exit;
    }

    $id = $Controller->criarColecao($nome, $descricao, $capa ?? '');
    echo json_encode(['id' => $id, 'nome' => $nome]);
}