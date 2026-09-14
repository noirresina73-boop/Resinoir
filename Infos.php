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

function capaParaInfo(string $capa): string {
    if ($capa === '') return './assets/imgs/placeholder.jpg';
    if (str_starts_with($capa, 'svg:')) {
        return ''; 
    }
    if (preg_match('#^(https?:)?//#', $capa) || str_starts_with($capa, '/')) {
        return $capa;
    }
    return './' . ltrim(str_replace(['../', './'], '', $capa), '/');
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
                <?php include 'topo_usuario.php'; ?>
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
                    <?php if (str_starts_with($todasImagens[0], 'svg:')): ?>
                        <div id="imagemGrande" class="imgGrande" onclick="abrirImagem()" style="cursor:pointer;" data-icone="<?= htmlspecialchars($todasImagens[0]) ?>">
                            <?= ListController::htmlIconePorChave($todasImagens[0], 'img-card') ?>
                        </div>
                    <?php else: ?>
                        <img
                            id="imagemGrande"
                            src="<?= capaParaInfo($todasImagens[0]) ?>"
                            class="imgGrande"
                            onclick="abrirImagem()">
                    <?php endif; ?>
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
                                <?php if (str_starts_with($imagem, 'svg:')): ?>
                                    <div class="btnLogo <?= $i === 0 ? 'selecionada' : '' ?>" onclick="trocarImagem('<?= $imagem ?>', this)" style="cursor:pointer;" data-icone="<?= htmlspecialchars($imagem) ?>">
                                        <?= ListController::htmlIconePorChave($imagem, 'img-card') ?>
                                    </div>
                                <?php else: ?>
                                    <img
                                        src="<?= $imagem ?>"
                                        class="btnLogo <?= $i === 0 ? 'selecionada' : '' ?>"
                                        onclick="trocarImagem('<?= $imagem ?>', this)">
                                <?php endif; ?>

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

                <div class="frete" id="freteContainer">
                    <div id="freteInfo" style="font-size:12px;color:var(--bone-dim);margin-top:4px;"></div>
                    <button type="button" id="btnTrocarEndereco" style="display:none;margin-top:8px;padding:6px 12px;border:1px solid rgba(176,141,87,0.5);border-radius:20px;background:transparent;color:#d4b077;font-family:'Jost',sans-serif;font-size:10px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;">Trocar endereço</button>
                    <button type="button" id="btnCalcularFrete" class="btn-carrinho">Calcular Frete</button>
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
        <input id="cepFrete" name="cep" type="text" inputmode="numeric" maxlength="9" placeholder="Ex.: 00000-000" required>
        <button type="button" id="btnCalcularSubmit" class="btn-frete-submit">Calcular</button>
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

    <div id="modalConfirmarSalvarCep" class="modal-frete" aria-hidden="true">
        <div class="modal-frete-backdrop" data-fechar-confirmar-cep="true"></div>
        <div class="modal-frete-content" role="dialog" aria-modal="true" aria-labelledby="modalConfirmarSalvarCepTitulo">
            <button type="button" class="modal-fechar" aria-label="Fechar" data-fechar-confirmar-cep="true">×</button>
            <div class="eyebrow">Salvar CEP</div>
            <h3 id="modalConfirmarSalvarCepTitulo">Deseja salvar este CEP no seu perfil?</h3>
            <p class="texto-modal-cep">Assim ele será usado automaticamente para calcular o frete.</p>
            <div class="modal-frete-acao-row">
                <button type="button" id="btnNaoSalvarCep" class="btn-frete-secundario">Não</button>
                <button type="button" id="btnSimSalvarCep" class="btn-frete-submit">Sim</button>
            </div>
        </div>
    </div>

    <script>

    function htmlIconePorChave(chave, modo = 'img-card') {
        const key = chave.replace('svg:', '');
        const svg = ICONES_BIBLIOTECA[key];
        if (!svg) return '';
        if (modo === 'cat-circle') {
            return svg.replace('<svg ', '<svg style="width:24px;height:24px;stroke:#e9e0c9;stroke-width:1.5;fill:none;" ');
        }
        return '<div class="svg-img-card">' + svg.replace('<svg ', '<svg style="width:100%;height:100%;stroke:#e9e0c9;stroke-width:1;fill:none;" ') + '</div>';
    }

    const whatsappNumero = String.fromCharCode(
        53, 53, 52, 54, 56, 56, 48, 52, 50, 52, 49, 53
    );

const produtoNome = <?= json_encode((string) ($anuncio['nome'] ?? 'Produto')) ?>;
const produtoId = <?= json_encode((string) ($anuncio['id'] ?? '')) ?>;
const produtoCapa = <?= json_encode((string) ($capa ?? '')) ?>;
const valorProduto = Number(<?= json_encode((float) ($anuncio['valor'] ?? 0)) ?>) || 0;
const produtoEstoque = Number(<?= json_encode((int) ($anuncio['estoque'] ?? 0)) ?>) || 0;
let usuarioCep = <?= json_encode((string) ($_SESSION['usuario_cep'] ?? '')) ?>;
const usuarioLogado = <?= json_encode((bool) isset($_SESSION['usuario_id'])) ?>;
let cepParaSalvar = '';

    function aplicarMascaraCep(input) {
        if (!input) return;
        input.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 8) v = v.slice(0, 8);
            if (v.length > 5) {
                v = v.slice(0, 5) + '-' + v.slice(5, 8);
            }
            if (e.target.value !== v) e.target.value = v;
        });
    }

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
    const imgGrande = document.getElementById("imagemGrande");
    if (!imgGrande) return;
    
    if (src && src.startsWith('svg:')) {
        const svgHtml = htmlIconePorChave(src, 'img-card');
        if (imgGrande.tagName === 'IMG') {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = svgHtml;
            const svgContainer = wrapper.firstElementChild;
            svgContainer.id = 'imagemGrande';
            svgContainer.className = 'imgGrande';
            svgContainer.onclick = abrirImagem;
            svgContainer.style.cursor = 'pointer';
            svgContainer.setAttribute('data-icone', src);
            imgGrande.parentNode.replaceChild(svgContainer, imgGrande);
        } else {
            imgGrande.setAttribute('data-icone', src);
            imgGrande.innerHTML = svgHtml;
        }
    } else {
        if (imgGrande.tagName !== 'IMG') {
            const novaImg = document.createElement('img');
            novaImg.id = 'imagemGrande';
            novaImg.src = src;
            novaImg.className = 'imgGrande';
            novaImg.onclick = abrirImagem;
            imgGrande.parentNode.replaceChild(novaImg, imgGrande);
        } else {
            imgGrande.src = src;
        }
    }
    document.querySelectorAll(".btnLogo").forEach(function(el){
      el.classList.remove("selecionada");
    });
    elemento.classList.add("selecionada");
}

function abrirImagem(){
    const imgGrande = document.getElementById("imagemGrande");
    if (!imgGrande) return;
    const overlay = document.getElementById("overlayImagem");
    overlay.innerHTML = '';

    if (imgGrande.tagName === 'IMG') {
        const expandida = document.createElement('img');
        expandida.id = 'imagemExpandida';
        expandida.src = imgGrande.src;
        expandida.onclick = function(event) { event.stopPropagation(); };
        overlay.appendChild(expandida);
    } else {
        const svgHtml = htmlIconePorChave(imgGrande.getAttribute('data-icone') || '', 'img-card');
        const wrapper = document.createElement('div');
        wrapper.style.width = 'min(92vw, 420px)';
        wrapper.style.display = 'flex';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        wrapper.onclick = function(event) { event.stopPropagation(); };
        wrapper.innerHTML = svgHtml;
        overlay.appendChild(wrapper);
    }
    overlay.classList.add("ativo");
}

function fecharImagem(){
    document.getElementById("overlayImagem").classList.remove("ativo");
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

    const input = document.getElementById('cepFrete');
    if (input && usuarioCep) {
        input.value = usuarioCep;
    }

    const resultado = document.getElementById('resultadoFrete');
    if (resultado && usuarioCep) {
        resultado.textContent = `CEP salvo: ${usuarioCep}`;
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

function fecharModalConfirmarCep() {
    const modal = document.getElementById('modalConfirmarSalvarCep');
    if (!modal) return;
    modal.classList.remove('ativo');
    modal.setAttribute('aria-hidden', 'true');
}

function abrirModalConfirmarCep() {
    const modal = document.getElementById('modalConfirmarSalvarCep');
    if (!modal) return;
    modal.classList.add('ativo');
    modal.setAttribute('aria-hidden', 'false');
}

function salvarCepConfirmado() {
    if (!cepParaSalvar) return;
    salvarCepNoPerfil(cepParaSalvar).then(function(resp) {
        fecharModalConfirmarCep();
    }).catch(function() {
        fecharModalConfirmarCep();
    });
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
    if (!usuarioCep) {
        abrirModalCepOpcional();
        return;
    }

    calcularFrete(usuarioCep).then(function(calculo) {
        if (calculo.valid) {
            const enderecoTexto = `%0A%0AEndereço salvo:%20${encodeURIComponent((calculo.rua || 'Rua não informada') + ', ' + (calculo.bairro || 'bairro não informado'))}%0ACEP:%20${encodeURIComponent(calculo.cep)}`;
            const texto = produtoEstoque <= 0
                ? `Olá! Gostaria de fazer o pedido do produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20confirmar%20a%20disponibilidade%20e%20o%20valor%20final.${enderecoTexto}`
                : `Olá! Gostaria de comprar o produto ${produtoNome} (ID: ${produtoId}).%0A%0AQuero%20mais%20informações%20sobre%20a%20entrega%20e%20pagamento.${enderecoTexto}`;
            const url = `https://wa.me/${whatsappNumero}?text=${texto}`;
            window.open(url, '_blank');
        } else {
            abrirModalCepOpcional();
        }
    });
}

function atualizarFreteTexto(mensagem, ok = true) {
    const freteInfo = document.getElementById('freteInfo');
    const btnTrocar = document.getElementById('btnTrocarEndereco');
    const btnCalcular = document.getElementById('btnCalcularFrete');
    if (!freteInfo) return;
    freteInfo.textContent = mensagem;
    freteInfo.style.color = ok ? '#9de3a6' : '#ffb7b7';
    if (btnCalcular) btnCalcular.style.display = 'none';
    if (btnTrocar) btnTrocar.style.display = 'inline-block';
}

function salvarCepNoPerfil(cep) {
    return fetch('atualizar_cep.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'cep=' + encodeURIComponent(cep)
    }).then(function(r) { return r.json(); });
}

async function calcularFreteAutomatico() {
    if (!usuarioCep) {
        atualizarFreteTexto('Informe seu CEP para calcular o frete.', false);
        return;
    }
    atualizarFreteTexto('Calculando frete...');
    const calculo = await calcularFrete(usuarioCep);
    if (calculo.valid) {
        const valorFormatado = `R$ ${calculo.valor.toFixed(2).replace('.', ',')}`;
        const cepExibicao = usuarioCep.replace(/(\d{5})(\d{3})/, '$1-$2');
        atualizarFreteTexto(`Frete para ${cepExibicao}: ${valorFormatado}. ${calculo.mensagem}`, true);
    } else {
        atualizarFreteTexto(calculo.mensagem, false);
    }
}

atualizarEstadoProduto();
document.getElementById('btnComprarWhatsApp')?.addEventListener('click', prepararLinkCompra);
document.getElementById('btnCalcularFrete')?.addEventListener('click', abrirModalFrete);
document.getElementById('btnTrocarEndereco')?.addEventListener('click', abrirModalFrete);
document.querySelectorAll('[data-fechar-frete]').forEach(function(botao){
    botao.addEventListener('click', fecharModalFrete);
});
document.querySelectorAll('[data-fechar-cep-opcional]').forEach(function(botao){
    botao.addEventListener('click', fecharModalCepOpcional);
});
document.querySelectorAll('[data-fechar-confirmar-cep]').forEach(function(botao){
    botao.addEventListener('click', fecharModalConfirmarCep);
});
document.getElementById('btnSimSalvarCep')?.addEventListener('click', salvarCepConfirmado);
document.getElementById('btnNaoSalvarCep')?.addEventListener('click', fecharModalConfirmarCep);
document.getElementById('btnInformarCep')?.addEventListener('click', function(){
    fecharModalCepOpcional();
    abrirModalFrete();
});
document.getElementById('btnContinuarSemCep')?.addEventListener('click', function(){
    fecharModalCepOpcional();
    continuarCompraSemCep();
});

if (usuarioCep) {
    calcularFreteAutomatico();
}

document.getElementById('btnCalcularSubmit')?.addEventListener('click', async function(){
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

    if (usuarioLogado) {
        cepParaSalvar = calculo.cep;
        abrirModalConfirmarCep();
    }
});

aplicarMascaraCep(document.getElementById('cepFrete'));

    </script>

    <style>
      .modal-frete {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 9999 !important;
        pointer-events: none !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        filter: none !important;
        perspective: none !important;
        will-change: auto !important;
        contain: none !important;
      }

      .modal-frete.ativo {
        display: flex !important;
        pointer-events: auto !important;
      }

      .modal-frete-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        background: rgba(8, 8, 10, 0.72) !important;
      }

      .modal-frete-content {
        position: relative !important;
        width: min(92vw, 420px) !important;
        max-width: 92vw !important;
        background: #17171a !important;
        border: 1px solid rgba(212, 176, 119, 0.32) !important;
        border-radius: 18px !important;
        padding: 1.5rem !important;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.38) !important;
        color: #f5efe6 !important;
        pointer-events: auto !important;
      }

      .modal-fechar {
        position: absolute !important;
        top: 0.8rem !important;
        right: 0.9rem !important;
        background: transparent !important;
        border: 0 !important;
        color: #f5efe6 !important;
        font-size: 1.8rem !important;
        line-height: 1 !important;
        cursor: pointer !important;
      }

      .form-frete {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.8rem !important;
        margin-top: 1rem !important;
      }

      .form-frete input {
        width: 100% !important;
        border-radius: 10px !important;
        border: 1px solid rgba(212, 176, 119, 0.35) !important;
        background: rgba(255, 255, 255, 0.02) !important;
        color: #fff !important;
        padding: 0.8rem 0.9rem !important;
      }

      .btn-frete-submit {
        border: 0 !important;
        border-radius: 10px !important;
        background: linear-gradient(135deg, #d4b077, #b98d44) !important;
        color: #17171a !important;
        font-weight: 700 !important;
        padding: 0.85rem 1rem !important;
        cursor: pointer !important;
      }

      .resultado-frete {
        margin-top: 1rem !important;
        min-height: 24px !important;
        font-size: 0.95rem !important;
      }

      #btnCalcularFrete {
        margin-top: 10px !important;
        width: 100% !important;
        padding: 10px !important;
        border-radius: 10px !important;
        border: 1px solid var(--gold) !important;
        background: linear-gradient(180deg, var(--gold-bright), var(--gold)) !important;
        color: var(--void) !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        letter-spacing: 1.5px !important;
        text-transform: uppercase !important;
        cursor: pointer !important;
      }

      #btnTrocarEndereco {
        margin-top: 8px !important;
        width: 100% !important;
        padding: 10px !important;
        border-radius: 10px !important;
        border: 1px solid rgba(176, 141, 87, 0.5) !important;
        background: transparent !important;
        color: #d4b077 !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        letter-spacing: 1.5px !important;
        text-transform: uppercase !important;
        cursor: pointer !important;
      }

      .resultado-frete.ok {
        color: #9de3a6 !important;
      }

      .resultado-frete.erro {
        color: #ffb7b7 !important;
      }

      .texto-modal-cep {
        margin-top: 1rem !important;
        color: #d9d0bf !important;
        line-height: 1.5 !important;
        font-size: 0.96rem !important;
      }

      .modal-frete-acao-row {
        display: flex !important;
        gap: 0.75rem !important;
        margin-top: 1.25rem !important;
      }

      .btn-frete-secundario {
        flex: 1 !important;
        border: 1px solid rgba(212, 176, 119, 0.4) !important;
        background: transparent !important;
        color: #f5efe6 !important;
        border-radius: 10px !important;
        padding: 0.8rem 0.9rem !important;
        cursor: pointer !important;
      }
    </style>
  </body>
</html>