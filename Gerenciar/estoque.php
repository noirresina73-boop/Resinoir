<?php
require_once __DIR__ . '/auth.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$produtos = $pdo->query('SELECT id, idPDR, nome, estoque, valor, capa FROM produtos ORDER BY estoque ASC, nome ASC')->fetchAll(PDO::FETCH_ASSOC);
$estoqueTotal = (int) $pdo->query('SELECT COALESCE(SUM(estoque), 0) FROM produtos')->fetchColumn();
$baixoEstoque = array_filter($produtos, fn($produto) => (int)$produto['estoque'] <= 3);
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
    <style>
      .stock-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
      .stock-card { background: rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.22); border-radius:16px; padding:1.2rem; }
      .stock-card .label { text-transform: uppercase; letter-spacing: .12rem; color: var(--bone-dim); font-size: .7rem; }
      .stock-card .value { font-size: 2rem; font-family: 'Cormorant Garamond', serif; margin-top: .4rem; }
      .stock-actions { display:flex; gap:.5rem; }
      .stock-actions .btn { padding: .5rem .8rem; font-size: .65rem; }
      .stock-thumb { width: 54px; height: 54px; object-fit: cover; border-radius: 12px; }
    </style>
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Estoque</a>
        <div class="topbar-actions">
          <a href="./index.php" class="btn btn-outline-secondary">Dashboard</a>
          <a href="./produtos-lista.php" class="btn btn-outline-secondary">Produtos</a>
          <a href="./vendas.php" class="btn btn-outline-success">Vendas</a>
        </div>
      </div>
    </nav>

    <div class="page-shell">
      <div class="admin-header">
        <div>
          <h1 class="admin-title">Controle de estoque</h1>
          <div class="admin-subtitle">itens / reposição / alertas</div>
        </div>
      </div>

      <div class="stock-grid">
        <div class="stock-card">
          <div class="label">Total em estoque</div>
          <div class="value"><?= $estoqueTotal ?></div>
        </div>
        <div class="stock-card">
          <div class="label">Produtos em alerta</div>
          <div class="value"><?= count($baixoEstoque) ?></div>
        </div>
        <div class="stock-card">
          <div class="label">Itens cadastrados</div>
          <div class="value"><?= count($produtos) ?></div>
        </div>
      </div>

      <div class="panel p-3 p-md-4">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Produto</th>
                <th>Estoque</th>
                <th>Valor</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($produtos)): ?>
                <tr><td colspan="4" class="empty-state">Nenhum produto cadastrado.</td></tr>
              <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                  <tr class="<?= (int) $produto['estoque'] <= 3 ? 'table-danger' : '' ?>">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($produto['capa'])): ?>
                          <?php $capaEstoque = trim((string) ($produto['capa'] ?? '')); if ($capaEstoque !== '' && !preg_match('#^(https?:)?//#', $capaEstoque) && !str_starts_with($capaEstoque, '../')) { $capaEstoque = preg_match('#^assets/#', $capaEstoque) ? '../' . $capaEstoque : (str_starts_with($capaEstoque, './') ? '../' . ltrim($capaEstoque, './') : '../' . ltrim($capaEstoque, './')); } ?>
                          <img class="stock-thumb" src="<?= htmlspecialchars($capaEstoque) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                        <?php else: ?>
                          <div class="stock-thumb d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,.03); color: var(--bone-dim); font-size: .7rem;">Sem img</div>
                        <?php endif; ?>
                        <div>
                          <div><strong><?= htmlspecialchars($produto['nome']) ?></strong></div>
                          <small class="text-muted"><?= htmlspecialchars($produto['idPDR'] ?? '') ?></small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge rounded-pill <?= (int) $produto['estoque'] <= 3 ? 'bg-danger' : 'bg-success' ?> px-3 py-2">
                        <?= (int) $produto['estoque'] ?> un.
                      </span>
                    </td>
                    <td>R$ <?= number_format((float) ($produto['valor'] ?? 0), 2, ',', '.') ?></td>
                    <td>
                      <div class="stock-actions">
                        <button type="button" class="btn btn-outline-secondary" onclick="ajustarEstoque(<?= (int) $produto['id'] ?>, 1)">+1</button>
                        <button type="button" class="btn btn-outline-danger" onclick="ajustarEstoque(<?= (int) $produto['id'] ?>, -1)">-1</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script>
      async function ajustarEstoque(id, quantidade) {
        const form = new FormData();
        form.append('acao', 'ajustar');
        form.append('id', id);
        form.append('quantidade', quantidade);

        const response = await fetch('./api/estoque.php', { method: 'POST', body: form });
        const dados = await response.json();

        if (dados.erro) {
          alert(dados.erro);
          return;
        }

        window.location.reload();
      }
    </script>
  </body>
</html>
