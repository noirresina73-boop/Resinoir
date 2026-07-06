<?php
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
        <form id="formAnum" data-bs-theme="dark" method="post" action="./Controllers/infosController.php">

          <div class="form-floating mb-3">
            <input required="true" name="id" type="number" class="form-control no-decor" id="floatingInput" value="1" readonly="true">
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
            <span class="input-group-text" id="inputGroup-sizing-default">Capa</span>
            <input required="true" id="inputCapa" accept="image/*" onchange="handleImageUpload(event)" name="capa" type="file" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
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
            <textarea name="imagens" class="form-control" id="exampleFormControlTextarea1" rows="13">
[
  {
    "id": 1,
    "url": ""
  },
  {
    "id": 2,
    "url": ""
  }
]
            </textarea>
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
          <img id="cardCapa" src="" class="card-img-top" alt="...">
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

          function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
              uploadedImageURL = e.target.result;
              document.getElementById('cardCapa').src = uploadedImageURL;
              document.getElementById('anuncioCapa').src = uploadedImageURL;
              document.getElementById('post-illustration').innerHTML = `<img src="${uploadedImageURL}" class="post-uploaded-img" alt="produto">`;
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>