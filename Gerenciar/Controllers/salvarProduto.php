<?php
use Controllers\infosController;
use Controllers\CatalogoAuxController;

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
$encomenda = 0;
$valor = $_POST["valor"];
$custo = (float) ($_POST['custo'] ?? 0);
$totalVendidos = 1;
$novidade = 1;

$nomePasta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nome);
$pastaPrincipal = __DIR__ . "/../../assets/imgs/$nomePasta";
$pastaCapa = "$pastaPrincipal/capa";
$pastaImagens = "$pastaPrincipal/imagens";

foreach ([$pastaPrincipal, $pastaCapa, $pastaImagens] as $pasta) {
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
}

$capa = null;
if (isset($_FILES["capa"]) && $_FILES["capa"]["error"] == UPLOAD_ERR_OK) {
    $nomeCapa = basename($_FILES["capa"]["name"]);
    move_uploaded_file($_FILES["capa"]["tmp_name"], "$pastaCapa/$nomeCapa");
    $capa = "./assets/imgs/$nomePasta/capa/$nomeCapa";
}

$jsonImagens = null;
if (isset($_FILES["imagens"]) && !empty(array_filter($_FILES["imagens"]["tmp_name"]))) {
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
        $categoria, $colecao, $jsonImagens, $encomenda, (float) $valor, (float) $custo, $novidade, $capa
    );

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
    (float) $custo, $totalVendidos, $novidade, $capa ?? ''
);

$redirect = '../produtos-lista.php?' . http_build_query([
    'salvo' => 1,
    'nome' => $nome,
    'idProduto' => $idPDR,
    'valor' => (string) $valor,
    'estoque' => (string) $estoque,
]);

header('Location: ' . $redirect);
exit;