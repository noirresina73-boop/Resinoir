<?php
require_once __DIR__ . '/auth.php';

use Controllers\CatalogoAuxController;
use Controllers\ListController;
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
          <a href="./home-layout.php" class="btn btn-outline-secondary">Home</a>
          <a href="./clientes-lista.php" class="btn btn-outline-secondary">Clientes</a>
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
                    <?php if ($c['capa'] && str_starts_with($c['capa'], 'svg:')): ?>
                      <?= ListController::htmlIconePorChave($c['capa'], 'thumb-admin') ?>
                    <?php elseif ($c['capa']): ?>
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
              <label class="form-label">Ícone</label>
              <div class="icon-selector" id="selectorIconesCategoria">
                <div class="icon-options" id="iconOptionsCategoria"></div>
                <div class="icon-preview" id="iconPreviewCategoria"></div>
              </div>
              <input type="hidden" id="itemIcone" value="">
              <div class="form-text">Selecione um ícone ou envie uma imagem abaixo.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Capa (imagem)</label>
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
      <?php
      $iconesJs = [];
      foreach (Controllers\ListController::ICONE_BIBLIOTECA as $chave => $dados) {
          $iconesJs[$chave] = $dados['svg'];
      }
      ?>
      const ICONES_BIBLIOTECA = <?= json_encode($iconesJs) ?>;

      function htmlIconePorChave(chave, modo = 'img-card') {
          const key = chave.replace('svg:', '');
          const svg = ICONES_BIBLIOTECA[key];
          if (!svg) return '';
          if (modo === 'cat-circle') {
              return svg.replace('<svg ', '<svg style="width:24px;height:24px;stroke:#e9e0c9;stroke-width:1.5;fill:none;" ');
          }
          return '<div class="svg-img-card">' + svg.replace('<svg ', '<svg style="width:100%;height:100%;stroke:#e9e0c9;stroke-width:1;fill:none;" ') + '</div>';
      }

      let iconesCategoria = [];
      let iconeSelecionadoCategoria = '';

      async function carregarIconesCategoria() {
        try {
          const resp = await fetch('./api/categoria.php');
          const dados = await resp.json();
          iconesCategoria = dados.icones || [];
          renderizarOpcoesIconesCategoria(iconesCategoria);
        } catch (e) {
          console.error('Erro ao carregar ícones', e);
        }
      }

      function renderizarOpcoesIconesCategoria(icones) {
        const container = document.getElementById('iconOptionsCategoria');
        container.innerHTML = '';
        icones.forEach(chave => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'icon-option';
          btn.dataset.chave = chave;
          btn.innerHTML = htmlIconePorChave('svg:' + chave, 'cat-circle');
          btn.onclick = () => selecionarIconeCategoria(chave, btn);
          container.appendChild(btn);
        });
      }

      function selecionarIconeCategoria(chave, btn) {
        iconeSelecionadoCategoria = chave;
        document.querySelectorAll('#iconOptionsCategoria .icon-option').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('itemIcone').value = 'svg:' + chave;
        document.getElementById('iconPreviewCategoria').innerHTML = htmlIconePorChave('svg:' + chave, 'img-card');
        document.getElementById('itemCapa').value = '';
      }

      function abrirModalCriar() {
        document.getElementById('modalTitulo').textContent = 'Nova categoria';
        document.getElementById('itemId').value = '';
        document.getElementById('itemNome').value = '';
        document.getElementById('itemDescricao').value = '';
        document.getElementById('itemCapa').value = '';
        document.getElementById('itemIcone').value = '';
        document.getElementById('iconPreviewCategoria').innerHTML = '';
        document.getElementById('capaAtualTexto').textContent = '';
        document.getElementById('erroItem').textContent = '';
        iconeSelecionadoCategoria = '';
        document.querySelectorAll('#iconOptionsCategoria .icon-option').forEach(b => b.classList.remove('active'));
        carregarIconesCategoria();
      }

      function abrirModalEditar(categoria) {
        document.getElementById('modalTitulo').textContent = 'Editar categoria';
        document.getElementById('itemId').value = categoria.id;
        document.getElementById('itemNome').value = categoria.nome;
        document.getElementById('itemDescricao').value = categoria.descricao;
        document.getElementById('itemCapa').value = '';
        document.getElementById('itemIcone').value = categoria.capa || '';
        document.getElementById('capaAtualTexto').textContent = categoria.capa ? 'Já tem uma capa — envie um arquivo só se quiser trocar.' : '';
        document.getElementById('erroItem').textContent = '';
        iconeSelecionadoCategoria = '';
        document.querySelectorAll('#iconOptionsCategoria .icon-option').forEach(b => b.classList.remove('active'));
        carregarIconesCategoria().then(() => {
          const capa = categoria.capa || '';
          if (capa.startsWith('svg:')) {
            const chave = capa.replace('svg:', '');
            const btn = document.querySelector(`#iconOptionsCategoria .icon-option[data-chave="${chave}"]`);
            if (btn) selecionarIconeCategoria(chave, btn);
          }
          if (capa && !capa.startsWith('svg:')) {
            document.getElementById('iconPreviewCategoria').innerHTML = '<img src="' + categoria.capa + '" style="max-width:80px;border-radius:8px;">';
          }
        });
      }

      async function salvar() {
        const id = document.getElementById('itemId').value;
        const nome = document.getElementById('itemNome').value.trim();
        const descricao = document.getElementById('itemDescricao').value.trim();
        const arquivoCapa = document.getElementById('itemCapa').files[0];
        const icone = document.getElementById('itemIcone').value;
        const erro = document.getElementById('erroItem');

        if (!nome) { erro.textContent = 'Digite um nome.'; return; }

        const form = new FormData();
        form.append('acao', id ? 'editar' : 'criar');
        if (id) form.append('id', id);
        form.append('nome', nome);
        form.append('descricao', descricao);
        if (icone) form.append('capa', icone);
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