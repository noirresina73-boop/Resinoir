<?php
require_once __DIR__ . '/auth.php';

use Controllers\infosController;
use Controllers\CatalogoAuxController;
include 'autoloader.php';

$nome = trim($_GET['nome'] ?? '');
$categoria = (int) ($_GET['categoria'] ?? 0);
$colecao = (int) ($_GET['colecao'] ?? 0);

$Controller = new infosController;
$produtos = $Controller->listarTodos(1, $nome, $categoria, $colecao);

$Aux = new CatalogoAuxController;
$categorias = $Aux->listarCategorias();
$colecoes = $Aux->listarColecoes();
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Produtos</a>
        <div class="topbar-actions">
          <a href="./categorias-lista.php" class="btn btn-outline-secondary">Categorias</a>
          <a href="./colecoes-lista.php" class="btn btn-outline-secondary">Coleções</a>
          <a href="./Infos.php" class="btn btn-outline-success">+ Novo produto</a>
        </div>
      </div>
    </nav>

    <div class="page-shell">
      <div class="admin-header">
        <div>
          <h1 class="admin-title">Dashboard de produtos</h1>
          <div class="admin-subtitle">catálogo / itens ativos</div>
        </div>
        <div class="topbar-actions">
          <a href="./index.php" class="btn btn-outline-secondary">Dashboard</a>
          <a href="./vendas.php" class="btn btn-outline-secondary">Vendas</a>
          <a href="./clientes-lista.php" class="btn btn-outline-secondary">Clientes</a>
        </div>
      </div>

      <div class="panel p-3 p-md-4">
        <form method="get" class="row g-2 filter-form">
          <div class="col-md-5">
            <input type="text" name="nome" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($nome) ?>">
          </div>
          <div class="col-md-3">
            <select name="categoria" class="form-select">
              <option value="0">Todas as categorias</option>
              <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $categoria === (int) $c['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <select name="colecao" class="form-select">
              <option value="0">Todas as coleções</option>
              <?php foreach ($colecoes as $co): ?>
                <option value="<?= $co['id'] ?>" <?= $colecao === (int) $co['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($co['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1">
            <button type="submit" class="btn btn-outline-light w-100">Filtrar</button>
          </div>
        </form>

        <?php if ($nome !== '' || $categoria > 0 || $colecao > 0): ?>
          <div class="mb-3">
            <a href="./produtos-lista.php" class="btn btn-sm btn-outline-secondary">Limpar filtros</a>
          </div>
        <?php endif; ?>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Capa</th>
                <th>Nome</th>
                <th>Valor</th>
                <th>Custo</th>
                <th>Estoque</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($produtos)): ?>
                <tr><td colspan="6" class="empty-state">Nenhum produto encontrado.</td></tr>
              <?php endif; ?>
              <?php foreach ($produtos as $p): ?>
                <?php $capaProduto = $p['capa'] ?? ''; $capaProduto = trim((string) $capaProduto); if ($capaProduto !== '' && !preg_match('#^(https?:)?//#', $capaProduto) && !str_starts_with($capaProduto, '../')) { $capaProduto = preg_match('#^assets/#', $capaProduto) ? '../' . $capaProduto : (str_starts_with($capaProduto, './') ? '../' . ltrim($capaProduto, './') : '../' . ltrim($capaProduto, './')); } ?>
                <tr>
                  <td><img class="card-thumb" src="<?= htmlspecialchars($capaProduto) ?>" alt="<?= htmlspecialchars($p['nome']) ?>"></td>
                  <td><?= htmlspecialchars($p['nome']) ?></td>
                  <td>R$ <?= htmlspecialchars($p['valor']) ?></td>
                  <td>R$ <?= htmlspecialchars($p['custo'] ?? 0) ?></td>
                  <td><?= htmlspecialchars($p['estoque']) ?></td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="./Infos.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-light">Editar</a>
                      <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirProduto(<?= (int) $p['id'] ?>)">Excluir</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalProdutoSalvo" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Produto salvo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <p class="mb-3">O produto foi salvo com sucesso.</p>
            <ul class="list-unstyled mb-0">
              <li><strong>Nome:</strong> <span id="modalProdutoNome">-</span></li>
              <li><strong>ID do produto:</strong> <span id="modalProdutoId">-</span></li>
              <li><strong>Preço:</strong> <span id="modalProdutoValor">-</span></li>
              <li><strong>Estoque:</strong> <span id="modalProdutoEstoque">-</span></li>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-success" id="btnIrParaListagem">Ir para listagem</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);

        if (params.get('salvo') !== '1') {
          return;
        }

        const nome = params.get('nome') || 'Produto';
        const idProduto = params.get('idProduto') || params.get('idPDR') || '-';
        const valor = params.get('valor') || '0';
        const estoque = params.get('estoque') || '0';

        document.getElementById('modalProdutoNome').textContent = nome;
        document.getElementById('modalProdutoId').textContent = idProduto;
        document.getElementById('modalProdutoValor').textContent = `R$ ${valor}`;
        document.getElementById('modalProdutoEstoque').textContent = estoque;

        const modal = new bootstrap.Modal(document.getElementById('modalProdutoSalvo'));
        modal.show();
      });

      document.getElementById('btnIrParaListagem')?.addEventListener('click', () => {
        window.location.href = './produtos-lista.php';
      });

      async function excluirProduto(id) {
        if (!confirm('Deseja excluir este produto?')) return;

        const form = new FormData();
        form.append('acao', 'excluir');
        form.append('id', id);

        const response = await fetch('./api/produtos.php', { method: 'POST', body: form });
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