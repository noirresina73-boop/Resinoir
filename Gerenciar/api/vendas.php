<?php
require_once __DIR__ . '/../auth.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$garantirEstruturaVendas = function () use ($pdo): void {
    $colunasVendas = $pdo->query('SHOW COLUMNS FROM vendas')->fetchAll(PDO::FETCH_COLUMN);
    $colunasItens = $pdo->query('SHOW COLUMNS FROM venda_itens')->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('cliente', $colunasVendas, true)) {
        $pdo->exec('ALTER TABLE vendas ADD COLUMN cliente VARCHAR(255) NOT NULL DEFAULT ""');
    }

    if (in_array('produto_id', $colunasVendas, true)) {
        $pdo->exec('ALTER TABLE vendas MODIFY produto_id INT NULL DEFAULT NULL');
    }
    if (in_array('produto_nome', $colunasVendas, true)) {
        $pdo->exec('ALTER TABLE vendas MODIFY produto_nome VARCHAR(255) NULL DEFAULT NULL');
    }
    if (in_array('quantidade', $colunasVendas, true)) {
        $pdo->exec('ALTER TABLE vendas MODIFY quantidade INT NULL DEFAULT NULL');
    }
    if (in_array('valor_unitario', $colunasVendas, true)) {
        $pdo->exec('ALTER TABLE vendas MODIFY valor_unitario DECIMAL(10,2) NULL DEFAULT NULL');
    }

    if (!in_array('venda_id', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN venda_id INT NOT NULL DEFAULT 0');
    }
    if (!in_array('produto_id', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN produto_id INT NOT NULL DEFAULT 0');
    }
    if (!in_array('produto_nome', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN produto_nome VARCHAR(255) NOT NULL DEFAULT ""');
    }
    if (!in_array('quantidade', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN quantidade INT NOT NULL DEFAULT 1');
    }
    if (!in_array('valor_unitario', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN valor_unitario DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
    if (!in_array('valor_total', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN valor_total DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
    if (!in_array('custo_total', $colunasItens, true)) {
        $pdo->exec('ALTER TABLE venda_itens ADD COLUMN custo_total DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente VARCHAR(255) NOT NULL,
        desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
        acrescimo DECIMAL(10,2) NOT NULL DEFAULT 0,
        valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        custo_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        observacao TEXT NULL,
        data_venda DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS venda_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        venda_id INT NOT NULL,
        produto_id INT NOT NULL,
        produto_nome VARCHAR(255) NOT NULL,
        quantidade INT NOT NULL DEFAULT 1,
        valor_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
        valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        custo_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE
    )");
};

$garantirEstruturaVendas();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['acao'] ?? '') === 'excluir') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0) {
        $pdo->prepare('DELETE FROM venda_itens WHERE venda_id = :id')->execute([':id' => $id]);
        $pdo->prepare('DELETE FROM vendas WHERE id = :id')->execute([':id' => $id]);
    }
    header('Location: ../vendas.php?msg=' . urlencode('Venda excluída com sucesso.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vendas.php');
    exit;
}

$cliente = trim((string) ($_POST['cliente'] ?? ''));
$desconto = (float) ($_POST['desconto'] ?? 0);
$acrescimo = (float) ($_POST['acrescimo'] ?? 0);
$observacao = trim((string) ($_POST['observacao'] ?? ''));
$itensJson = trim((string) ($_POST['itens_json'] ?? ''));
$itens = json_decode($itensJson, true);

if ($cliente === '' || !is_array($itens) || empty($itens)) {
    header('Location: ../vendas.php?msg=' . urlencode('Cliente e pelo menos um produto são obrigatórios.'));
    exit;
}

$totalVenda = 0.0;
$totalCusto = 0.0;
$itensValidos = [];

foreach ($itens as $item) {
    $produtoId = (int) ($item['id'] ?? 0);
    $quantidade = max(1, (int) ($item['quantidade'] ?? 1));

    if ($produtoId <= 0) {
        continue;
    }

    $produto = $pdo->prepare('SELECT id, nome, valor, custo, estoque FROM produtos WHERE id = :id LIMIT 1');
    $produto->execute([':id' => $produtoId]);
    $produto = $produto->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        continue;
    }

    $valorUnitario = (float) ($item['valor'] ?? $produto['valor'] ?? 0);
    $valorLinha = $valorUnitario * $quantidade;
    $custoLinha = ((float) ($produto['custo'] ?? 0)) * $quantidade;

    $itensValidos[] = [
        'id' => $produtoId,
        'nome' => $produto['nome'],
        'quantidade' => $quantidade,
        'valor_unitario' => $valorUnitario,
        'valor_total' => $valorLinha,
        'custo_total' => $custoLinha,
        'estoque' => (int) ($produto['estoque'] ?? 0),
    ];

    $totalVenda += $valorLinha;
    $totalCusto += $custoLinha;
}

if (empty($itensValidos)) {
    header('Location: ../vendas.php?msg=' . urlencode('Nenhum produto válido foi selecionado.'));
    exit;
}

$totalVenda = $totalVenda - $desconto + $acrescimo;

$insertVenda = $pdo->prepare('INSERT INTO vendas (cliente, desconto, acrescimo, valor_total, custo_total, observacao, data_venda) VALUES (:cliente, :desconto, :acrescimo, :valor_total, :custo_total, :observacao, NOW())');
$insertVenda->execute([
    ':cliente' => $cliente,
    ':desconto' => $desconto,
    ':acrescimo' => $acrescimo,
    ':valor_total' => $totalVenda,
    ':custo_total' => $totalCusto,
    ':observacao' => $observacao,
]);
$vendaId = (int) $pdo->lastInsertId();

foreach ($itensValidos as $item) {
    $quantidade = max(1, (int) $item['quantidade']);
    $novoEstoque = max(0, (int) $item['estoque'] - $quantidade);

    $pdo->prepare('INSERT INTO venda_itens (venda_id, produto_id, produto_nome, quantidade, valor_unitario, valor_total, custo_total) VALUES (:venda_id, :produto_id, :produto_nome, :quantidade, :valor_unitario, :valor_total, :custo_total)')->execute([
        ':venda_id' => $vendaId,
        ':produto_id' => (int) $item['id'],
        ':produto_nome' => $item['nome'],
        ':quantidade' => $quantidade,
        ':valor_unitario' => $item['valor_unitario'],
        ':valor_total' => $item['valor_total'],
        ':custo_total' => $item['custo_total'],
    ]);

    $pdo->prepare('UPDATE produtos SET estoque = :estoque WHERE id = :id')->execute([
        ':estoque' => $novoEstoque,
        ':id' => (int) $item['id'],
    ]);
}

header('Location: ../vendas.php?msg=' . urlencode('Venda registrada com sucesso.'));
exit;
