<?php
require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=resinior', 'root', 'senha');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$acao = $_POST['acao'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$quantidade = (int) ($_POST['quantidade'] ?? 0);

if ($acao !== 'ajustar' || $id <= 0) {
    echo json_encode(['erro' => 'Dados inválidos.']);
    exit;
}

$produto = $pdo->prepare('SELECT estoque FROM produtos WHERE id = :id LIMIT 1');
$produto->execute([':id' => $id]);
$produto = $produto->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo json_encode(['erro' => 'Produto não encontrado.']);
    exit;
}

$novoEstoque = max(0, (int) $produto['estoque'] + $quantidade);

$pdo->prepare('UPDATE produtos SET estoque = :estoque WHERE id = :id')->execute([
    ':estoque' => $novoEstoque,
    ':id' => $id,
]);

echo json_encode(['sucesso' => true, 'estoque' => $novoEstoque]);
