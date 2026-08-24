<?php
require_once __DIR__ . '/auth.php';

include 'autoloader.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styleInfos.css">
    <style>
      /* ===== Editor de moldura ===== */
      .editor-viewport {
        width: 260px;
        height: 260px;
        margin: 0 auto;
        position: relative;
        cursor: grab;
      }
      .editor-viewport.dragging { cursor: grabbing; }
      .editor-frame {
        width: 100%;
        height: 100%;
        background-repeat: no-repeat;
      }
      .moldura-frame-wrapper {
        width: 180px;
        height: 180px;
        margin: 0 auto;
        background-repeat: no-repeat;
      }

      /* grade de formatos padrão */
      .shape-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
      }
      .shape-btn {
        width: 42px;
        height: 42px;
        background: #2a2a2a;
        border: 2px solid #444;
        border-radius: 6px;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .shape-btn.active { border-color: #0d6efd; background: #1c3a5e; }
      .shape-btn-icon {
        width: 100%;
        height: 100%;
        background: #ddd;
      }
      .none { display: none !important; }
    </style>
  </head>
  <body>

    <header>
      <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
          <a class="navbar-brand">Criar post</a>
          <div class="d-flex">
            <button class="btn btn-outline-danger btn-form" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Descartar o post</button>
            <button class="btn btn-outline-success btn-form" form="formAnum" type="submit">Salvar</button>
          </div>
        </div>
      </nav>
    </header>

    <main>
      <div class="formulario">
        <form id="formAnum" data-bs-theme="dark" method="post" action="./Controllers/infosController.php" enctype="multipart/form-data">

          <div class="form-floating mb-3">
            <input required="true" name="id" type="number" class="form-control no-decor" id="floatingInput" value="1" >
            <label for="floatingInput">Id</label>
          </div>
            <label style="margin-bottom: 10px;">Id produto</label><br>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">1</span>
            <input required="true" name="idPDR" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Nome</span>
            <input required="true" id="inputNome" oninput="updatePost();" name="nome" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Modelo</span>
            <input required="true" name="modelo" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Tamanho</span>
            <input required="true" name="tamanho" type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Cor</span>
            <input required="true" name="cor" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>

          <div class="input-group mb-3">
              <span class="input-group-text">Capa</span>

              <input
                  id="inputCapa"
                  name="capa"
                  type="file"
                  accept="image/*"
                  class="form-control"
                  onchange="handleImageUpload(event)"
                  required>

              <button type="button" class="btn btn-danger" onclick="limparCapa()">✕</button>
          </div>

          <div class="mb-3">
            <button type="button" class="btn btn-outline-info" id="btnMoldura" disabled onclick="abrirEditorMoldura()">
              🖼️ Escolher moldura da capa
            </button>
          </div>

          <!-- Campos que vão salvar no banco junto com o resto do produto -->
          <input type="hidden" name="moldura_tipo" id="molduraTipo" value="circle">
          <input type="hidden" name="moldura_zoom" id="molduraZoom" value="150">
          <input type="hidden" name="moldura_x" id="molduraX" value="50">
          <input type="hidden" name="moldura_y" id="molduraY" value="50">
          <input type="hidden" name="moldura_custom_svg" id="molduraCustomSvg" value="">

          <script>
          function limparCapa() {
              document.getElementById("inputCapa").value = "";
              document.getElementById("btnMoldura").disabled = true;
          }
          </script>

          <div class="input-group mb-3">
            <span class="input-group-text">R$</span>
            <span class="input-group-text">0.00</span>
            <input required="true" id="inputValor" oninput="updatePost();" name="valor" step="0.01" type="number" class="form-control" aria-label="Dollar amount (with dot and two decimal places)">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Estoque</span>
            <input required="true" name="estoque" type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="mb-3">

            <label class="form-label">Imagens</label>
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

                  botao.onclick = function () {
                      div.remove();
                  };

                  input.onchange = function () {

                      // Quando escolher um arquivo,
                      // cria automaticamente outro campo.
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

      <div class="postagem fixed" id="postagem">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
          <input type="radio" class="btn-check" name="btnradio" oninput="noneCarrocelUpdate();" id="btnradio1" autocomplete="off" checked>
          <label class="btn btn-outline-primary" for="btnradio1">Card</label>
          
          <input type="radio" class="btn-check" name="btnradio" id="btnradio3" oninput="noneCardUpdate();" autocomplete="off">
          <label class="btn btn-outline-primary" for="btnradio3">Informaçoes</label>
        </div>



        <div class="post" id="post">
        <div class="card" style="width: 18rem;" data-bs-theme="dark">
          <div class="moldura-frame-wrapper" id="cardCapaFrame"></div>
          <div class="card-body">
            <h5 class='card-title' id='cardName'></h5>
            <h6 class='card-preco' id='cardValor'></h6>
          </div>
        </div>
      </div>

      <div class="none infos" id="infos">
        <!-- anuncio -->
            <div class="anuncio">
              <div class="fotos">
          <div class="carrocel" id="carrocel">
                  <div id="carouselExampleIndicators" class="carousel slide">

            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="https://marketplace.canva.com/wUgTo/MAGiKZwUgTo/1/tl/canva-avatar-icon-MAGiKZwUgTo.png" class=" img d-block w-100" alt="...">
              </div>
              <div class="carousel-item">
                <img src="   https://cdn-icons-png.flaticon.com/512/5064/5064052.png "img class="d-block w-100" alt="...">
              </div>
              <div class="carousel-item">
                <img src="https://lh3.googleusercontent.com/a/ACg8ocIcC9k0lsuZkDapdNakRqlss7SMkojqtLooZLGqpw2aGMrpKsI=s360-c-no" class="img d-block w-100" alt="...">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>

            <div class="btns">
              <button id="btn1" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" onclick="btnUpdate(1)" class="btnC" aria-label="Slide 1"><img src="https://marketplace.canva.com/wUgTo/MAGiKZwUgTo/1/tl/canva-avatar-icon-MAGiKZwUgTo.png" class="btnLogo" alt="..."></button>
              <button id="btn2" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" onclick="btnUpdate(2)" class="btnC" aria-label="Slide 2"><img src="   https://cdn-icons-png.flaticon.com/512/5064/5064052.png " class="btnLogo" alt="..."></button>
              <button id="btn3" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" onclick="btnUpdate(3)" class="btnC" aria-label="Slide 3"><img src="https://lh3.googleusercontent.com/a/ACg8ocIcC9k0lsuZkDapdNakRqlss7SMkojqtLooZLGqpw2aGMrpKsI=s360-c-no" class="btnLogo" alt="..."></button>
            </div>
          </div>
              </div>
          <div class="infosAnuncio">
            <div class="Titulos">
                <h5 class='card-title' id='anuncioName'></h5>
            </div>
            <div class="Preco">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
            <div class="frete">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
            <div class="cor">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
            <div class="tamanho">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
            <div class="quantidade">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
            <div class="botao">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div data-bs-theme="dark" class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Atenção</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Você não salvou o post.
            Se sair agora vai descartar o post
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-outline-danger">Descartar post</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Modal: editor de moldura da capa ===== -->
    <div data-bs-theme="dark" class="modal fade" id="modalMoldura" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar capa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div class="mb-3">
              <label class="form-label d-block">Formato da moldura</label>
              <div class="shape-grid" id="shapeGrid"></div>

              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleCustomSvg()">
                + Moldura personalizada (SVG)
              </button>
              <div class="mb-3 mt-2 none" id="customSvgWrapper">
                <label class="form-label">Arquivo SVG da moldura</label>
                <input type="file" accept=".svg,image/svg+xml" class="form-control" id="inputCustomSvg" onchange="handleCustomSvg(event)">
                <div class="form-text">
                  O SVG precisa ter um único path/shape definindo o contorno — não precisa mais ser viewBox "0 0 100 100", o editor lê o viewBox do próprio arquivo e escala sozinho.
                  Dá pra reaproveitar os trevos e quadrifólios góticos que você já vetorizou como moldura personalizada.
                </div>
              </div>
            </div>

            <div class="editor-viewport" id="editorViewport">
              <div class="editor-frame" id="editorFrame"></div>
            </div>
            <div class="form-text text-center mb-3">Arraste a imagem para posicionar dentro da moldura.</div>

            <div class="mb-3">
              <label class="form-label">Zoom</label>
              <input type="range" class="form-range" min="100" max="300" value="150" id="zoomRange" oninput="updateZoom(this.value)">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" onclick="saveMoldura()">Aplicar</button>
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
                document.getElementById('anuncioName').textContent = name;
                document.getElementById('anuncioValor').textContent = 'R$ ' + price;
              }

          let uploadedImageURL = '';

          function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
              uploadedImageURL = e.target.result;
              editorState.imageUrl = uploadedImageURL;
              // nova imagem começa centralizada, sem zoom extra
              editorState.zoom = 150;
              editorState.x = 50;
              editorState.y = 50;
              aplicarMolduraNoPreview();
              document.getElementById('btnMoldura').disabled = false;
            };
            reader.readAsDataURL(file);
          }

          function noneCardUpdate(){
            const post = document.getElementById('post');
            post.classList = 'none post';
            const infos = document.getElementById('infos');
            infos.classList = 'infos';
            const postagem = document.getElementById('postagem');
            postagem.classList = 'postagem';
          }
          function noneCarrocelUpdate(){
            const post = document.getElementById('post');
            post.classList = 'post';
            const infos = document.getElementById('infos');
            infos.classList = 'none infos';
            const postagem = document.getElementById('postagem');
            postagem.classList = 'postagem fixed';
          }

          function btnUpdate(btn){
            var botao = document.getElementById('btn1');
            botao.classList = 'btnC';
            var botao = document.getElementById('btn2');
            botao.classList = 'btnC';
            var botao = document.getElementById('btn3');
            botao.classList = 'btnC';
            var botao = document.getElementById('btn'+btn);
            botao.classList = 'btnC activeBtn';
          }

          /* ===================================================
             Editor de moldura da capa
             Guarda: tipo de moldura, zoom (%) e posição x/y (%)
             — dá pra salvar essas colunas no banco e remontar
             a imagem depois só com CSS (background-size /
             background-position / clip-path), sem reprocessar
             o arquivo original.
          =================================================== */

          // formatos padrão (clip-path em %, funciona em qualquer tamanho de caixa)
          const MOLDURA_SHAPES = [
            { id: 'circle',        label: 'Círculo',          clip: 'circle(50% at 50% 50%)' },
            { id: 'square',        label: 'Quadrado',          clip: 'inset(0)' },
            { id: 'rounded',       label: 'Quadrado arred.',   clip: 'inset(0 round 18%)' },
            { id: 'diamond',       label: 'Losango',           clip: 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)' },
            { id: 'triangle-up',   label: 'Triângulo',         clip: 'polygon(50% 0%, 0% 100%, 100% 100%)' },
            { id: 'triangle-down', label: 'Triângulo invertido', clip: 'polygon(0% 0%, 100% 0%, 50% 100%)' },
            { id: 'pentagon',      label: 'Pentágono',         clip: 'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)' },
            { id: 'hexagon',       label: 'Hexágono',          clip: 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)' },
            { id: 'star',          label: 'Estrela',           clip: 'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)' },
            { id: 'star-thin',     label: 'Estrela fina',      clip: 'polygon(50% 0%, 57% 36%, 100% 36%, 65% 58%, 79% 100%, 50% 74%, 21% 100%, 35% 58%, 0% 36%, 43% 36%)' },
            { id: 'arrow-r',       label: 'Seta direita',      clip: 'polygon(0% 20%, 60% 20%, 60% 0%, 100% 50%, 60% 100%, 60% 80%, 0% 80%)' },
            { id: 'arrow-l',       label: 'Seta esquerda',     clip: 'polygon(100% 20%, 40% 20%, 40% 0%, 0% 50%, 40% 100%, 40% 80%, 100% 80%)' },
            { id: 'arrow-d',       label: 'Seta para baixo',   clip: 'polygon(20% 0%, 80% 0%, 80% 60%, 100% 60%, 50% 100%, 0% 60%, 20% 60%)' },
            { id: 'chevron-l',     label: 'Chevron esquerda',  clip: 'polygon(100% 0%, 40% 50%, 100% 100%, 70% 100%, 10% 50%, 70% 0%)' },
            { id: 'chevron-r',     label: 'Chevron direita',   clip: 'polygon(0% 0%, 60% 50%, 0% 100%, 30% 100%, 90% 50%, 30% 0%)' },
            { id: 'bubble',        label: 'Balão de fala',     clip: 'polygon(0% 0%, 100% 0%, 100% 75%, 78% 75%, 65% 100%, 60% 75%, 0% 75%)' },
            { id: 'bubble-2',      label: 'Balão de fala 2',   clip: 'polygon(0% 0%, 100% 0%, 100% 75%, 35% 75%, 22% 100%, 20% 75%, 0% 75%)' }
          ];

          function getClipPath(shapeId){
            if (shapeId === 'custom') {
              return editorState.customSvg ? 'url(#customMolduraClip)' : 'none';
            }
            const found = MOLDURA_SHAPES.find(s => s.id === shapeId);
            return found ? found.clip : 'circle(50% at 50% 50%)';
          }

          function montarShapeGrid(){
            const grid = document.getElementById('shapeGrid');
            grid.innerHTML = '';
            MOLDURA_SHAPES.forEach(shape => {
              const btn = document.createElement('div');
              btn.className = 'shape-btn' + (shape.id === editorState.shape ? ' active' : '');
              btn.title = shape.label;
              btn.dataset.shapeId = shape.id;
              btn.onclick = () => setMolduraShape(shape.id);

              const icon = document.createElement('div');
              icon.className = 'shape-btn-icon';
              icon.style.clipPath = shape.clip;

              btn.appendChild(icon);
              grid.appendChild(btn);
            });
          }

          function toggleCustomSvg(){
            const wrapper = document.getElementById('customSvgWrapper');
            const abrindo = wrapper.classList.contains('none');
            wrapper.classList.toggle('none');
            if (abrindo) setMolduraShape('custom');
          }

          let editorState = {
            shape: 'circle',
            zoom: 150,
            x: 50,
            y: 50,
            imageUrl: '',
            customSvg: ''
          };

          function abrirEditorMoldura(){
            if(!uploadedImageURL){
              alert('Selecione uma imagem de capa primeiro.');
              return;
            }
            document.getElementById('zoomRange').value = editorState.zoom;
            montarShapeGrid();
            applyEditorStyle();
            const modal = new bootstrap.Modal(document.getElementById('modalMoldura'));
            modal.show();
          }

          function applyEditorStyle(){
            const clip = getClipPath(editorState.shape);

            const frame = document.getElementById('editorFrame');
            frame.style.clipPath = clip;
            frame.style.backgroundImage = "url('" + editorState.imageUrl + "')";
            frame.style.backgroundSize = editorState.zoom + '% auto';
            frame.style.backgroundPosition = editorState.x + '% ' + editorState.y + '%';

            document.querySelectorAll('#shapeGrid .shape-btn').forEach(btn => {
              btn.classList.toggle('active', btn.dataset.shapeId === editorState.shape);
            });
          }

          function setMolduraShape(shape){
            editorState.shape = shape;
            applyEditorStyle();
          }

          function updateZoom(v){
            editorState.zoom = Number(v);
            applyEditorStyle();
          }

          function handleCustomSvg(event){
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
              const svgText = e.target.result;
              const parser = new DOMParser();
              const doc = parser.parseFromString(svgText, 'image/svg+xml');
              const svgRoot = doc.querySelector('svg');
              const shapeEl = doc.querySelector('path, polygon, circle, ellipse, rect');
              if (!svgRoot || !shapeEl) {
                alert('Não encontrei uma forma válida (path/polygon/circle/rect) dentro do SVG.');
                return;
              }

              // descobre o sistema de coordenadas do SVG original (viewBox, ou largura/altura)
              // pra escalar automaticamente pra dentro da moldura, seja qual for a unidade usada
              let minX = 0, minY = 0, vbWidth, vbHeight;
              const viewBoxAttr = svgRoot.getAttribute('viewBox');
              if (viewBoxAttr) {
                const partes = viewBoxAttr.trim().split(/[\s,]+/).map(Number);
                minX = partes[0]; minY = partes[1]; vbWidth = partes[2]; vbHeight = partes[3];
              } else {
                vbWidth = parseFloat(svgRoot.getAttribute('width')) || 100;
                vbHeight = parseFloat(svgRoot.getAttribute('height')) || 100;
              }
              if (!vbWidth || !vbHeight) {
                alert('Não consegui ler o tamanho (viewBox) do SVG.');
                return;
              }

              editorState.customSvg = shapeEl.outerHTML;
              editorState.shape = 'custom';

              const sx = 1 / vbWidth;
              const sy = 1 / vbHeight;
              const clipTransform = 'scale(' + sx + ' ' + sy + ') translate(' + (-minX) + ' ' + (-minY) + ')';

              let defsSvg = document.getElementById('customMolduraDefs');
              if (!defsSvg) {
                defsSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                defsSvg.setAttribute('id', 'customMolduraDefs');
                defsSvg.style.position = 'absolute';
                defsSvg.style.width = '0';
                defsSvg.style.height = '0';
                document.body.appendChild(defsSvg);
              }
              defsSvg.innerHTML = '<defs><clipPath id="customMolduraClip" clipPathUnits="objectBoundingBox" transform="' + clipTransform + '">' + editorState.customSvg + '</clipPath></defs>';
              applyEditorStyle();
            };
            reader.readAsText(file);
          }

          function saveMoldura(){
            document.getElementById('molduraTipo').value = editorState.shape;
            document.getElementById('molduraZoom').value = editorState.zoom;
            document.getElementById('molduraX').value = editorState.x.toFixed(1);
            document.getElementById('molduraY').value = editorState.y.toFixed(1);
            document.getElementById('molduraCustomSvg').value = editorState.shape === 'custom' ? editorState.customSvg : '';
            aplicarMolduraNoPreview();
            bootstrap.Modal.getInstance(document.getElementById('modalMoldura')).hide();
          }

          function aplicarMolduraNoPreview(){
            const frame = document.getElementById('cardCapaFrame');
            if (!frame) return;

            frame.style.clipPath = getClipPath(editorState.shape);
            frame.style.backgroundImage = "url('" + editorState.imageUrl + "')";
            frame.style.backgroundSize = editorState.zoom + '% auto';
            frame.style.backgroundPosition = editorState.x + '% ' + editorState.y + '%';
          }

          // arrastar para posicionar (mouse + touch)
          (function(){
            const viewport = document.getElementById('editorViewport');

            function startDrag(x, y){
              viewport.dataset.dragging = '1';
              viewport.dataset.lastX = x;
              viewport.dataset.lastY = y;
              viewport.classList.add('dragging');
            }
            function endDrag(){
              viewport.dataset.dragging = '';
              viewport.classList.remove('dragging');
            }
            function moveDrag(x, y){
              if (viewport.dataset.dragging !== '1') return;
              const dx = x - Number(viewport.dataset.lastX);
              const dy = y - Number(viewport.dataset.lastY);
              viewport.dataset.lastX = x;
              viewport.dataset.lastY = y;
              const rect = viewport.getBoundingClientRect();
              editorState.x = Math.min(100, Math.max(0, editorState.x - (dx / rect.width) * 100));
              editorState.y = Math.min(100, Math.max(0, editorState.y - (dy / rect.height) * 100));
              applyEditorStyle();
            }

            viewport.addEventListener('mousedown', (e) => startDrag(e.clientX, e.clientY));
            window.addEventListener('mouseup', endDrag);
            window.addEventListener('mousemove', (e) => moveDrag(e.clientX, e.clientY));

            viewport.addEventListener('touchstart', (e) => {
              startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }, { passive: true });
            viewport.addEventListener('touchend', endDrag);
            viewport.addEventListener('touchmove', (e) => {
              moveDrag(e.touches[0].clientX, e.touches[0].clientY);
              e.preventDefault();
            }, { passive: false });

            // zoom com scroll do mouse, sem precisar do slider
            viewport.addEventListener('wheel', (e) => {
              e.preventDefault();
              editorState.zoom = Math.min(300, Math.max(100, editorState.zoom - e.deltaY * 0.2));
              document.getElementById('zoomRange').value = editorState.zoom;
              applyEditorStyle();
            }, { passive: false });
          })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>