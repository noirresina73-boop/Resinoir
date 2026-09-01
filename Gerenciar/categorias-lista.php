<?php
require_once __DIR__ . '/auth.php';

use Controllers\CatalogoAuxController;
include 'autoloader.php';

$Aux = new CatalogoAuxController;
$categorias = $Aux->listarCategorias();
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Categorias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Categorias</a>
        <div class="topbar-actions">
          <a href="./produtos-lista.php" class="btn btn-outline-secondary">Produtos</a>
          <a href="./colecoes-lista.php" class="btn btn-outline-secondary">Coleções</a>
          <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalItem" onclick="abrirModalCriar()">+ Nova categoria</button>
        </div>
      </div>
    </nav>

    <div class="page-shell">
      <div class="admin-header">
        <div>
          <h1 class="admin-title">Categorias</h1>
          <div class="admin-subtitle">organização do catálogo</div>
        </div>
      </div>

      <div class="panel">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Capa</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($categorias)): ?>
                <tr><td colspan="4" class="empty-state">Nenhuma categoria cadastrada.</td></tr>
              <?php endif; ?>
              <?php foreach ($categorias as $c): ?>
                <?php $capaCategoria = trim((string) ($c['capa'] ?? '')); if ($capaCategoria !== '' && !preg_match('#^(https?:)?//#', $capaCategoria) && !str_starts_with($capaCategoria, '../')) { $capaCategoria = preg_match('#^assets/#', $capaCategoria) ? '../' . $capaCategoria : (str_starts_with($capaCategoria, './') ? '../' . ltrim($capaCategoria, './') : '../' . ltrim($capaCategoria, './')); } ?>
                <tr>
                  <td>
                    <?php if ($c['capa']): ?>
                      <img class="card-thumb" src="<?= htmlspecialchars($capaCategoria) ?>" alt="<?= htmlspecialchars($c['nome']) ?>">
                    <?php else: ?>
                      <span class="text-secondary">—</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($c['nome']) ?></td>
                  <td class="text-secondary" style="max-width:300px;"><?= htmlspecialchars($c['descricao']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-outline-light" onclick='abrirModalEditar(<?= json_encode($c) ?>)' data-bs-toggle="modal" data-bs-target="#modalItem">Editar</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="excluir(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>')">Excluir</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL CRIAR/EDITAR -->
    <div data-bs-theme="dark" class="modal fade" id="modalItem" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="modalTitulo">Nova categoria</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="itemId">
            <div class="mb-3">
              <label class="form-label">Nome</label>
              <input type="text" class="form-control" id="itemNome">
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea class="form-control" id="itemDescricao" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Capa</label>
              <input type="file" accept="image/*" class="form-control" id="itemCapa">
              <div class="form-text" id="capaAtualTexto"></div>
            </div>
            <div class="form-text" id="erroItem" style="color:#f88;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" onclick="salvar()">Salvar</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      function abrirModalCriar() {
        document.getElementById('modalTitulo').textContent = 'Nova categoria';
        document.getElementById('itemId').value = '';
        document.getElementById('itemNome').value = '';
        document.getElementById('itemDescricao').value = '';
        document.getElementById('itemCapa').value = '';
        document.getElementById('capaAtualTexto').textContent = '';
        document.getElementById('erroItem').textContent = '';
      }

      function abrirModalEditar(categoria) {
        document.getElementById('modalTitulo').textContent = 'Editar categoria';
        document.getElementById('itemId').value = categoria.id;
        document.getElementById('itemNome').value = categoria.nome;
        document.getElementById('itemDescricao').value = categoria.descricao;
        document.getElementById('itemCapa').value = '';
        document.getElementById('capaAtualTexto').textContent = categoria.capa ? 'Já tem uma capa — envie um arquivo só se quiser trocar.' : '';
        document.getElementById('erroItem').textContent = '';
      }

      async function salvar() {
        const id = document.getElementById('itemId').value;
        const nome = document.getElementById('itemNome').value.trim();
        const descricao = document.getElementById('itemDescricao').value.trim();
        const arquivoCapa = document.getElementById('itemCapa').files[0];
        const erro = document.getElementById('erroItem');

        if (!nome) { erro.textContent = 'Digite um nome.'; return; }

        const form = new FormData();
        form.append('acao', id ? 'editar' : 'criar');
        if (id) form.append('id', id);
        form.append('nome', nome);
        form.append('descricao', descricao);
        if (arquivoCapa) form.append('capa', arquivoCapa);

        const resp = await fetch('./api/categoria.php', { method: 'POST', body: form });
        const dados = await resp.json();

        if (dados.erro) { erro.textContent = dados.erro; return; }

        window.location.reload();
      }

      async function excluir(id, nome) {
        if (!confirm(`Excluir a categoria "${nome}"?`)) return;

        const form = new FormData();
        form.append('acao', 'excluir');
        form.append('id', id);

        const resp = await fetch('./api/categoria.php', { method: 'POST', body: form });
        const dados = await resp.json();

        if (dados.erro) {
          alert(dados.erro);
          return;
        }

        window.location.reload();
      }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  </body>
</html>