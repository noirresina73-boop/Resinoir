<?php
    namespace Controllers;

use PDO;

    class ListController
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

        public function listNovidades(){
            $BD = new ListController;
            $BD = $BD->BDlog();
            $query = $BD->prepare('SELECT COUNT(*) FROM produtos where novidade = :novidade;');
            $query->bindValue(':novidade', 1, PDO::PARAM_INT);
            $query->execute();
            $TotalRegistros = $query->fetch();
            $i=1;
            $idP=1;
            while($i <= $TotalRegistros['COUNT(*)']){

            $query = $BD->prepare('SELECT * FROM produtos where id = :id;');
            $query->bindValue(':id', $idP, PDO::PARAM_INT);
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
                <div class='card' style='width: 18rem;' data-bs-theme='dark'>
                <img src='$capa' class='card-img-top' alt='...'>
                <div class='card-body'>
                    <h5 class='card-title'>$nome</h5>
                    <h6 class='card-preco'>$valor</h6>
                    <a href='#' class=' button'>Ver mais</a>
                </div>
                </div>
                ";

                $i++;
                }
            $idP++;
            }
        }

        public function listProdutos(){
            $BD = new ListController;
            $BD = $BD->BDlog();
            $query = $BD->query('SELECT COUNT(*) FROM produtos;');
            $TotalRegistros = $query->fetch();
            $i=1;
            $idP=1;
            while($i <= $TotalRegistros['COUNT(*)']){

            $query = $BD->prepare('SELECT * FROM produtos where id = :id;');
            $query->bindValue(':id', $idP, PDO::PARAM_INT);
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
                <div class='card' style='width: 18rem;' data-bs-theme='dark'>
                <img src='$capa' class='card-img-top' alt='...'>
                <div class='card-body'>
                    <h5 class='card-title'>$nome</h5>
                    <h6 class='card-preco'>$valor</h6>
                    <a href='#' class=' button'>Ver mais</a>
                </div>
                </div>
                ";

                $i++;
                }
            $idP++;
            }
        }
    }
?>