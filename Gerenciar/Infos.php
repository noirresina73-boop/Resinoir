<?php
require_once __DIR__ . '/auth.php';
use Controllers\infosController;
use Controllers\CatalogoAuxController;

include 'autoloader.php';

$Aux = new CatalogoAuxController;
$categorias = $Aux->listarCategorias();
$colecoes = $Aux->listarColecoes();

$produto = null;
$modoEdicao = false;
$proximoId = 1;

if (isset($_GET['id'])) {
    $Produtos = new infosController;
    $produto = $Produtos->buscarPorId((int) $_GET['id']);
    $modoEdicao = (bool) $produto;
}

if (!$modoEdicao) {
    $proximoId = (new infosController)->obterProximoId();
}
?>

<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/gerenciar.css">
    <link rel="stylesheet" href="./styles/styleInfos.css">
  </head>
  <body>

    <header>
      <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
          <a class="navbar-brand"><?= $modoEdicao ? 'Editar produto' : 'Criar post' ?></a>
          <div class="topbar-actions">
            <a href="./produtos-lista.php" class="btn btn-outline-secondary btn-form">Ver todos</a>
            <button class="btn btn-outline-danger btn-form" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Descartar o post</button>
            <button class="btn btn-outline-success btn-form" form="formAnum" type="submit">Salvar</button>
          </div>
        </div>
      </nav>
    </header>

    <main>
      <div class="formulario">
        <form id="formAnum" data-bs-theme="dark" method="post" action="./Controllers/salvarProduto.php" enctype="multipart/form-data">

          <input type="hidden" name="acao" value="<?= $modoEdicao ? 'editar' : 'criar' ?>">
          <?php if ($modoEdicao): ?>
            <input type="hidden" name="produtoId" value="<?= (int) $produto['id'] ?>">
          <?php endif; ?>

          <input type="hidden" name="id" id="produtoIdReal" value="<?= $proximoId ?>">
          <label style="margin-bottom: 10px;">ID do produto</label><br>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default"><?= $modoEdicao ? htmlspecialchars($produto['id']) : $proximoId ?></span>
            <input required="true" name="idPDR" id="inputIdPDR" type="text" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['idPDR']) : '' ?>" readonly>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Nome</span>
            <input required="true" id="inputNome" oninput="updatePost();" name="nome" type="text" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['nome']) : '' ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="3"><?= $modoEdicao ? htmlspecialchars($produto['descricao']) : '' ?></textarea>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Modelo</span>
            <input required="true" name="modelo" type="text" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['modelo']) : '' ?>">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Tamanho</span>
            <input required="true" name="tamanho" type="number" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['tamanho']) : '' ?>">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Cor</span>
            <input required="true" name="cor" type="text" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['cor']) : '' ?>">
          </div>

          <!-- CATEGORIA -->
          <div class="input-group mb-3">
            <span class="input-group-text">Categoria</span>
            <select name="categoria" id="selectCategoria" class="form-select">
              <option value="">Nenhuma</option>
              <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($modoEdicao && (int)$produto['categoria'] === (int)$c['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#modalCategoria">+</button>
          </div>

          <!-- COLEÇÃO -->
          <div class="input-group mb-3">
            <span class="input-group-text">Coleção</span>
            <select name="colecao" id="selectColecao" class="form-select">
              <option value="">Nenhuma</option>
              <?php foreach ($colecoes as $co): ?>
                <option value="<?= $co['id'] ?>" <?= ($modoEdicao && (int)$produto['colecao'] === (int)$co['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($co['nome']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#modalColecao">+</button>
          </div>

          <div class="input-group mb-3">
              <span class="input-group-text">Capa</span>
              <input id="inputCapa" name="capa" type="file" accept="image/*" class="form-control" onchange="handleImageUpload(event)" <?= $modoEdicao ? '' : 'required' ?>>
              <button type="button" class="btn btn-danger" onclick="limparCapa()">✕</button>
          </div>
          <?php if ($modoEdicao && $produto['capa']): ?>
            <?php $capaPreview = trim((string) ($produto['capa'] ?? '')); if ($capaPreview !== '' && !preg_match('#^(https?:)?//#', $capaPreview) && !str_starts_with($capaPreview, '../')) { $capaPreview = preg_match('#^assets/#', $capaPreview) ? '../' . $capaPreview : (str_starts_with($capaPreview, './') ? '../' . ltrim($capaPreview, './') : '../' . ltrim($capaPreview, './')); } ?>
            <div class="mb-3">
              <img src="<?= htmlspecialchars($capaPreview) ?>" style="max-width:120px;border-radius:8px;">
              <div class="form-text">Capa atual — só envie um arquivo acima se quiser trocar.</div>
            </div>
          <?php endif; ?>

          <script>
          function gerarCodigoProduto(nome, id) {
              const texto = (nome || '')
                  .normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .trim()
                  .split(/\s+/)
                  .filter(Boolean)
                  .map((palavra) => palavra.charAt(0).toUpperCase())
                  .join('');

              return (texto || 'PR') + String(id || 1);
          }

          function atualizarIdProduto() {
              const nome = document.getElementById('inputNome')?.value || '';
              const id = document.getElementById('produtoIdReal')?.value || 1;
              const campo = document.getElementById('inputIdPDR');

              if (campo && !campo.dataset.edicao) {
                  campo.value = gerarCodigoProduto(nome, id);
              }
          }

          function limparCapa() {
              document.getElementById("inputCapa").value = "";
          }

          document.addEventListener('DOMContentLoaded', function () {
              const campo = document.getElementById('inputIdPDR');
              const nomeInput = document.getElementById('inputNome');

              if (campo && !campo.value) {
                  atualizarIdProduto();
              }

              if (campo) {
                  campo.dataset.edicao = <?= $modoEdicao ? 'true' : 'false' ?>;
              }

              if (nomeInput) {
                  nomeInput.addEventListener('input', atualizarIdProduto);
              }
          });
          </script>

          <div class="input-group mb-3">
            <span class="input-group-text">Custo</span>
            <input required="true" name="custo" step="0.01" type="number" class="form-control" value="<?= $modoEdicao ? htmlspecialchars((string) ($produto['custo'] ?? 0)) : '0' ?>">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">R$</span>
            <input required="true" id="inputValor" oninput="updatePost();" name="valor" step="0.01" type="number" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['valor']) : '' ?>">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">Estoque</span>
            <input required="true" name="estoque" type="number" class="form-control" value="<?= $modoEdicao ? htmlspecialchars($produto['estoque']) : '' ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Imagens</label>
            <?php if ($modoEdicao && !empty($produto['imagem'])): ?>
              <div class="form-text mb-2">Já existem imagens salvas — só adicione arquivos abaixo se quiser trocar todas elas.</div>
            <?php endif; ?>
            <div id="imagens">
              <script>
              function adicionarCampoImagem() {
                  const container = document.getElementById("imagens");
                  const div = document.createElement("div");
                  div.className = "input-group mb-3";
                  const span = document.createElement("span");
                  span.className = "input-group-text";
                  span.innerText = "Imagem";
                  const input = document.createElement("input");
                  input.type = "file";
                  input.name = "imagens[]";
                  input.accept = "image/*";
                  input.className = "form-control";
                  const botao = document.createElement("button");
                  botao.type = "button";
                  botao.className = "btn btn-danger";
                  botao.innerHTML = "✕";
                  botao.onclick = function () { div.remove(); };
                  input.onchange = function () {
                      if (container.lastElementChild === div) {
                          adicionarCampoImagem();
                      }
                  };
                  div.appendChild(span);
                  div.appendChild(input);
                  div.appendChild(botao);
                  container.appendChild(div);
              }
              adicionarCampoImagem();
              </script>
            </div>
          </div>
      </form>
      </div>

      <!-- pré-visualização do post -->
      <div class="postagem fixed" id="postagem">
        <div class="post" id="post">
          <div class="card" style="width: 18rem;" data-bs-theme="dark">
            <?php $cardCapa = ''; if ($modoEdicao && !empty($produto['capa'])) { $cardCapa = trim((string) $produto['capa']); if (!preg_match('#^(https?:)?//#', $cardCapa) && !str_starts_with($cardCapa, '../')) { $cardCapa = preg_match('#^assets/#', $cardCapa) ? '../' . $cardCapa : (str_starts_with($cardCapa, './') ? '../' . ltrim($cardCapa, './') : '../' . ltrim($cardCapa, './')); } } ?>
            <img id="cardCapa" src="<?= htmlspecialchars($cardCapa) ?>" class="card-img-top" alt="...">
            <div class="card-body">
              <h5 class="card-title" id="cardName"></h5>
              <h6 class="card-preco" id="cardValor"></h6>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- MODAL DESCARTAR -->
    <div data-bs-theme="dark" class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5">Atenção</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">Você não salvou o post. Se sair agora vai descartar o post</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-outline-danger" onclick="window.location.href='./produtos-lista.php'">Descartar post</button>
          </div>
        </div>
      </div>
    </div>

<!-- MODAL CRIAR CATEGORIA -->
<div data-bs-theme="dark" class="modal fade" id="modalCategoria" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Nova categoria</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" id="novaCategoriaNome">
        </div>
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea class="form-control" id="novaCategoriaDescricao" rows="2"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Capa</label>
          <input type="file" accept="image/*" class="form-control" id="novaCategoriaCapa">
        </div>
        <div class="form-text" id="erroCategoria" style="color:#f88;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="criarCategoria()">Criar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CRIAR COLEÇÃO -->
<div data-bs-theme="dark" class="modal fade" id="modalColecao" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Nova coleção</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" id="novaColecaoNome">
        </div>
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea class="form-control" id="novaColecaoDescricao" rows="2"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Capa</label>
          <input type="file" accept="image/*" class="form-control" id="novaColecaoCapa">
        </div>
        <div class="form-text" id="erroColecao" style="color:#f88;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="criarColecao()">Criar</button>
      </div>
    </div>
  </div>
</div>


    <script>
      updatePost();
      function updatePost() {
        const name = document.getElementById('inputNome').value || 'Nome do Produto';
        const price = document.getElementById('inputValor').value || '0,00';
        document.getElementById('cardName').textContent = name;
        document.getElementById('cardValor').textContent = 'R$ ' + price;
      }

      function handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
          document.getElementById('cardCapa').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }

      async function criarCategoria() {
        const nome = document.getElementById('novaCategoriaNome').value.trim();
        const descricao = document.getElementById('novaCategoriaDescricao').value.trim();
        const erro = document.getElementById('erroCategoria');

        if (!nome) { erro.textContent = 'Digite um nome.'; return; }

        const form = new FormData();
        form.append('nome', nome);
        form.append('descricao', descricao);

        const resp = await fetch('./api/categoria.php', { method: 'POST', body: form });
        const dados = await resp.json();

        if (dados.erro) { erro.textContent = dados.erro; return; }

        const select = document.getElementById('selectCategoria');
        const option = document.createElement('option');
        option.value = dados.id;
        option.textContent = dados.nome;
        option.selected = true;
        select.appendChild(option);

        bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
        document.getElementById('novaCategoriaNome').value = '';
        document.getElementById('novaCategoriaDescricao').value = '';
        erro.textContent = '';
      }

      async function criarColecao() {
        const nome = document.getElementById('novaColecaoNome').value.trim();
        const descricao = document.getElementById('novaColecaoDescricao').value.trim();
        const erro = document.getElementById('erroColecao');

        if (!nome) { erro.textContent = 'Digite um nome.'; return; }

        const form = new FormData();
        form.append('nome', nome);
        form.append('descricao', descricao);

        const resp = await fetch('./api/colecao.php', { method: 'POST', body: form });
        const dados = await resp.json();

        if (dados.erro) { erro.textContent = dados.erro; return; }

        const select = document.getElementById('selectColecao');
        const option = document.createElement('option');
        option.value = dados.id;
        option.textContent = dados.nome;
        option.selected = true;
        select.appendChild(option);

        bootstrap.Modal.getInstance(document.getElementById('modalColecao')).hide();
        document.getElementById('novaColecaoNome').value = '';
        document.getElementById('novaColecaoDescricao').value = '';
        erro.textContent = '';
      }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>