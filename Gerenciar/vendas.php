<?php
require_once __DIR__ . '/auth.php';

$BD = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$BD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$garantirEstruturaVendas = function () use ($BD): void {
    $colunasVendas = $BD->query('SHOW COLUMNS FROM vendas')->fetchAll(PDO::FETCH_COLUMN);
    $colunasItens = $BD->query('SHOW COLUMNS FROM venda_itens')->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('cliente', $colunasVendas, true)) {
        $BD->exec('ALTER TABLE vendas ADD COLUMN cliente VARCHAR(255) NOT NULL DEFAULT ""');
    }

    if (in_array('produto_id', $colunasVendas, true)) {
        $BD->exec('ALTER TABLE vendas MODIFY produto_id INT NULL DEFAULT NULL');
    }
    if (in_array('produto_nome', $colunasVendas, true)) {
        $BD->exec('ALTER TABLE vendas MODIFY produto_nome VARCHAR(255) NULL DEFAULT NULL');
    }
    if (in_array('quantidade', $colunasVendas, true)) {
        $BD->exec('ALTER TABLE vendas MODIFY quantidade INT NULL DEFAULT NULL');
    }
    if (in_array('valor_unitario', $colunasVendas, true)) {
        $BD->exec('ALTER TABLE vendas MODIFY valor_unitario DECIMAL(10,2) NULL DEFAULT NULL');
    }

    if (!in_array('venda_id', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN venda_id INT NOT NULL DEFAULT 0');
    }
    if (!in_array('produto_id', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN produto_id INT NOT NULL DEFAULT 0');
    }
    if (!in_array('produto_nome', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN produto_nome VARCHAR(255) NOT NULL DEFAULT ""');
    }
    if (!in_array('quantidade', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN quantidade INT NOT NULL DEFAULT 1');
    }
    if (!in_array('valor_unitario', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN valor_unitario DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
    if (!in_array('valor_total', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN valor_total DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
    if (!in_array('custo_total', $colunasItens, true)) {
        $BD->exec('ALTER TABLE venda_itens ADD COLUMN custo_total DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    $BD->exec("CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente VARCHAR(255) NOT NULL,
        desconto DECIMAL(10,2) NOT NULL DEFAULT 0,
        acrescimo DECIMAL(10,2) NOT NULL DEFAULT 0,
        valor_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        custo_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        observacao TEXT NULL,
        data_venda DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $BD->exec("CREATE TABLE IF NOT EXISTS venda_itens (
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
$BD->exec("CREATE TABLE IF NOT EXISTS clientes (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL UNIQUE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$colunasProdutos = $BD->query('SHOW COLUMNS FROM produtos')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('custo', $colunasProdutos, true)) {
    $BD->exec('ALTER TABLE produtos ADD COLUMN custo DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER valor');
}

$produtos = $BD->query('SELECT id, idPDR, nome, valor, custo, estoque, capa FROM produtos ORDER BY nome ASC')->fetchAll(PDO::FETCH_ASSOC);
$clientes = $BD->query('SELECT id, nome FROM clientes ORDER BY nome ASC')->fetchAll(PDO::FETCH_ASSOC);
$vendaEditando = null;
if (!empty($_GET['editar'])) {
  $consultaVenda = $BD->prepare('SELECT * FROM vendas WHERE id = :id');
  $consultaVenda->execute([':id' => (int) $_GET['editar']]);
  $vendaEditando = $consultaVenda->fetch(PDO::FETCH_ASSOC) ?: null;
  if ($vendaEditando) {
    $consultaItens = $BD->prepare('SELECT vi.*, p.capa FROM venda_itens vi LEFT JOIN produtos p ON p.id = vi.produto_id WHERE vi.venda_id = :id');
    $consultaItens->execute([':id' => (int) $_GET['editar']]);
    $vendaEditando['itens'] = $consultaItens->fetchAll(PDO::FETCH_ASSOC);
  }
}
$totais = $BD->query('SELECT COUNT(*) AS total_vendas, COALESCE(SUM(valor_total), 0) AS total_recebido, COALESCE(SUM(custo_total), 0) AS total_custo FROM vendas')->fetch(PDO::FETCH_ASSOC);
$listaVendas = $BD->query('SELECT v.*, GROUP_CONCAT(CONCAT(vi.quantidade, "x ", vi.produto_nome) SEPARATOR ", ") AS itens FROM vendas v LEFT JOIN venda_itens vi ON vi.venda_id = v.id GROUP BY v.id ORDER BY v.data_venda DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);

$resolveImagemProduto = static function ($capa) {
    if (empty($capa) || !is_string($capa)) {
        return '';
    }

    $capa = trim($capa);
    if ($capa === '') {
        return '';
    }

    $capa = str_replace('\\', '/', $capa);

    if (str_starts_with($capa, '../') || str_starts_with($capa, 'http://') || str_starts_with($capa, 'https://')) {
        return $capa;
    }

    if (str_starts_with($capa, '/')) {
        return $capa;
    }

    if (str_starts_with($capa, './')) {
        return '../' . ltrim($capa, './');
    }

    if (str_starts_with($capa, 'assets/')) {
        return '../' . $capa;
    }

    return '../' . ltrim($capa, './');
};

$mensagem = '';
if (!empty($_GET['msg'])) {
    $mensagem = htmlspecialchars($_GET['msg']);
}
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
    <style>
      .dashboard-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
      .metric { background: rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.22); border-radius:16px; padding:1.2rem; }
      .metric .label { color: var(--bone-dim); text-transform: uppercase; letter-spacing: .12rem; font-size: .7rem; }
      .metric .value { font-size: 1.8rem; font-family: 'Cormorant Garamond', serif; margin-top: .5rem; }
      .venda-form { background: rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.22); border-radius:18px; padding:1.2rem; }
      .table thead th { text-transform: uppercase; letter-spacing: .12rem; font-size: .72rem; }
      .btn-acao { min-width: 110px; }
      .itens-venda-container { display:flex; flex-direction:column; gap:0.8rem; }
      .empty-itens { border:1px dashed rgba(176,141,87,.35); border-radius:14px; padding:1rem; color:var(--bone-dim); background:rgba(255,255,255,.02); }
      .item-venda-row { display:grid; grid-template-columns:68px 1fr 120px auto; align-items:center; gap:0.9rem; background:rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.24); border-radius:14px; padding:0.8rem 0.9rem; }
      .item-venda-imagem-wrap { width:68px; height:68px; overflow:hidden; border-radius:12px; background:rgba(255,255,255,.04); }
      .item-venda-imagem { width:100%; height:100%; object-fit:cover; }
      .item-venda-info { display:flex; flex-direction:column; gap:0.2rem; }
      .item-venda-info small { color:var(--bone-dim); }
      .item-venda-quantidade-wrap { display:flex; flex-direction:column; gap:0.25rem; }
      .item-venda-quantidade-wrap label { font-size:0.7rem; color:var(--bone-dim); text-transform:uppercase; letter-spacing:0.08rem; }
      .item-venda-quantidade-wrap input { width:100%; min-height:40px; }
      .item-venda-desconto-wrap { display:flex; flex-direction:column; gap:0.25rem; }
      .item-venda-desconto-wrap label { font-size:0.7rem; color:var(--bone-dim); text-transform:uppercase; letter-spacing:0.08rem; }
      .selected-product-box { background: rgba(255,255,255,0.04); border:1px solid rgba(176,141,87,.26); border-radius:12px; padding:0.8rem 0.9rem; color:var(--bone); min-height:48px; }
      .produto-card-select { display:block; background:rgba(18,13,16,.82); border:1px solid rgba(176,141,87,.25); border-radius:16px; padding:0.8rem; color:#f4efe8; text-decoration:none; transition:0.2s ease; }
      .produto-card-select:hover { border-color:rgba(176,141,87,.75); transform:translateY(-1px); }
      .produto-card-thumb-wrap { width:100%; height:170px; overflow:hidden; border-radius:12px; background:rgba(255,255,255,0.04); margin-bottom:0.75rem; }
      .produto-card-thumb { width:100%; height:100%; object-fit:cover; display:block; }
      .placeholder-thumb { display:flex; align-items:center; justify-content:center; width:100%; height:100%; color:var(--bone-dim); background:rgba(255,255,255,0.03); }
      .produto-card-info { display:flex; flex-direction:column; gap:0.2rem; }
      .produto-card-info small, .produto-card-info span { color:var(--bone-dim); }
      @media (max-width: 576px) {
        .item-venda-row { grid-template-columns:60px 1fr; }
        .item-venda-quantidade-wrap, .item-venda-row > .btn { grid-column:1 / -1; }
      }
    </style>
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Painel administrativo</a>
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
          <h1 class="admin-title">Dashboard de vendas</h1>
          <div class="admin-subtitle">controle / performance / registros</div>
        </div>
      </div>

      <?php if ($mensagem): ?>
        <div class="alert alert-success" role="alert"><?= $mensagem ?></div>
      <?php endif; ?>

      <div class="dashboard-grid">
        <div class="metric">
          <div class="label">Total vendido</div>
          <div class="value"><?= count($listaVendas) ?></div>
        </div>
        <div class="metric">
          <div class="label">Receita</div>
          <div class="value">R$ <?= number_format((float) $totais['total_recebido'], 2, ',', '.') ?></div>
        </div>
        <div class="metric">
          <div class="label">Custo</div>
          <div class="value">R$ <?= number_format((float) $totais['total_custo'], 2, ',', '.') ?></div>
        </div>
        <div class="metric">
          <div class="label">Lucro</div>
          <div class="value">R$ <?= number_format((float) $totais['total_recebido'] - (float) $totais['total_custo'], 2, ',', '.') ?></div>
        </div>
      </div>

      <div class="venda-form mb-4">
        <h2 class="admin-title" style="font-size: 2rem; margin-bottom: 1rem;"><?= $vendaEditando ? 'Editar venda' : 'Registrar venda' ?></h2>
        <form method="post" action="./api/vendas.php" id="formVenda">
          <input type="hidden" name="venda_id" value="<?= (int) ($vendaEditando['id'] ?? 0) ?>">
          <div class="row g-3">
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <label class="form-label mb-0">Itens da venda</label>
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalEscolhaProduto">
                  + Adicionar produto
                </button>
              </div>
              <div id="listaItensVenda" class="itens-venda-container">
                <div class="empty-itens">Nenhum item adicionado.</div>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Cliente</label>
              <input type="text" name="cliente" list="clientesCadastrados" class="form-control" placeholder="Nome do cliente" value="<?= htmlspecialchars($vendaEditando['cliente'] ?? '') ?>" required>
              <datalist id="clientesCadastrados"><?php foreach ($clientes as $cliente): ?><option value="<?= htmlspecialchars($cliente['nome']) ?>"><?php endforeach; ?></datalist>
            </div>
            <div class="col-md-2">
              <label class="form-label">Desconto</label>
              <input type="number" step="0.01" min="0" name="desconto" class="form-control" value="<?= htmlspecialchars((string) ($vendaEditando['desconto'] ?? 0)) ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Acréscimo</label>
              <input type="number" step="0.01" min="0" name="acrescimo" class="form-control" value="<?= htmlspecialchars((string) ($vendaEditando['acrescimo'] ?? 0)) ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Observação</label>
              <input type="text" name="observacao" class="form-control" placeholder="Obs. da venda" value="<?= htmlspecialchars($vendaEditando['observacao'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Data</label>
              <input type="datetime-local" name="data_venda" class="form-control" value="<?= $vendaEditando ? date('Y-m-d\\TH:i', strtotime($vendaEditando['data_venda'])) : date('Y-m-d\\TH:i') ?>">
            </div>
            <input type="hidden" name="itens_json" id="itens_json" value="[]">
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-outline-success">Salvar venda</button>
            </div>
          </div>
        </form>
      </div>

      <div class="panel">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Itens</th>
                <th>Desconto</th>
                <th>Acréscimo</th>
                <th>Total</th>
                <th>Data</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($listaVendas)): ?>
                <tr><td colspan="7" class="empty-state">Nenhuma venda registrada.</td></tr>
              <?php else: ?>
                <?php foreach ($listaVendas as $v): ?>
                  <tr>
                    <td><?= htmlspecialchars($v['cliente']) ?></td>
                    <td><button type="button" class="btn btn-link text-light p-0" data-bs-toggle="modal" data-bs-target="#detalheVenda<?= (int) $v['id'] ?>"><?= htmlspecialchars($v['itens'] ?? 'Sem itens') ?></button></td>
                    <td>R$ <?= number_format((float) $v['desconto'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format((float) $v['acrescimo'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format((float) $v['valor_total'], 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
                    <td>
                      <a href="./vendas.php?editar=<?= (int) $v['id'] ?>" class="btn btn-sm btn-outline-light btn-acao">Editar</a>
                      <a href="./api/vendas.php?acao=excluir&id=<?= (int) $v['id'] ?>" class="btn btn-sm btn-outline-danger btn-acao" onclick="return confirm('Excluir esta venda?')">Excluir</a>
                    </td>
                  </tr>
                  <div class="modal fade" id="detalheVenda<?= (int) $v['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Venda de <?= htmlspecialchars($v['cliente']) ?></h5></div><div class="modal-body"><p><strong>Observação:</strong> <?= nl2br(htmlspecialchars($v['observacao'] ?? '')) ?: 'Nenhuma' ?></p><p><strong>Desconto:</strong> R$ <?= number_format((float) $v['desconto'], 2, ',', '.') ?></p><p><strong>Total:</strong> R$ <?= number_format((float) $v['valor_total'], 2, ',', '.') ?></p><p><strong>Itens:</strong> <?= htmlspecialchars($v['itens'] ?? '') ?></p></div></div></div></div>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalEscolhaProduto" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Escolher produto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <input type="text" id="buscaProduto" class="form-control" placeholder="Buscar produto por nome ou código..." />
            </div>
            <div class="row g-3" id="listaProdutosModal">
              <?php foreach ($produtos as $produto): ?>
                <?php $imagemProduto = $resolveImagemProduto($produto['capa'] ?? ''); ?>
                <div class="col-md-4 produto-card-item" data-nome="<?= strtolower(htmlspecialchars($produto['nome'])) ?>" data-codigo="<?= strtolower(htmlspecialchars($produto['idPDR'] ?? '')) ?>">
                  <button type="button" class="produto-card-select w-100 text-start" data-id="<?= (int) $produto['id'] ?>" data-nome="<?= htmlspecialchars($produto['nome']) ?>" data-valor="<?= htmlspecialchars((string) ($produto['valor'] ?? '0')) ?>" data-custo="<?= htmlspecialchars((string) ($produto['custo'] ?? 0)) ?>" data-capa="<?= htmlspecialchars($imagemProduto) ?>">
                    <div class="produto-card-thumb-wrap">
                      <?php if ($imagemProduto !== ''): ?>
                        <img src="<?= htmlspecialchars($imagemProduto) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="produto-card-thumb">
                      <?php else: ?>
                        <div class="produto-card-thumb placeholder-thumb">Sem imagem</div>
                      <?php endif; ?>
                    </div>
                    <div class="produto-card-info">
                      <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                      <small><?= htmlspecialchars($produto['idPDR'] ?? '') ?></small>
                      <span>R$ <?= number_format((float) ($produto['valor'] ?? 0), 2, ',', '.') ?></span>
                    </div>
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      const itensVenda = <?= json_encode(array_map(static function ($item) {
        return [
          'id' => (int) $item['produto_id'],
          'nome' => $item['produto_nome'],
          'valor' => (float) $item['valor_unitario'],
          'desconto' => (float) ($item['desconto'] ?? 0),
          'quantidade' => (int) $item['quantidade'],
          'capa' => $item['capa'] ?? '',
        ];
      }, $vendaEditando['itens'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
      const listaItensVenda = document.getElementById('listaItensVenda');
      const itensJsonInput = document.getElementById('itens_json');
      const buscaProduto = document.getElementById('buscaProduto');
      const produtosCards = [...document.querySelectorAll('.produto-card-item')];

      function normalizarCapa(capa) {
        if (!capa || typeof capa !== 'string') {
          return '';
        }

        const valor = capa.trim();
        if (!valor) return '';
        if (valor.startsWith('../') || valor.startsWith('http://') || valor.startsWith('https://')) return valor;
        if (valor.startsWith('/')) return valor;
        if (valor.startsWith('./')) return '../' + valor.replace(/^\.\//, '');
        if (valor.startsWith('assets/')) return '../' + valor;
        return '../' + valor.replace(/^\.\//, '');
      }

      function atualizarItensVenda() {
        if (!listaItensVenda) return;

        if (!itensVenda.length) {
          listaItensVenda.innerHTML = '<div class="empty-itens">Nenhum item adicionado.</div>';
          itensJsonInput.value = '[]';
          return;
        }

        listaItensVenda.innerHTML = itensVenda.map((item, index) => {
          const imagem = normalizarCapa(item.capa);
          return `
            <div class="item-venda-row">
              <div class="item-venda-imagem-wrap">
                ${imagem ? `<img src="${imagem}" alt="${item.nome}" class="item-venda-imagem">` : '<div class="item-venda-imagem placeholder-thumb">Sem imagem</div>'}
              </div>
              <div class="item-venda-info">
                <strong>${item.nome}</strong>
                <small>R$ ${Number(item.valor || 0).toFixed(2).replace('.', ',')}</small>
              </div>
              <div class="item-venda-quantidade-wrap">
                <label>Qtd</label>
                <input type="number" min="1" value="${item.quantidade}" data-index="${index}" class="item-quantidade">
              </div>
              <div class="item-venda-desconto-wrap">
                <label>Desconto R$</label>
                <input type="number" step="0.01" min="0" value="${item.desconto || 0}" data-index="${index}" class="item-desconto">
              </div>
              <button type="button" class="btn btn-outline-danger btn-sm" data-remove="${index}">Remover</button>
            </div>
          `;
        }).join('');

        itensJsonInput.value = JSON.stringify(itensVenda);

        document.querySelectorAll('.item-quantidade').forEach((input) => {
          input.addEventListener('input', function () {
            const index = Number(this.dataset.index);
            const valor = Number(this.value || 1);
            itensVenda[index].quantidade = Math.max(1, valor);
            atualizarItensVenda();
          });
        });

        document.querySelectorAll('.item-desconto').forEach((input) => {
          input.addEventListener('input', function () {
            const index = Number(this.dataset.index);
            itensVenda[index].desconto = Math.max(0, Number(this.value || 0));
            itensJsonInput.value = JSON.stringify(itensVenda);
          });
        });

        document.querySelectorAll('[data-remove]').forEach((btn) => {
          btn.addEventListener('click', function () {
            const index = Number(this.dataset.remove);
            itensVenda.splice(index, 1);
            atualizarItensVenda();
          });
        });
      }

      document.querySelectorAll('.produto-card-select').forEach((btn) => {
        btn.addEventListener('click', function () {
          const id = Number(this.dataset.id);
          const nome = this.dataset.nome;
          const valor = Number(this.dataset.valor || 0);
          const capa = this.dataset.capa || '';

          const itemExistente = itensVenda.find((item) => item.id === id);
          if (itemExistente) {
            itemExistente.quantidade += 1;
          } else {
            itensVenda.push({ id, nome, valor, capa, quantidade: 1, desconto: 0 });
          }

          atualizarItensVenda();

          const modal = bootstrap.Modal.getInstance(document.getElementById('modalEscolhaProduto'));
          if (modal) modal.hide();
        });
      });

      if (buscaProduto) {
        buscaProduto.addEventListener('input', function () {
          const termo = this.value.trim().toLowerCase();

          produtosCards.forEach((item) => {
            const nome = (item.dataset.nome || '').toLowerCase();
            const codigo = (item.dataset.codigo || '').toLowerCase();
            const visivel = !termo || nome.includes(termo) || codigo.includes(termo);
            item.style.display = visivel ? '' : 'none';
          });
        });
      }

      document.getElementById('formVenda')?.addEventListener('submit', function () {
        if (!itensVenda.length) {
          alert('Adicione pelo menos um produto para registrar a venda.');
          return false;
        }

        itensJsonInput.value = JSON.stringify(itensVenda);
      });

      atualizarItensVenda();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>
