<?php
use Controllers\infosController;
use Controllers\CatalogoAuxController;
use Controllers\ListController;

include __DIR__ . '/../autoloader.php';

function gerarCodigoProduto(string $nome, int $id): string
{
    $nome = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $nome);
    $palavras = preg_split('/\s+/', trim($nome));
    $iniciais = '';

    foreach ($palavras as $palavra) {
        $palavra = trim($palavra);
        if ($palavra !== '') {
            $iniciais .= strtoupper(substr($palavra, 0, 1));
        }
    }

    if ($iniciais === '') {
        $iniciais = 'PR';
    }

    return $iniciais . (string) $id;
}

$acao = $_POST['acao'] ?? 'criar';

$Controller = new infosController;
$id = isset($_POST['id']) ? (int) $_POST['id'] : $Controller->obterProximoId();
$nome = trim((string) ($_POST['nome'] ?? ''));
$idPDR = $acao === 'editar' ? trim((string) ($_POST['idPDR'] ?? '')) : gerarCodigoProduto($nome, $id);
$modelo = $_POST["modelo"];
$descricao = $_POST["descricao"];
$cor = $_POST["cor"];
$tamanho = $_POST["tamanho"];
$estoque = $_POST["estoque"];
$categoria = !empty($_POST["categoria"]) ? (int) $_POST["categoria"] : null;
$colecao = !empty($_POST["colecao"]) ? (int) $_POST["colecao"] : null;
$status = in_array($_POST['status'] ?? 'disponivel', ['disponivel', 'esgotado', 'sob_encomenda'], true) ? $_POST['status'] : 'disponivel';
$encomenda = $status === 'sob_encomenda' ? 1 : 0;
$valor = $_POST["valor"];
$custo = (float) ($_POST['custo'] ?? 0);
$totalVendidos = $acao === 'editar' ? (int) ($_POST['totalVendidos'] ?? 0) : 0;
$novidade = isset($_POST['novidade']) ? 1 : 0;
$precoPersonalizado = isset($_POST['preco_personalizado']) ? 1 : 0;
$precoMinimo = (float) ($_POST['preco_minimo'] ?? 0);
$precoMaximo = (float) ($_POST['preco_maximo'] ?? 0);

$capa = null;

$capaInput = trim((string) ($_POST['capa'] ?? ''));
$capaIconeInput = trim((string) ($_POST['capa_icone'] ?? ''));

if ($capaIconeInput !== '' && ListController::chaveIconeValida($capaIconeInput)) {
    $capa = $capaIconeInput;
} elseif ($capaInput !== '' && ListController::chaveIconeValida($capaInput)) {
    $capa = $capaInput;
} elseif (isset($_FILES["capa"]) && $_FILES["capa"]["error"] == UPLOAD_ERR_OK) {
    $nomePasta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nome);
    $pastaCapa = __DIR__ . "/../../assets/imgs/$nomePasta/capa";

    if (!is_dir($pastaCapa)) {
        mkdir($pastaCapa, 0777, true);
    }

    $nomeCapa = basename($_FILES["capa"]["name"]);
    move_uploaded_file($_FILES["capa"]["tmp_name"], "$pastaCapa/$nomeCapa");
    $capa = "./assets/imgs/$nomePasta/capa/$nomeCapa";
}

$jsonImagens = null;
if (isset($_FILES["imagens"]) && !empty(array_filter($_FILES["imagens"]["tmp_name"]))) {
    $nomePasta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nome);
    $pastaImagens = __DIR__ . "/../../assets/imgs/$nomePasta/imagens";

    if (!is_dir($pastaImagens)) {
        mkdir($pastaImagens, 0777, true);
    }

    $imagens = [];
    foreach ($_FILES["imagens"]["tmp_name"] as $i => $tmp) {
        if ($_FILES["imagens"]["error"][$i] == UPLOAD_ERR_OK) {
            $nomeImagem = basename($_FILES["imagens"]["name"][$i]);
            move_uploaded_file($tmp, "$pastaImagens/$nomeImagem");
            $imagens[] = "./assets/imgs/$nomePasta/imagens/$nomeImagem";
        }
    }
    $jsonImagens = json_encode($imagens);
}

if ($acao === 'editar') {
    $produtoId = (int) $_POST['produtoId'];
    $Controller = new infosController;
    $Controller->atualizar(
        $produtoId, $nome, $modelo, $descricao, $cor, (int) $tamanho, (int) $estoque,
        $categoria, $colecao, $jsonImagens, $encomenda, (float) $valor, (float) $custo, $novidade, $capa, $status,
        $precoPersonalizado, $precoMinimo, $precoMaximo
    );

    $variacoes = [];
    $variacaoNomes = $_POST['variacao_nome'] ?? [];
    $variacaoIcones = $_POST['variacao_icone'] ?? [];
    $variacaoEstoques = $_POST['variacao_estoque'] ?? [];
    $variacaoPrecos = $_POST['variacao_preco_adicional'] ?? [];
    for ($i = 0; $i < count($variacaoNomes); $i++) {
        if (trim((string) $variacaoNomes[$i]) !== '') {
            $variacoes[] = [
                'nome' => trim((string) $variacaoNomes[$i]),
                'capa' => trim((string) ($variacaoIcones[$i] ?? '')),
                'estoque' => (int) ($variacaoEstoques[$i] ?? 0),
                'preco_adicional' => (float) ($variacaoPrecos[$i] ?? 0),
            ];
        }
    }
    $Controller->salvarVariacoes($produtoId, $variacoes);

    $redirect = '../produtos-lista.php?' . http_build_query([
        'salvo' => 1,
        'nome' => $nome,
        'idProduto' => trim((string) ($_POST['idPDR'] ?? $idPDR)),
        'valor' => (string) $valor,
        'estoque' => (string) $estoque,
    ]);

    header('Location: ' . $redirect);
    exit;
}

$Criar = new infosController;
$novoId = $Criar->criar(
    (int) $id, $idPDR, $nome, $modelo, $descricao, $cor, (int) $tamanho, (int) $estoque,
    $categoria, $colecao, $jsonImagens ?? json_encode([]), $encomenda, (float) $valor,
    (float) $custo, $totalVendidos, $novidade, $capa ?? '', $status,
    $precoPersonalizado, $precoMinimo, $precoMaximo
);

if ($novoId > 0) {
    $variacoes = [];
    $variacaoNomes = $_POST['variacao_nome'] ?? [];
    $variacaoIcones = $_POST['variacao_icone'] ?? [];
    $variacaoEstoques = $_POST['variacao_estoque'] ?? [];
    $variacaoPrecos = $_POST['variacao_preco_adicional'] ?? [];
    for ($i = 0; $i < count($variacaoNomes); $i++) {
        if (trim((string) $variacaoNomes[$i]) !== '') {
            $variacoes[] = [
                'nome' => trim((string) $variacaoNomes[$i]),
                'capa' => trim((string) ($variacaoIcones[$i] ?? '')),
                'estoque' => (int) ($variacaoEstoques[$i] ?? 0),
                'preco_adicional' => (float) ($variacaoPrecos[$i] ?? 0),
            ];
        }
    }
    if (!empty($variacoes)) {
        $Criar->salvarVariacoes($novoId, $variacoes);
    }
}

$redirect = '../produtos-lista.php?' . http_build_query([
    'salvo' => 1,
    'nome' => $nome,
    'idProduto' => $idPDR,
    'valor' => (string) $valor,
    'estoque' => (string) $estoque,
]);

header('Location: ' . $redirect);
exit;