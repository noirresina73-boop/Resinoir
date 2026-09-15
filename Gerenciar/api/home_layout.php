<?php
require_once __DIR__ . '/../auth.php';

use Controllers\infosController;

include __DIR__ . '/../autoloader.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$acao = $_POST['acao'] ?? '';

if ($acao === 'salvar_home_layout') {
    $bannerProdutoId = isset($_POST['banner_produto_id']) ? (int) $_POST['banner_produto_id'] : 0;
    $novidadesJson = $_POST['novidades'] ?? '';

    $novidadesIds = [];
    $novidadesArray = json_decode($novidadesJson, true);
    if (is_array($novidadesArray)) {
        foreach ($novidadesArray as $item) {
            $id = (int) $item;
            if ($id > 0) {
                $novidadesIds[] = $id;
            }
        }
    }
    $novidadesIds = array_slice($novidadesIds, 0, 3);
    $novidadesJson = json_encode(array_values($novidadesIds));

    $stmt = $pdo->prepare('INSERT INTO configuracoes (chave, valor, atualizado_em) VALUES (:chave, :valor, NOW()) ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()');
    $stmt->execute([':chave' => 'home_banner_produto_id', ':valor' => (string) $bannerProdutoId]);
    $stmt->execute([':chave' => 'home_novidades', ':valor' => $novidadesJson]);

    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'novidades' => $novidadesIds]);
    exit;
}

if ($acao === 'buscar_produto') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['erro' => 'ID inválido']);
        exit;
    }

    $query = $pdo->prepare('SELECT id, nome, valor, capa, estoque, colecao, colecao AS colecao_id FROM produtos WHERE id = :id LIMIT 1');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $produto = $query->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo json_encode(['erro' => 'Produto não encontrado']);
        exit;
    }

    $capaHtml = '';
    if ($produto['capa'] && str_starts_with($produto['capa'], 'svg:')) {
        $capaHtml = ListController::htmlIconePorChave($produto['capa'], 'thumb-admin');
    } elseif ($produto['capa']) {
        $capa = trim((string) $produto['capa']);
        if ($capa !== '' && !preg_match('#^(https?:)?//#', $capa) && !str_starts_with($capa, '../')) {
            $capa = preg_match('#^assets/#', $capa) ? '../' . $capa : (str_starts_with($capa, './') ? '../' . ltrim($capa, './') : '../' . ltrim($capa, './'));
        }
        $capaHtml = '<img class="card-thumb" src="' . htmlspecialchars($capa) . '" alt="' . htmlspecialchars($produto['nome']) . '">';
    }

    $colecaoInfo = null;
    if ((int) ($produto['colecao_id'] ?? 0) > 0) {
        $query = $pdo->prepare('SELECT id, nome, descricao, capa FROM colecao WHERE id = :id LIMIT 1');
        $query->bindValue(':id', (int) $produto['colecao_id'], PDO::PARAM_INT);
        $query->execute();
        $colecaoInfo = $query->fetch(PDO::FETCH_ASSOC);
    }

    header('Content-Type: application/json');
    echo json_encode([
        'sucesso' => true,
        'id' => (int) $produto['id'],
        'nome' => $produto['nome'],
        'valor' => (float) $produto['valor'],
        'capa' => $produto['capa'],
        'capaHtml' => $capaHtml,
        'estoque' => (int) $produto['estoque'],
        'colecaoId' => (int) ($produto['colecao_id'] ?? 0),
        'colecaoInfo' => $colecaoInfo,
    ]);
    exit;
}

if ($acao === 'buscar_produtos_por_nome') {
    $nome = trim($_POST['nome'] ?? '');
    if ($nome === '') {
        echo json_encode(['produtos' => []]);
        exit;
    }

    $query = $pdo->prepare('SELECT id, nome, valor, capa, estoque, colecao FROM produtos WHERE nome LIKE :nome ORDER BY nome ASC LIMIT 20');
    $query->bindValue(':nome', '%' . $nome . '%', PDO::PARAM_STR);
    $query->execute();
    $produtos = $query->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($produtos as $p) {
        $capa = trim((string) ($p['capa'] ?? ''));
        if ($capa !== '' && !preg_match('#^(https?:)?//#', $capa) && !str_starts_with($capa, '../')) {
            $capa = preg_match('#^assets/#', $capa) ? '../' . $capa : (str_starts_with($capa, './') ? '../' . ltrim($capa, './') : '../' . ltrim($capa, './'));
        }
        $result[] = [
            'id' => (int) $p['id'],
            'nome' => $p['nome'],
            'valor' => number_format((float) $p['valor'], 2, ',', '.'),
            'capa' => $p['capa'],
            'capaSrc' => $capa !== '' ? $capa : '',
            'estoque' => (int) ($p['estoque'] ?? 0),
            'colecao' => (int) ($p['colecao'] ?? 0),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['produtos' => $result]);
    exit;
}

if ($acao === 'buscar_produtos_por_ids') {
    $idsJson = $_POST['ids'] ?? '[]';
    $ids = json_decode($idsJson, true);
    if (!is_array($ids) || empty($ids)) {
        echo json_encode(['produtos' => []]);
        exit;
    }

    $ids = array_map('intval', array_filter($ids, 'is_numeric'));
    if (empty($ids)) {
        echo json_encode(['produtos' => []]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, nome, valor, capa, estoque, colecao FROM produtos WHERE id IN ($placeholders) ORDER BY FIELD(id, " . implode(',', $ids) . ')';
    $query = $pdo->prepare($sql);
    $query->execute($ids);
    $produtos = $query->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($produtos as $p) {
        $capa = trim((string) ($p['capa'] ?? ''));
        if ($capa !== '' && !preg_match('#^(https?:)?//#', $capa) && !str_starts_with($capa, '../')) {
            $capa = preg_match('#^assets/#', $capa) ? '../' . $capa : (str_starts_with($capa, './') ? '../' . ltrim($capa, './') : '../' . ltrim($capa, './'));
        }
        $result[] = [
            'id' => (int) $p['id'],
            'nome' => $p['nome'],
            'valor' => number_format((float) $p['valor'], 2, ',', '.'),
            'capa' => $p['capa'],
            'capaSrc' => $capa !== '' ? $capa : '',
            'estoque' => (int) ($p['estoque'] ?? 0),
            'colecao' => (int) ($p['colecao'] ?? 0),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['produtos' => $result]);
    exit;
}

header('Content-Type: application/json');
echo json_encode(['erro' => 'Ação inválida']);
exit;
