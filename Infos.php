<?php

namespace Controllers;

include 'autoloader.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Se o parâmetro 'id' não estiver presente, você pode definir um valor padrão ou lidar com o erro de outra forma
    $id = 1; // Valor padrão, por exemplo
}

$controller = new infosController();
$anuncio = $controller->pageInfo($id);


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
    <h1>Hello, world!</h1>
    <p>

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
                <h5 class='card-title' id='anuncioName'><?php echo $anuncio['nome']; ?></h5>
            </div>
            <div class="Preco">
                <h6 class='card-preco' id='anuncioValor'><?php echo $anuncio['valor']; ?></h6>
            </div>
            <div class="frete">
              <h6 class='card-preco' id='anuncioValor'> <!-- <?php echo $anuncio['frete']; ?> --></h6>
            </div>
            <div class="cor">
                <h6 class='card-preco' id='anuncioValor'><?php echo $anuncio['cor']; ?></h6>
            </div>
            <div class="tamanho">
                <h6 class='card-preco' id='anuncioValor'><?php echo $anuncio['tamanho']; ?></h6>
            </div>
            <div class="quantidade">
                <h6 class='card-preco' id='anuncioValor'><!-- <?php echo $anuncio['quantidade']; ?> --></h6>
            </div>
            <div class="botao">
                <h6 class='card-preco' id='anuncioValor'></h6>
            </div>
          </div>
        </div>
      </div>
        <?php

        ?>
    </p>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>