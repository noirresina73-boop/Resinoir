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

// Seções de descrição. Cada chave vira um bloco fixo na página; se o
// campo não existir em $anuncio, o bloco mostra um aviso em vez de dar erro.
$secoes = [
    'descricao'   => 'Descrição',
];
?>

<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/styleInfos.css">
  </head>
  <body>

    <div class="device">

        <navbar class="topnav">
            <div class="icon-btn" onclick="window.history.back()">
                <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><path d="M15 18l-6-6 6-6"/></svg>
            </div>
            <div class="wordmark">Resinoir</div>
            <div class="side">
                <div class="icon-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#e9e0c9" stroke-width="1.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                </div>
            </div>
        </navbar>

        <main>

        <div class="anuncio">

            <div class="produto-header">
                <div class="eyebrow">Peça</div>
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
                    <div class="zoom" onclick="abrirImagem()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                            <circle cx="10" cy="10" r="7"/>
                            <path d="M10 7v6M7 10h6"/>
                            <path d="M15 15l6 6"/>
                        </svg>
                    </div>
                </div>

                <div class="miniaturas">
                    <div class="viewport">
                        <div id="listaMiniaturas" class="lista">

                            <?php foreach ($todasImagens as $i => $imagem) { ?>

                                <img
                                    src="<?= $imagem ?>"
                                    class="btnLogo <?= $i === 0 ? 'selecionada' : '' ?>"
                                    onclick="trocarImagem('<?= $imagem ?>', this)">

                            <?php } ?>

                        </div>
                    </div>
                </div>

            </div>

            <div id="overlayImagem" onclick="fecharImagem()">
                <img id="imagemExpandida">
            </div>

            <div class="infosAnuncio">

                <div class="Preco">
                    <h6 id="anuncioValor">R$ <?= number_format((float) $anuncio['valor'], 2, ',', '.') ?></h6>
                </div>

                <div class="frete">
                    <!-- Frete -->
                </div>

                <?php if (!empty($anuncio['cor']) || !empty($anuncio['tamanho'])) { ?>
                <div class="detalhes">

                    <?php if (!empty($anuncio['cor'])) { ?>
                    <div class="cor">
                        <span class="rotulo">Cor</span>
                        <h6><?= $anuncio['cor'] ?></h6>
                    </div>
                    <?php } ?>

                    <?php if (!empty($anuncio['tamanho'])) { ?>
                    <div class="tamanho">
                        <span class="rotulo">Tamanho</span>
                        <h6><?= $anuncio['tamanho'] ?></h6>
                    </div>
                    <?php } ?>

                </div>
                <?php } ?>

                <div class="quantidade">
                    <!-- Quantidade -->
                </div>

                <div class="botao">
                    <button class="btn-comprar">Comprar Agora</button>
                    <button class="btn-carrinho">Adicionar ao Carrinho</button>
                </div>

            </div>
            </div>

            <div class="descricaoAnuncio">

                <?php foreach ($secoes as $chave => $rotulo) { ?>
                    <div class="secaoDescricao">
                        <div class="eyebrow"><?= $rotulo ?></div>
                        <div class="conteudoSecao">
                            <?php if (!empty($anuncio[$chave])) { ?>
                                <?= nl2br(htmlspecialchars($anuncio[$chave])) ?>
                            <?php } else { ?>
                                <span class="vazio">Nenhuma informação cadastrada ainda.</span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div>

        </main>

    </div>

    <script>

function trocarImagem(src, elemento){
    document.getElementById("imagemGrande").src = src;
    document.querySelectorAll(".btnLogo").forEach(function(img){
        img.classList.remove("selecionada");
    });
    elemento.classList.add("selecionada");
}

function abrirImagem(){
    const img = document.getElementById("imagemGrande").src;
    document.getElementById("imagemExpandida").src = img;
    document.getElementById("overlayImagem").classList.add("ativo");
}

function fecharImagem(){
    document.getElementById("overlayImagem").classList.remove("ativo");
}

    </script>
  </body>
</html>