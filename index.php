<?php

use Controllers\ListController;

include 'autoloader.php';
?>

<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styleIndex.css">
  </head>
  <body>

  <header id="Home">
        <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#Home"><n class="Titulo">Computer store</n></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#Home">Home</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="#Destaques">Destaques</a>
                    </li>
                    <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Produtos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#Celulares">Celulares</a></li>
                        <li><a class="dropdown-item" href="#Fones">Fones</a></li>
                        <li><a class="dropdown-item" href="#Relogios">Relogios</a></li>
                    </ul>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="#Duvidas">Duvidas</a>
                    </li>
                </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
              <div class="novidades" id="Produtos">
            <div class="card" style="width: 18rem;" data-bs-theme="dark" id="Fones">
                <img src="./Assets/fone.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Plus HB200</h5>
                    <p class="card-text"><del>R$149,90</del><br>
                        <!-- tag "N" eu fiz pra ser mais facil, mas ela não existe -->
                        <N class="prc">$89,91</N> <br> <N class="cndc">40% OFFno Pix </N></p>
                    <a href="#" class="btn btn-primary btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal1">Adicionar ao Carrinho</a>
                </div>
            </div>

            <div class="card" style="width: 18rem;" data-bs-theme="dark" id="Celulares">
            <img src="./Assets/celular.png" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">Galaxy Z flip 7</h5>
                <p class="card-text"><del>R$9.199</del><br>
                        <!-- tag "N" eu fiz pra ser mais facil, mas ela não existe -->
                        <N class="prc">R$5.193,50</N> <br> <N class="cndc">43% OFFno Pix</N></p>
                    <a href="#" class="btn btn-primary btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal2">Adicionar ao Carrinho</a>
            </div>
            </div>

            <div class="card" style="width: 18rem;" data-bs-theme="dark" id="Relogios">
                <img src="./Assets/relogio.png" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Smartwatch S10</h5>
                  <p class="card-text"><del>R$78,90</del><br>
                        <!-- tag "N" eu fiz pra ser mais facil, mas ela não existe -->
                        <N class="prc">31,56</N> <br> <N class="cndc">43% OFFno Pix</N></p>
                  <a href="#" class="btn btn-primary btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal3">Adicionar ao Carrinho</a>
                </div>
            </div>
        </div>

            <div class="Produtos">
                <?php
                $Listar = new ListController;
                $Listar = $Listar->listProdutos();
                ?>
          </div>
    </main>
<footer>
  Instagram: @resinoir<br>
  Telegram: @resinoir<br>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>