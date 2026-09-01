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
                <div class="icon-btn"  onclick="window.location.href='./pesquisa.php'">
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
                    <div class="estoque-status" id="statusEstoque">
                        <?= ((int) ($anuncio['estoque'] ?? 0)) <= 0 ? 'Esgotado' : 'Disponível' ?>
                    </div>
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
                    <button class="btn-comprar" id="btnComprarWhatsApp" type="button">Comprar</button>
                    <button class="btn-carrinho" id="btnCalcularFrete" type="button">Calcular Frete</button>
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

    <div id="modalFrete" class="modal-frete" aria-hidden="true">
        <div class="modal-frete-backdrop" data-fechar-frete="true"></div>
        <div class="modal-frete-content" role="dialog" aria-modal="true" aria-labelledby="modalFreteTitulo">
            <button type="button" class="modal-fechar" aria-label="Fechar" data-fechar-frete="true">×</button>
            <div class="eyebrow">Frete</div>
            <h3 id="modalFreteTitulo">Calcular frete</h3>
            <form id="formFrete" class="form-frete">
                <label for="cepFrete">Digite seu CEP</label>
                <input id="cepFrete" name="cep" type="text" inputmode="numeric" maxlength="9" placeholder="Ex.: 88000-000" required>
                <button type="submit" class="btn-frete-submit">Calcular</button>
            </form>
            <div id="resultadoFrete" class="resultado-frete" aria-live="polite"></div>
        </div>
    </div>

    <div id="modalCepOpcional" class="modal-frete modal-cep-opcional" aria-hidden="true">
        <div class="modal-frete-backdrop" data-fechar-cep-opcional="true"></div>
        <div class="modal-frete-content" role="dialog" aria-modal="true" aria-labelledby="modalCepOpcionalTitulo">
            <button type="button" class="modal-fechar" aria-label="Fechar" data-fechar-cep-opcional="true">×</button>
            <div class="eyebrow">Opcional</div>
            <h3 id="modalCepOpcionalTitulo">Quer informar o CEP?</h3>
            <p class="texto-modal-cep">Você pode continuar sem informar o CEP agora e, se preferir, calcular o frete depois.</p>
            <div class="modal-frete-acao-row">
                <button type="button" id="btnContinuarSemCep" class="btn-frete-secundario">Continuar sem CEP</button>
                <button type="button" id="btnInformarCep" class="btn-frete-submit">Informar CEP</button>
            </div>
        </div>
    </div>

    <script>

const whatsappNumero = String.fromCharCode(
    53, 53, 52, 54, 56, 56, 48, 52, 50, 52, 49, 53
);

const produtoNome = <?= json_encode((string) ($anuncio['nome'] ?? 'Produto')) ?>;
const produtoId = <?= json_encode((string) ($anuncio['id'] ?? '')) ?>;
const produtoCapa = <?= json_encode((string) ($capa ?? '')) ?>;
const valorProduto = Number(<?= json_encode((float) ($anuncio['valor'] ?? 0)) ?>) || 0;
const produtoEstoque = Number(<?= json_encode((int) ($anuncio['estoque'] ?? 0)) ?>) || 0;

function atualizarEstadoProduto() {
    const botaoCompra = document.getElementById('btnComprarWhatsApp');
    const statusEstoque = document.getElementById('statusEstoque');

    if (!botaoCompra || !statusEstoque) return;

    if (produtoEstoque <= 0) {
        botaoCompra.textContent = 'Fazer pedido';
        botaoCompra.classList.add('btn-esgotado');
        statusEstoque.textContent = 'Esgotado · Fazer pedido';
        statusEstoque.classList.add('esgotado');
        return;
    }

    botaoCompra.textContent = 'Comprar';
    botaoCompra.classList.remove('btn-esgotado');
    statusEstoque.textContent = 'Disponível';
    statusEstoque.classList.remove('esgotado');
}

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

const FRETE_CACHE_KEY = 'resinoir_ultimo_frete';

function getFreteCache() {
    try {
        const valor = localStorage.getItem(FRETE_CACHE_KEY);
        return valor ? JSON.parse(valor) : {};
    } catch (erro) {
        return {};
    }
}

function setFreteCache(dados) {
    try {
        localStorage.setItem(FRETE_CACHE_KEY, JSON.stringify(dados));
    } catch (erro) {
        console.warn('Não foi possível salvar o cache do frete.', erro);
    }
}

function getEnderecoResumo(dados) {
    const rua = dados?.rua || '';
    const bairro = dados?.bairro || '';
    const cidade = dados?.cidade || '';
    const estado = dados?.estado || '';

    if (rua || bairro) {
        return [rua, bairro].filter(Boolean).join(', ');
    }

    if (cidade || estado) {
        return [cidade, estado].filter(Boolean).join(' - ');
    }

    return '';
}

function abrirModalFrete() {
    const modal = document.getElementById('modalFrete');
    if (!modal) return;
    modal.classList.add('ativo');
    modal.setAttribute('aria-hidden', 'false');

    const cache = getFreteCache();
    const input = document.getElementById('cepFrete');
    if (input && cache.cep) {
        input.value = cache.cep;
    }

    const resultado = document.getElementById('resultadoFrete');
    if (resultado && cache.cep && cache.rua) {
        const resumo = getEnderecoResumo(cache);
        resultado.textContent = `Último endereço salvo: ${resumo || cache.cep}.`;
        resultado.classList.add('ok');
    } else if (resultado) {
        resultado.textContent = '';
        resultado.classList.remove('ok', 'erro');
    }

    setTimeout(() => input && input.focus(), 50);
}

function fecharModalFrete() {
    const modal = document.getElementById('modalFrete');
    if (!modal) return;
    modal.classList.remove('ativo');
    modal.setAttribute('aria-hidden', 'true');
    document.getElementById('formFrete')?.reset();
    document.getElementById('resultadoFrete').textContent = '';
}

function abrirModalCepOpcional() {
    const modal = document.getElementById('modalCepOpcional');
    if (!modal) return;
    modal.classList.add('ativo');
    modal.setAttribute('aria-hidden', 'false');
}

function fecharModalCepOpcional() {
    const modal = document.getElementById('modalCepOpcional');
    if (!modal) return;
    modal.classList.remove('ativo');
    modal.setAttribute('aria-hidden', 'true');
}

async function calcularFrete(cep) {
    const digits = (cep || '').replace(/\D/g, '');

    if (!/^\d{8}$/.test(digits)) {
        return { valid: false, mensagem: 'Digite um CEP válido com 8 dígitos.' };
    }

    try {
        const cepFormatado = digits.replace(/(\d{5})(\d{3})/, '$1-$2');

        const resposta = await fetch('./api/calcular_frete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ endereco: digits })
        });

        const dados = await resposta.json();

        if (!resposta.ok || !dados.sucesso) {
            return { valid: false, mensagem: dados.erro || 'Não foi possível calcular o frete.' };
        }

        const enderecoResumo = getEnderecoResumo(dados);
        const mensagemEntrega = enderecoResumo ? `Entregamos em ${enderecoResumo}. ` : '';

        const retorno = {
            valid: true,
            mensagem: `${mensagemEntrega}Frete estimado para ${cepFormatado}: R$ ${Number(dados.valor_numero || 0).toFixed(2).replace('.', ',')}.`,
            valor: Number(dados.valor_numero || 0),
            prazo: `${Number(dados.distancia || 0).toFixed(1).replace('.', ',')} km aproximados`,
            cep: digits,
            rua: dados.rua || '',
            bairro: dados.bairro || '',
            cidade: dados.cidade || '',
            estado: dados.estado || ''
        };

        setFreteCache(retorno);
        return retorno;
    } catch (erro) {
        console.error(erro);
        return { valid: false, mensagem: 'Não foi possível consultar o cálculo de frete no momento.' };
    }
}

function continuarCompraSemCep() {
    const texto = produtoEstoque <= 0
        ? `Olá! Gostaria de fazer o pedido do produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20confirmar%20a%20disponibilidade%20e%20o%20valor%20final.`
        : `Olá! Gostaria de comprar o produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20mais%20informações%20sobre%20a%20entrega%20e%20pagamento.`;
    const url = `https://wa.me/${whatsappNumero}?text=${texto}`;
    window.open(url, '_blank');
}

function prepararLinkCompra() {
    const ultimoFrete = getFreteCache();

    if (!ultimoFrete || !ultimoFrete.cep) {
        abrirModalCepOpcional();
        return;
    }

    const enderecoTexto = `%0A%0AEndereço salvo:%20${encodeURIComponent((ultimoFrete.rua || 'Rua não informada') + ', ' + (ultimoFrete.bairro || 'bairro não informado'))}%0ACEP:%20${encodeURIComponent(ultimoFrete.cep)}`;
    const texto = produtoEstoque <= 0
        ? `Olá! Gostaria de fazer o pedido do produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20confirmar%20a%20disponibilidade%20e%20o%20valor%20final.${enderecoTexto}`
        : `Olá! Gostaria de comprar o produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20mais%20informações%20sobre%20a%20entrega%20e%20pagamento.${enderecoTexto}`;
    const url = `https://wa.me/${whatsappNumero}?text=${texto}`;
    window.open(url, '_blank');
}

atualizarEstadoProduto();
document.getElementById('btnComprarWhatsApp')?.addEventListener('click', prepararLinkCompra);
document.getElementById('btnCalcularFrete')?.addEventListener('click', abrirModalFrete);
document.querySelectorAll('[data-fechar-frete]').forEach(function(botao){
    botao.addEventListener('click', fecharModalFrete);
});
document.querySelectorAll('[data-fechar-cep-opcional]').forEach(function(botao){
    botao.addEventListener('click', fecharModalCepOpcional);
});
document.getElementById('btnInformarCep')?.addEventListener('click', function(){
    fecharModalCepOpcional();
    abrirModalFrete();
});
document.getElementById('btnContinuarSemCep')?.addEventListener('click', function(){
    fecharModalCepOpcional();
    continuarCompraSemCep();
});

document.getElementById('formFrete')?.addEventListener('submit', async function(event){
    event.preventDefault();
    const cepInput = document.getElementById('cepFrete');
    const resultado = document.getElementById('resultadoFrete');
    resultado.textContent = 'Calculando frete...';
    resultado.classList.remove('erro', 'ok');

    const calculo = await calcularFrete(cepInput.value);

    if (!calculo.valid) {
        resultado.textContent = calculo.mensagem;
        resultado.classList.add('erro');
        resultado.classList.remove('ok');
        return;
    }

    const resumo = getEnderecoResumo(calculo);
    resultado.textContent = resumo ? `${calculo.mensagem} Endereço: ${resumo}.` : calculo.mensagem;
    resultado.classList.remove('erro');
    resultado.classList.add('ok');
});

    </script>

    <style>
      .modal-frete {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1200;
      }

      .modal-frete.ativo {
        display: flex;
      }

      .modal-frete-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(8, 8, 10, 0.72);
      }

      .modal-frete-content {
        position: relative;
        width: min(92vw, 420px);
        background: #17171a;
        border: 1px solid rgba(212, 176, 119, 0.32);
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.38);
        color: #f5efe6;
      }

      .modal-fechar {
        position: absolute;
        top: 0.8rem;
        right: 0.9rem;
        background: transparent;
        border: 0;
        color: #f5efe6;
        font-size: 1.8rem;
        line-height: 1;
        cursor: pointer;
      }

      .form-frete {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        margin-top: 1rem;
      }

      .form-frete input {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(212, 176, 119, 0.35);
        background: rgba(255, 255, 255, 0.02);
        color: #fff;
        padding: 0.8rem 0.9rem;
      }

      .btn-frete-submit {
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #d4b077, #b98d44);
        color: #17171a;
        font-weight: 700;
        padding: 0.85rem 1rem;
        cursor: pointer;
      }

      .resultado-frete {
        margin-top: 1rem;
        min-height: 24px;
        font-size: 0.95rem;
      }

      .resultado-frete.ok {
        color: #9de3a6;
      }

      .resultado-frete.erro {
        color: #ffb7b7;
      }

      .texto-modal-cep {
        margin-top: 1rem;
        color: #d9d0bf;
        line-height: 1.5;
        font-size: 0.96rem;
      }

      .modal-frete-acao-row {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.25rem;
      }

      .btn-frete-secundario {
        flex: 1;
        border: 1px solid rgba(212, 176, 119, 0.4);
        background: transparent;
        color: #f5efe6;
        border-radius: 10px;
        padding: 0.8rem 0.9rem;
        cursor: pointer;
      }
    </style>
  </body>
</html>