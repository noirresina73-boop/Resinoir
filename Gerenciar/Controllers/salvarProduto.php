<?php
use Controllers\infosController;
use Controllers\CatalogoAuxController;

include __DIR__ . '/../autoloader.php';

$acao = $_POST['acao'] ?? 'criar';

$id = $_POST["id"];
$idPDR = $id . $_POST["idPDR"];
$nome = $_POST["nome"];
$modelo = $_POST["modelo"];
$descricao = $_POST["descricao"];
$cor = $_POST["cor"];
$tamanho = $_POST["tamanho"];
$estoque = $_POST["estoque"];
$categoria = (int) ($_POST["categoria"] ?? 0);
$colecao = (int) ($_POST["colecao"] ?? 0);
$encomenda = 0;
$valor = $_POST["valor"];
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
        $categoria, $colecao, $jsonImagens, $encomenda, (float) $valor, $novidade, $capa
    );
    header('Location: ../infos.php?id=' . $produtoId . '&salvo=1');
    exit;
}

$Criar = new infosController;
$novoId = $Criar->criar(
    (int) $id, $idPDR, $nome, $modelo, $descricao, $cor, (int) $tamanho, (int) $estoque,
    $categoria, $colecao, $jsonImagens ?? json_encode([]), $encomenda, (float) $valor,
    $totalVendidos, $novidade, $capa ?? ''
);
header('Location: ../infos.php?id=' . $novoId . '&salvo=1');
exit;