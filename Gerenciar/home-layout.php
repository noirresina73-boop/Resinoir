<?php
require_once __DIR__ . '/auth.php';

use Controllers\infosController;
use Controllers\ListController;

include __DIR__ . '/autoloader.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$bannerProdutoId = (int) $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'home_banner_produto_id' LIMIT 1")->fetchColumn();
$novidadesJson = $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'home_novidades' LIMIT 1")->fetchColumn();

$bannerProduto = null;
if ($bannerProdutoId > 0) {
    $query = $pdo->prepare('SELECT p.*, c.nome AS colecao_nome, c.descricao AS colecao_descricao FROM produtos p LEFT JOIN colecao c ON p.colecao = c.id WHERE p.id = :id LIMIT 1');
    $query->bindValue(':id', $bannerProdutoId, PDO::PARAM_INT);
    $query->execute();
    $bannerProduto = $query->fetch(PDO::FETCH_ASSOC);
}

$novidadesIds = [];
$decoded = json_decode((string) $novidadesJson, true);
if (is_array($decoded)) {
    $novidadesIds = array_values(array_filter($decoded, 'is_numeric'));
}

$novidadesProdutos = [];
if (!empty($novidadesIds)) {
    $placeholders = implode(',', array_fill(0, count($novidadesIds), '?'));
    $query = $pdo->prepare("SELECT id, nome, valor, capa, estoque FROM produtos WHERE id IN ($placeholders) ORDER BY FIELD(id, " . implode(',', $novidadesIds) . ')');
    $query->execute($novidadesIds);
    $novidadesProdutos = $query->fetchAll(PDO::FETCH_ASSOC);
}

$mensagem = !empty($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Layout da Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
    <style>
      .produto-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(18, 13, 16, 0.9);
        border: 1px solid rgba(176, 141, 87, 0.28);
        border-radius: 12px;
        margin-bottom: 8px;
      }
      .produto-preview .capa-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid rgba(176, 141, 87, 0.3);
        flex-shrink: 0;
      }
      .produto-preview .capa-svg {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        border: 1px solid rgba(176, 141, 87, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
      }
      .produto-preview .info {
        flex: 1;
        min-width: 0;
      }
      .produto-preview .info .nome {
        font-size: 0.95rem;
        color: var(--bone);
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .produto-preview .info .detalhe {
        font-size: 0.78rem;
        color: var(--bone-dim);
      }
      .slot-novidade {
        border: 2px dashed rgba(176, 141, 87, 0.4);
        border-radius: 12px;
        padding: 8px;
        min-height: 88px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: grab;
      }
      .slot-novidade:hover {
        border-color: var(--gold-bright);
        background: rgba(176, 141, 87, 0.06);
      }
      .slot-novidade.drag-over {
        border-color: var(--gold-bright);
        background: rgba(176, 141, 87, 0.12);
        transform: scale(1.02);
      }
      .slot-novidade.preenchido {
        border: 1px solid rgba(176, 141, 87, 0.28);
        background: rgba(18, 13, 16, 0.9);
        cursor: grab;
      }
      .slot-vazio .placeholder {
        color: var(--bone-dim);
        font-size: 0.8rem;
        text-align: center;
      }
      .search-results {
        position: absolute;
        z-index: 2050;
        width: 100%;
        max-height: 240px;
        overflow-y: auto;
        background: rgba(18, 13, 16, 0.95);
        border: 1px solid rgba(176, 141, 87, 0.3);
        border-radius: 10px;
        display: none;
        pointer-events: auto;
      }
      .search-results.show {
        display: block;
      }
      .input-group-busca {
        position: relative;
      }
      }
      .search-results.show {
        display: block;
      }
      .search-result-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid rgba(176, 141, 87, 0.12);
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .search-result-item:hover {
        background: rgba(176, 141, 87, 0.08);
      }
      .search-result-item .nome {
        font-size: 0.85rem;
        color: var(--bone);
      }
      .search-result-item .detalhe {
        font-size: 0.72rem;
        color: var(--bone-dim);
      }
      .search-result-item .capa-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(176, 141, 87, 0.3);
        flex-shrink: 0;
      }
      .badge-slot {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--gold);
        color: var(--void);
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        border: 1px solid rgba(176, 141, 87, 0.28);
      }
      .preview-novidade {
        position: relative;
      }
      .preview-novidade .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(163, 40, 58, 0.8);
        color: var(--bone);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        cursor: pointer;
        border: 1px solid rgba(176, 141, 87, 0.28);
      }
      .drag-handle {
        cursor: grab;
        padding: 4px;
        opacity: 0.5;
      }
    </style>
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Administração</a>
        <div class="topbar-actions">
          <a href="./produtos-lista.php" class="btn btn-outline-secondary btn-sm">Produtos</a>
          <a href="./categorias-lista.php" class="btn btn-outline-secondary btn-sm">Categorias</a>
          <a href="./colecoes-lista.php" class="btn btn-outline-secondary btn-sm">Coleções</a>
          <a href="./home-layout.php" class="btn btn-outline-success btn-sm">Home</a>
          <a href="./vendas.php" class="btn btn-outline-secondary btn-sm">Vendas</a>
          <a href="./clientes-lista.php" class="btn btn-outline-secondary btn-sm">Clientes</a>
        </div>
      </div>
    </nav>

    <div class="page-shell">
      <div class="admin-header">
        <div>
          <h1 class="admin-title">Layout da Home</h1>
          <div class="admin-subtitle">Banner + Novidades em destaque</div>
        </div>
        <div class="topbar-actions">
          <a href="./index.php" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        </div>
      </div>

      <?php if ($mensagem): ?>
        <div class="alert alert-success mt-3" role="alert"><?= $mensagem ?></div>
      <?php endif; ?>

      <!-- BANNER PRODUCT SECTION -->
      <div class="panel p-3 p-md-4 mb-4">
        <h2 class="admin-title" style="font-size: 1.6rem; margin-bottom: 0.5rem;">Produto em Destaque (Banner)</h2>
        <p class="admin-subtitle">Escolha um anúncio para o banner. O banner puxará automaticamente a coleção desse produto.</p>

        <div class="position-relative">
          <div class="input-group mb-2">
            <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg> Buscar produto</span>
            <input type="text" id="buscarProduto" class="form-control" placeholder="Digite o nome do produto..." autocomplete="off">
          </div>
          <div id="resultadosBusca" class="search-results"></div>
        </div>

        <?php if ($bannerProduto): ?>
          <div class="produto-preview mt-3" data-banner-preview>
            <?php
            $capaBanner = $bannerProduto['capa'] ?? '';
            if ($capaBanner && str_starts_with($capaBanner, 'svg:')):
                echo '<span class="capa-svg">' . ListController::htmlIconePorChave($capaBanner, 'img-card') . '</span>';
            elseif ($capaBanner):
                $capaPath = trim((string) $capaBanner);
                if (!preg_match('#^(https?:)?//#', $capaPath) && !str_starts_with($capaPath, '../')) {
                    $capaPath = preg_match('#^assets/#', $capaPath) ? '../' . $capaPath : '../' . ltrim($capaPath, './');
                }
                echo '<img class="capa-thumb" src="' . htmlspecialchars($capaPath) . '" alt="' . htmlspecialchars($bannerProduto['nome']) . '">';
            else:
                echo '<span class="text-secondary" style="width:56px;text-align:center;">—</span>';
            endif;
            ?>
            <div class="info">
              <div class="nome"><?= htmlspecialchars($bannerProduto['nome']) ?></div>
              <div class="detalhe">R$ <?= number_format((float) $bannerProduto['valor'], 2, ',', '.') ?> · ID: <?= $bannerProduto['id'] ?></div>
              <?php if (!empty($bannerProduto['colecao_nome'])): ?>
                <div class="detalhe" style="color: var(--gold-bright);">Coleção: <?= htmlspecialchars($bannerProduto['colecao_nome']) ?></div>
              <?php else: ?>
                <div class="detalhe" style="color: #ffb7b7;">Produto sem coleção — o banner não mostrará categorias.</div>
              <?php endif; ?>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" id="limparBanner" style="height: fit; padding: 0.35rem 0.7rem;">Limpar</button>
          </div>
        <?php else: ?>
          <div class="produto-preview mt-3" data-banner-preview style="border: 2px dashed rgba(176,141,87,0.3); background: transparent;">
            <div class="placeholder text-center" style="width: 100%;">Nenhum produto selecionado para o banner.</div>
          </div>
        <?php endif; ?>

        <input type="hidden" id="bannerProdutoId" value="<?= $bannerProdutoId ?>">
        <div class="mt-3">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="usarCapaProduto" <?= (int) $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'home_banner_usar_produto_capa' LIMIT 1")->fetchColumn() === 1 ? 'checked' : '' ?>>
            <label class="form-check-label" for="usarCapaProduto">Usar capa do produto no banner (não da coleção)</label>
          </div>
        </div>
      </div>

      <!-- NOVIDADES SECTION -->
      <div class="panel p-3 p-md-4 mb-4">
        <h2 class="admin-title" style="font-size: 1.6rem; margin-bottom: 0.5rem;">Novidades em Destaque</h2>
        <p class="admin-subtitle">Selecione até 3 produtos e arraste para ordenar.</p>

        <div id="slotsNovidades">
          <div class="row g-2">
            <?php for ($i = 0; $i < 3; $i++): ?>
              <?php
              $produtoSlot = null;
              foreach ($novidadesProdutos as $np) {
                  if ((int) $np['id'] === $novidadesIds[$i]) {
                      $produtoSlot = $np;
                      break;
                  }
              }
              ?>
              <div class="col-12" data-slot="<?= $i ?>">
                <div class="input-group input-group-busca mb-2">
                  <span class="input-group-text">Slot <?= $i + 1 ?></span>
                  <input type="text" class="form-control buscar-slot" placeholder="Buscar produto..." data-slot="<?= $i ?>" autocomplete="off">
                  <div class="search-results slot-results-<?= $i ?>"></div>
                </div>
                <div class="slot-container">
                  <div class="slot-novidade<?= $produtoSlot ? ' preenchido' : ' slot-vazio' ?>" data-slot="<?= $i ?>">
                    <?php if ($produtoSlot): ?>
                      <span class="badge-slot"><?= $i + 1 ?></span>
                      <?php
                      $capaSlot = $produtoSlot['capa'] ?? '';
                      if ($capaSlot && str_starts_with($capaSlot, 'svg:')):
                          echo '<span class="capa-svg">' . ListController::htmlIconePorChave($capaSlot, 'img-card') . '</span>';
                      elseif ($capaSlot):
                          $capaPath = trim((string) $capaSlot);
                          if (!preg_match('#^(https?:)?//#', $capaPath) && !str_starts_with($capaPath, '../')) {
                              $capaPath = preg_match('#^assets/#', $capaPath) ? '../' . $capaPath : '../' . ltrim($capaPath, './');
                          }
                          echo '<img class="capa-thumb" src="' . htmlspecialchars($capaPath) . '" alt="' . htmlspecialchars($produtoSlot['nome']) . '">';
                      else:
                          echo '<span class="text-secondary" style="width:56px;text-align:center;">—</span>';
                      endif;
                      ?>
                      <div class="info">
                        <div class="nome"><?= htmlspecialchars($produtoSlot['nome']) ?></div>
                        <div class="detalhe">R$ <?= number_format((float) $produtoSlot['valor'], 2, ',', '.') ?> · ID: <?= $produtoSlot['id'] ?></div>
                      </div>
                      <div class="drag-handle"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/></svg></div>
                      <div class="remove-btn" data-slot="<?= $i ?>" title="Remover">×</div>
                    <?php else: ?>
                      <div class="placeholder">Slot vazio — busque um produto</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <input type="hidden" id="novidadesIds" value="<?= htmlspecialchars(json_encode($novidadesIds)) ?>">
      </div>

      <!-- SAVE BUTTON -->
      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">Cancelar</button>
        <button type="button" class="btn btn-outline-success" id="salvarLayout">Salvar layout</button>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
      const ICONES_BIBLIOTEC = <?= json_encode(array_map(function($i) { return $i['svg']; }, ListController::ICONE_BIBLIOTECA)) ?>;

      function htmlIconePorChave(chave, modo) {
        const key = chave.replace('svg:', '');
        const svg = ICONES_BIBLIOTEC[key];
        if (!svg) return '';
        if (modo === 'img-card') {
          return '<div class="svg-img-card">' + svg.replace('<svg ', '<svg style="width:100%;height:100%;stroke:#e9e0c9;stroke-width:1;fill:none;" ') + '</div>';
        }
        if (modo === 'thumb-admin') {
          return '<div class="svg-img-card" style="width:100%;height:100%;">' + svg.replace('<svg ', '<svg style="width:48px;height:48px;" ') + '</div>';
        }
        return '<div class="svg-img-card">' + svg + '</div>';
      }

      function gerarCapaPreview(capa, alt, classe) {
        if (!capa) return '<span class="text-secondary" style="width:56px;text-align:center;">—</span>';
        if (capa.startsWith('svg:')) return '<span class="capa-svg">' + htmlIconePorChave(capa, classe || 'img-card') + '</span>';
        let path = capa;
        if (!/^(https?:)?\/\//.test(path) && !path.startsWith('../')) {
          path = /^assets\//.test(path) ? '../' + path : '../' + path.replace(/^.\//, '');
        }
        const cls = (classe === 'thumb-admin') ? 'card-thumb' : 'capa-thumb';
        return '<img class="' + cls + '" src="' + path + '" alt="' + alt + '">';
      }

      let novidadesOrdenadas = <?= json_encode($novidadesIds) ?>;
      let produtosCache = {};

      <?php foreach ($novidadesProdutos as $np): ?>
        produtosCache[<?= (int) $np['id'] ?>] = {
          id: <?= (int) $np['id'] ?>,
          nome: <?= json_encode($np['nome']) ?>,
          valor: <?= json_encode(number_format((float) $np['valor'], 2, ',', '.')) ?>,
          capa: <?= json_encode($np['capa'] ?? '') ?>,
          colecao: <?= (int) ($np['colecao'] ?? 0) ?>
        };
      <?php endforeach; ?>

      async function buscarProdutos(nome) {
        const form = new FormData();
        form.append('acao', 'buscar_produtos_por_nome');
        form.append('nome', nome);
        const resp = await fetch('./api/home_layout.php', { method: 'POST', body: form });
        return await resp.json();
      }

      async function buscarProdutosPorIds(ids) {
        const form = new FormData();
        form.append('acao', 'buscar_produtos_por_ids');
        form.append('ids', JSON.stringify(ids));
        const resp = await fetch('./api/home_layout.php', { method: 'POST', body: form });
        return await resp.json();
      }

      let buscaTimeout = null;

      function montarItemResultado(p, targetType, targetId) {
        const capaHtml = gerarCapaPreview(p.capa, p.nome, 'thumb-admin');
        const jsonStr = JSON.stringify(p).replace(/"/g, '&quot;');
        const onclick = targetType === 'banner'
          ? 'selecionarProdutoBanner(' + jsonStr + ')'
          : 'selecionarProdutoSlot(' + jsonStr + ', ' + targetId + ')';
        return '<div class="search-result-item" onclick="' + onclick + '">' +
          capaHtml +
          '<div><div class="nome">' + p.nome + '</div><div class="detalhe">R$ ' + p.valor + ' · ID: ' + p.id + '</div></div>' +
          '</div>';
      }

      function setupBusca(inputSelector, resultsSelector, targetType, targetId) {
        const input = document.querySelector(inputSelector);
        const results = document.querySelector(resultsSelector);
        if (!input || !results) return;

        input.addEventListener('input', () => {
          clearTimeout(buscaTimeout);
          const termo = input.value.trim();
          if (termo === '') {
            results.classList.remove('show');
            return;
          }
          buscaTimeout = setTimeout(async () => {
            try {
              const dados = await buscarProdutos(termo);
              const produtos = dados.produtos || [];
              results.innerHTML = produtos.map(p => montarItemResultado(p, targetType, targetId)).join('');
              results.classList.add('show');
            } catch (e) {
              console.error(e);
            }
          }, 300);
        });

        document.addEventListener('click', (e) => {
          if (!input.contains(e.target) && !results.contains(e.target)) {
            results.classList.remove('show');
          }
        });
      }

      function selecionarProdutoBanner(produto) {
        document.getElementById('bannerProdutoId').value = produto.id;
        const preview = document.querySelector('[data-banner-preview]');
        if (preview) {
          const capaHtml = gerarCapaPreview(produto.capa, produto.nome, 'img-card');
          const colecaoId = parseInt(produto.colecao) || 0;
          let colecaoHtml;
          if (colecaoId > 0) {
            colecaoHtml = '<div class="detalhe" style="color: var(--gold-bright);">Coleção ID: ' + colecaoId + '</div>';
          } else {
            colecaoHtml = '<div class="detalhe" style="color: #ffb7b7;">Produto sem coleção — o banner não mostrará categorias.</div>';
          }
          preview.innerHTML =
            capaHtml +
            '<div class="info">' +
              '<div class="nome">' + produto.nome + '</div>' +
              '<div class="detalhe">R$ ' + produto.valor + ' · ID: ' + produto.id + '</div>' +
              colecaoHtml +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger btn-sm" id="limparBanner" style="height: fit; padding: 0.35rem 0.7rem;">Limpar</button>';
        }
        document.getElementById('buscarProduto').value = '';
        document.getElementById('resultadosBusca').classList.remove('show');
        document.getElementById('resultadosBusca').innerHTML = '';
      }

      function selecionarProdutoSlot(produto, slotIndex) {
        produtosCache[produto.id] = produto;
        novidadesOrdenadas[slotIndex] = produto.id;
        document.getElementById('novidadesIds').value = JSON.stringify(novidadesOrdenadas);
        const input = document.querySelector('.buscar-slot[data-slot="' + slotIndex + '"]');
        if (input) input.value = '';
        const resultsEl = document.querySelector('.slot-results-' + slotIndex);
        if (resultsEl) {
          resultsEl.classList.remove('show');
          resultsEl.innerHTML = '';
        }
        renderizarSlots();
      }

      function removerSlot(slotIndex) {
        novidadesOrdenadas[slotIndex] = null;
        document.getElementById('novidadesIds').value = JSON.stringify(novidadesOrdenadas);
        renderizarSlots();
      }

      function gerarSlotHtml(produto, slotIndex) {
        const capaHtml = gerarCapaPreview(produto.capa, produto.nome, 'img-card');
        const badgeSlot = '<span class="badge-slot">' + (slotIndex + 1) + '</span>';
        const dragHandle = '<div class="drag-handle" draggable="true" data-drag-slot="' + slotIndex + '"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/></svg></div>';
        const removeBtn = '<div class="remove-btn" onclick="removerSlot(' + slotIndex + ')" title="Remover">×</div>';
        return badgeSlot + capaHtml +
          '<div class="info">' +
            '<div class="nome">' + produto.nome + '</div>' +
            '<div class="detalhe">R$ ' + produto.valor + ' · ID: ' + produto.id + '</div>' +
          '</div>' +
          dragHandle + removeBtn;
      }

      function renderizarSlots() {
        const slots = document.querySelectorAll('.slot-novidade');
        slots.forEach((slot, i) => {
          const produtoId = novidadesOrdenadas[i];
          if (produtoId && produtosCache[produtoId]) {
            const produto = produtosCache[produtoId];
            slot.classList.add('preenchido');
            slot.classList.remove('slot-vazio');
            slot.innerHTML = gerarSlotHtml(produto, i);
          } else if (produtoId) {
            slot.classList.add('preenchido');
            slot.classList.remove('slot-vazio');
            slot.innerHTML = '<div class="placeholder">Carregando...</div>';
          } else {
            slot.classList.add('slot-vazio');
            slot.classList.remove('preenchido');
            slot.innerHTML = '<div class="placeholder">Slot vazio — busque um produto</div>';
          }
        });
        document.getElementById('novidadesIds').value = JSON.stringify(novidadesOrdenadas);
        setupDragAndDrop();
      }

      function setupDragAndDrop() {
        document.querySelectorAll('.drag-handle').forEach(handle => {
          handle.addEventListener('dragstart', () => {
            handle.parentElement.setAttribute('data-dragging', '1');
          });
          handle.addEventListener('dragend', () => {
            const slot = handle.closest('.slot-novidade');
            slot.removeAttribute('data-dragging');
          });
        });

        document.querySelectorAll('.slot-novidade').forEach(slot => {
          slot.addEventListener('dragover', e => {
            e.preventDefault();
            slot.classList.add('drag-over');
          });
          slot.addEventListener('dragleave', () => {
            slot.classList.remove('drag-over');
          });
          slot.addEventListener('drop', e => {
            e.preventDefault();
            slot.classList.remove('drag-over');
            const dragging = document.querySelector('.slot-novidade[data-dragging="1"]');
            if (!dragging) return;
            const fromIdx = parseInt(dragging.getAttribute('data-slot'));
            const toIdx = parseInt(slot.getAttribute('data-slot'));
            if (fromIdx !== toIdx && fromIdx !== undefined && toIdx !== undefined) {
              const temp = novidadesOrdenadas[fromIdx];
              novidadesOrdenadas[fromIdx] = novidadesOrdenadas[toIdx];
              novidadesOrdenadas[toIdx] = temp;
              renderizarSlots();
            }
          });
        });
      }

      document.getElementById('limparBanner')?.addEventListener('click', () => {
        document.getElementById('bannerProdutoId').value = '';
        const preview = document.querySelector('[data-banner-preview]');
        if (preview) {
          preview.innerHTML = '<div class="placeholder text-center" style="width: 100%;">Nenhum produto selecionado para o banner.</div>';
        }
      });

      setupBusca('#buscarProduto', '#resultadosBusca', 'banner');

      document.querySelectorAll('.buscar-slot').forEach(input => {
        const slotIdx = input.getAttribute('data-slot');
        setupBusca('.buscar-slot[data-slot="' + slotIdx + '"]', '.slot-results-' + slotIdx, 'slot', slotIdx);
      });

      document.getElementById('salvarLayout')?.addEventListener('click', async () => {
        const bannerId = document.getElementById('bannerProdutoId').value;
        const usarCapaProduto = document.getElementById('usarCapaProduto').checked ? 1 : 0;
        const novidadesFilter = novidadesOrdenadas.filter(x => x !== null && x !== undefined && x !== '').map(x => parseInt(x));

        const form = new FormData();
        form.append('acao', 'salvar_home_layout');
        form.append('banner_produto_id', bannerId);
        form.append('usar_capa_produto', usarCapaProduto);
        form.append('novidades', JSON.stringify(novidadesFilter));

        const resp = await fetch('./api/home_layout.php', { method: 'POST', body: form });
        const dados = await resp.json();

        if (dados.sucesso) {
          window.location.href = './home-layout.php?msg=' + encodeURIComponent('Layout da home atualizado com sucesso.');
        } else {
          alert(dados.erro || 'Erro ao salvar.');
        }
      });

      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.remove-btn').forEach(btn => {
          btn.addEventListener('click', () => removerSlot(parseInt(btn.getAttribute('data-slot'))));
        });
      });
    </script>
  </body>
</html>
