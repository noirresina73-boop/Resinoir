<?php
require_once __DIR__ . '/auth.php';

include __DIR__ . '/autoloader.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    produto_nome VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    valor_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
    acrescimo DECIMAL(10,2) NOT NULL DEFAULT 0,
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    custo_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    cliente VARCHAR(255) NOT NULL,
    observacao TEXT NULL,
    data_venda DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

$campoConfiguracao = static function (string $chave, string $padrao = '') use ($pdo): string {
    $valor = $pdo->prepare('SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1');
    $valor->execute([':chave' => $chave]);
    $valor = $valor->fetchColumn();
    return $valor !== false && $valor !== null ? (string) $valor : $padrao;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'salvar_frete') {
    $precoGasolina = trim((string) ($_POST['preco_gasolina'] ?? ''));
    $cepOrigem = trim((string) ($_POST['cep_origem'] ?? '85506290'));

    foreach ([
        ['preco_gasolina', $precoGasolina !== '' ? $precoGasolina : '5.80'],
        ['cep_origem', $cepOrigem !== '' ? preg_replace('/\D+/', '', $cepOrigem) : '85506290'],
    ] as [$chave, $valor]) {
        $stmt = $pdo->prepare('INSERT INTO configuracoes (chave, valor, atualizado_em) VALUES (:chave, :valor, NOW()) ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()');
        $stmt->execute([':chave' => $chave, ':valor' => $valor]);
    }
    header('Location: ./index.php?msg=' . urlencode('Configurações de frete atualizadas com sucesso.'));
    exit;
}

$precoGasolina = (float) $campoConfiguracao('preco_gasolina', '5.80');
$cepOrigem = $campoConfiguracao('cep_origem', '85506290');

$produtosTotais = (int) $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$estoqueTotal = (int) $pdo->query('SELECT COALESCE(SUM(estoque), 0) FROM produtos')->fetchColumn();
$valorTotal = $pdo->query('SELECT COALESCE(SUM(valor * estoque), 0) FROM produtos')->fetchColumn();
$totaisVendas = $pdo->query('SELECT COUNT(*) AS total_vendas, COALESCE(SUM(valor_total), 0) AS receita, COALESCE(SUM(custo_total), 0) AS custo FROM vendas')->fetch(PDO::FETCH_ASSOC);
$ultimasVendas = $pdo->query('SELECT * FROM vendas ORDER BY data_venda DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
$mensagem = !empty($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
    <style>
      .dashboard-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1rem; }
      .metric { background: rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.22); border-radius:16px; padding:1.2rem; }
      .metric .label { text-transform: uppercase; letter-spacing: .12rem; color: var(--bone-dim); font-size: .72rem; }
      .metric .value { font-size: 2rem; font-family: 'Cormorant Garamond', serif; margin-top: .5rem; }
    </style>
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Administração</a>
        <div class="topbar-actions">
          <a href="./produtos-lista.php" class="btn btn-outline-secondary">Produtos</a>
          <a href="./categorias-lista.php" class="btn btn-outline-secondary">Categorias</a>
          <a href="./colecoes-lista.php" class="btn btn-outline-secondary">Coleções</a>
          <a href="./vendas.php" class="btn btn-outline-success">Vendas</a>
        </div>
      </div>
    </nav>

    <div class="page-shell">
      <div class="admin-header">
        <div>
          <h1 class="admin-title">Resumo geral</h1>
          <div class="admin-subtitle">visão do negócio</div>
        </div>
      </div>

      <?php if ($mensagem): ?>
        <div class="alert alert-success mt-3" role="alert"><?= $mensagem ?></div>
      <?php endif; ?>

      <div class="dashboard-grid">
        <div class="metric">
          <div class="label">Produtos</div>
          <div class="value"><?= $produtosTotais ?></div>
        </div>
        <div class="metric">
          <div class="label">Estoque total</div>
          <div class="value"><?= $estoqueTotal ?></div>
        </div>
        <div class="metric">
          <div class="label">Valor em catálogo</div>
          <div class="value">R$ <?= number_format((float) $valorTotal, 2, ',', '.') ?></div>
        </div>
        <div class="metric">
          <div class="label">Vendas</div>
          <div class="value"><?= (int) ($totaisVendas['total_vendas'] ?? 0) ?></div>
        </div>
        <div class="metric">
          <div class="label">Receita</div>
          <div class="value">R$ <?= number_format((float) ($totaisVendas['receita'] ?? 0), 2, ',', '.') ?></div>
        </div>
        <div class="metric">
          <div class="label">Lucro</div>
          <div class="value">R$ <?= number_format((float) (($totaisVendas['receita'] ?? 0) - ($totaisVendas['custo'] ?? 0)), 2, ',', '.') ?></div>
        </div>
      </div>

      <div class="panel mt-4 p-3 p-md-4">
        <h2 class="admin-title" style="font-size: 2rem;">Configuração de frete</h2>
        <form method="post" action="./api/configuracoes.php" class="row g-3 mt-2">
          <input type="hidden" name="acao" value="salvar_frete">
          <div class="col-md-6">
            <label class="form-label">Preço da gasolina (R$/L)</label>
            <input type="number" step="0.01" min="0" name="preco_gasolina" class="form-control" value="<?= number_format($precoGasolina, 2, '.', '') ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">CEP de origem</label>
            <input type="text" name="cep_origem" class="form-control" value="<?= htmlspecialchars($cepOrigem) ?>" maxlength="8" required>
          </div>
          <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-outline-success">Salvar configuração</button>
          </div>
        </form>
      </div>

      <div class="panel mt-4 p-3 p-md-4">
        <h2 class="admin-title" style="font-size: 2rem;">Últimas vendas</h2>
        <div class="table-responsive mt-3">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Produto</th>
                <th>Total</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($ultimasVendas)): ?>
                <tr><td colspan="4" class="empty-state">Nenhuma venda registrada.</td></tr>
              <?php else: ?>
                <?php foreach ($ultimasVendas as $v): ?>
                  <tr>
                    <td><?= htmlspecialchars($v['cliente']) ?></td>
                    <td><?= htmlspecialchars($v['produto_nome']) ?></td>
                    <td>R$ <?= number_format((float) $v['valor_total'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>
