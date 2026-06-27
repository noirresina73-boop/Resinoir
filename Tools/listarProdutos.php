<?php
    namespace Tools;

use PDO;

    class listarProdutos{

        public function BDlog(){
            try {
                    $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');
                
                } catch (\Exception $mnsg) {
                    echo "<li>";
                    echo "Erro ao conectar oa banco: ". $mnsg->getMessage();
                    echo "</li>";
                }

                return $BD;
        }

        public function listarNovidads(){
            $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');
            $query = $BD->query('SELECT COUNT(*) FROM produtos;');
            $TotalRegis = $query->fetch();
            $i=1;
            $id=1;
            while($i <= $TotalRegis['COUNT(*)']){

            $query = $BD->prepare('SELECT * FROM produtos where id = :id;');
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $retorno = $query->fetch();
            if($retorno){


                $nome = $retorno["Nome"];
                $descricao = $retorno["Descricao"];
                $tamanho = $retorno["Tamanho"];
                $cor = $retorno["cor"];
                $quantidade = $retorno["Quantidade"];
                $tipo = $retorno["Tipo"];
                $valor = $retorno["Valor"];
                $imagem = $retorno["imagem"];


            echo"


            <div class='card' style='width: 18rem;' data-bs-theme='dark'>
            <img src='$imagem' class='card-img-top' alt='...'>
            <div class='card-body'>
                <h5 class='card-title'>$nome</h5>
                <h6 class='card-preco'>$valor</h6>
                <p class='card-text'>$descricao</p>
                <a href='#' class=' button'>Go somewhere</a>
            </div>
            </div>

            ";
            $i++;
            }
            $id++;

            }

            }
        }
?>