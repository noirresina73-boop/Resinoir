<?php
    namespace Controllers;

    $id = 3;
    $idPDR = $id . $_POST["idPDR"];
    $nome = $_POST["nome"];
    $modelo = $_POST["modelo"];
    $descricao = $_POST["descricao"];
    $cor = $_POST["cor"];
    $tamanho = $_POST["tamanho"];
    $estoque = $_POST["estoque"];
    $imagemString = $_POST['imagens'];
    $imagem = json_decode($imagemString, true);
    $imagem = json_encode($imagem);
    //$encomenda = $_POST["encomenda"];
    $encomenda = 0;
    $valor = $_POST["valor"];
    //$totalVendidos = $_POST["totalVendidos"];
    $totalVendidos = 1;
    //$novidade = $_POST["novidade"];
    $novidade = 1;
    $capa = $_POST["capa"];
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
    $imagem,
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
                $imagem[] = $retorno["imagem"];
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
            string $imagem,
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
                    id,
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
                    :id,
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

            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->bindValue(':idPDR', $idPDR, PDO::PARAM_STR);
            $query->bindValue(':nome', $nome, PDO::PARAM_STR);
            $query->bindValue(':modelo', $modelo, PDO::PARAM_STR);
            $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
            $query->bindValue(':cor', $cor, PDO::PARAM_STR);
            $query->bindValue(':tamanho', $tamanho, PDO::PARAM_INT);
            $query->bindValue(':estoque', $estoque, PDO::PARAM_INT);
            $query->bindValue(':imagem', $imagem, PDO::PARAM_STR);
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