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

$capa = $anuncio['capa'];
$imagens = json_decode($anuncio['imagem'], true);

$todasImagens = [$capa];

if (is_array($imagens)) {
    $todasImagens = array_merge($todasImagens, $imagens);
}
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
    <p>

<div class="anuncio">
    <div class="Titulos">
      <h5 id="anuncioName"><?= $anuncio['nome'] ?></h5>
    </div>
    <div class="conteudo">
    <div class="fotos">

        <div class="imagemPrincipal">
            <img
                id="imagemGrande"
                src="<?= $todasImagens[0] ?>"
                class="imgGrande"
                onclick="abrirImagem()">
        </div>

        <div class="miniaturas">

            <button class="seta" onclick="voltar()">❮</button>

            <div class="viewport">

                <div id="listaMiniaturas" class="lista">

                    <?php foreach ($todasImagens as $imagem) { ?>

                        <img
                            src="<?= $imagem ?>"
                            class="btnLogo"
                            onclick="trocarImagem('<?= $imagem ?>')">

                    <?php } ?>

                </div>

            </div>

            <button class="seta" onclick="avancar()">❯</button>

        </div>

    </div>

    <div id="overlayImagem" onclick="fecharImagem()">
        <img id="imagemExpandida">
    </div>

    <div class="infosAnuncio">

        <div class="Preco">
            <h6 id="anuncioValor"><?= $anuncio['valor'] ?></h6>
        </div>

        <div class="frete">
            <!-- Frete -->
        </div>

        <div class="cor">
            <h6><?= $anuncio['cor'] ?></h6>
        </div>

        <div class="tamanho">
            <h6><?= $anuncio['tamanho'] ?></h6>
        </div>

        <div class="quantidade">
            <!-- Quantidade -->
        </div>

        <div class="botao">
            <!-- Botão Comprar -->
        </div>

    </div>
</div>
</div>

    <script>
      let indice = 0;

const lista = document.getElementById("listaMiniaturas");
const miniaturas = lista.querySelectorAll(".btnLogo");
const larguraMiniatura = document.querySelector(".btnLogo").offsetWidth + 10;

function trocarImagem(src){
    document.getElementById("imagemGrande").src = src;
}

function abrirImagem(){

    const img = document.getElementById("imagemGrande").src;

    document.getElementById("imagemExpandida").src = img;
    document.getElementById("overlayImagem").classList.add("ativo");
}

function fecharImagem(){
    document.getElementById("overlayImagem").classList.remove("ativo");
}


const visiveis = 4;


function atualizarMiniaturas() {

    const lista = document.getElementById("listaMiniaturas");
    const viewport = document.querySelector(".viewport");

    const max = lista.scrollWidth - viewport.clientWidth;

    let deslocamento = indice * larguraMiniatura;

    if (deslocamento > max) {
        deslocamento = max;
    }

    lista.style.transform = `translateX(-${deslocamento}px)`;
}

function avancar() {

    const lista = document.getElementById("listaMiniaturas");
    const viewport = document.querySelector(".viewport");

    const max = lista.scrollWidth - viewport.clientWidth;

    const atual = indice * 90;

    if (atual < max) {
        indice++;
        atualizarMiniaturas();
    }
}

function voltar() {

    if (indice > 0) {
        indice--;
        atualizarMiniaturas();
    }

}
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>