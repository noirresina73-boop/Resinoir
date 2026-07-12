<?php
    namespace Controllers;

    $id =  $_POST["id"];
    $idPDR = $id . $_POST["idPDR"];
    $nome = $_POST["nome"];
    $modelo = $_POST["modelo"];
    $descricao = $_POST["descricao"];
    $cor = $_POST["cor"];
    $tamanho = $_POST["tamanho"];
    $estoque = $_POST["estoque"];
    // $imagemString = $_POST['imagens'];
    // $imagem = json_decode($imagemString, true);
    // $imagem = json_encode($imagem);
    //$encomenda = $_POST["encomenda"];
    $encomenda = 0;
    $valor = $_POST["valor"];
    //$totalVendidos = $_POST["totalVendidos"];
    $totalVendidos = 1;
    //$novidade = $_POST["novidade"];
    $novidade = 1;
$nomePasta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nome);

$pastaPrincipal = "../../assets/imgs/$nomePasta";
$pastaCapa = "$pastaPrincipal/capa";
$pastaImagens = "$pastaPrincipal/imagens";

// Cria as pastas
foreach ([$pastaPrincipal, $pastaCapa, $pastaImagens] as $pasta) {
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
}

/* ==========================
   CAPA
========================== */

$capa = "";

if (
    isset($_FILES["capa"]) &&
    $_FILES["capa"]["error"] == UPLOAD_ERR_OK
) {

    $nomeCapa = basename($_FILES["capa"]["name"]);

    move_uploaded_file(
        $_FILES["capa"]["tmp_name"],
        "$pastaCapa/$nomeCapa"
    );

    // Valor para salvar no banco
    $capa = "./assets/imgs/$nomePasta/capa/$nomeCapa";
}

/* ==========================
   IMAGENS
========================== */

$imagens = [];

if (isset($_FILES["imagens"])) {

    foreach ($_FILES["imagens"]["tmp_name"] as $i => $tmp) {

        if ($_FILES["imagens"]["error"][$i] == UPLOAD_ERR_OK) {

            $nomeImagem = basename($_FILES["imagens"]["name"][$i]);

            move_uploaded_file(
                $tmp,
                "$pastaImagens/$nomeImagem"
            );

            // Caminho para salvar no banco
            $imagens[] = "./assets/imgs/$nomePasta/imagens/$nomeImagem";
        }
    }
}

$jsonImagens = json_encode($imagens);

echo "Capa: $capa <br><br>";
echo "Imagens: $jsonImagens";
    //$capa = $_POST["capa"];
    $Criar = new infosController;
    $Criar = $Criar->criar(
    $id,
    $idPDR,
    $nome,
    $modelo,
    $descricao,
    $cor,
    $tamanho,
    $estoque,
    $jsonImagens,
    $encomenda,
    $valor,
    $totalVendidos,
    $novidade,
    $capa
);

use PDO;

    class infosController
    {
        protected function BDlog(){
            try {
                    $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');

                } catch (\Exception $mnsg) {
                    echo "<li>";
                    echo "Erro ao conectar oa banco: ". $mnsg->getMessage();
                    echo "</li>";
                }

                return $BD;
        }

        public function pageInfo(int $id){
            $BD = new infosController;
            $BD = $BD->BDlog();

            $query = $BD->prepare('SELECT * FROM produtos where id = :id;');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $retorno = $query->fetch();
            if($retorno){

                $id = $retorno["id"];
                $idPDR = $retorno["idPDR"];
                $nome = $retorno["nome"];
                $modelo = $retorno["modelo"];
                $descricao = $retorno["descricao"];
                $cor = $retorno["cor"];
                $tamanho = $retorno["tamanho"];
                $estoque = $retorno["estoque"];
                $imagem = json_decode($retorno["imagem"]);
                $encomenda = $retorno["encomenda"];
                $valor = $retorno["valor"];
                $totalVendidos = $retorno["totalVendidos"];
                $novidade = $retorno["novidade"];
                $capa = $retorno["capa"];

                echo"
                <!-- toda a pagina -->
                ";
            }
        }

        public function criar(
            int $id,
            string $idPDR,
            string $nome,
            string $modelo,
            string $descricao,
            string $cor,
            int $tamanho,
            int $estoque,
            string $jsonImagens,
            int $encomenda,
            float $valor,
            int $totalVendidos,
            int $novidade,
            string $capa
        ){
            $BD = new infosController;
            $BD = $BD->BDlog();

            try {
                $query = $BD->prepare('
                INSERT INTO produtos (
                    idPDR,
                    nome,
                    modelo,
                    descricao,
                    cor,
                    tamanho,
                    estoque,
                    imagem,
                    encomenda,
                    valor,
                    totalVendidos,
                    novidade,
                    capa
                ) VALUES (
                    :idPDR,
                    :nome,
                    :modelo,
                    :descricao,
                    :cor,
                    :tamanho,
                    :estoque,
                    :imagem,
                    :encomenda,
                    :valor,
                    :totalVendidos,
                    :novidade,
                    :capa
                )
            ');
            } catch (\Exception $e) {
                echo $e;
            }

            $query->bindValue(':idPDR', $idPDR, PDO::PARAM_STR);
            $query->bindValue(':nome', $nome, PDO::PARAM_STR);
            $query->bindValue(':modelo', $modelo, PDO::PARAM_STR);
            $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
            $query->bindValue(':cor', $cor, PDO::PARAM_STR);
            $query->bindValue(':tamanho', $tamanho, PDO::PARAM_INT);
            $query->bindValue(':estoque', $estoque, PDO::PARAM_INT);
            $query->bindValue(':imagem', $jsonImagens, PDO::PARAM_STR);
            $query->bindValue(':encomenda', $encomenda, PDO::PARAM_INT);
            $query->bindValue(':valor', $valor, PDO::PARAM_STR);
            $query->bindValue(':totalVendidos', $totalVendidos, PDO::PARAM_INT);
            $query->bindValue(':novidade', $novidade, PDO::PARAM_INT);
            $query->bindValue(':capa', $capa, PDO::PARAM_STR);

            if (!$query->execute()) {
                echo "<pre>";
                print_r($query->errorInfo());
                echo "</pre>";
            }
        }
    }
?>