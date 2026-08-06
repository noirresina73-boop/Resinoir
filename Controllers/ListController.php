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

        public function Banner($tela = null)
        {

            $campo = 'id';
            $condicao = '>';
            $parametro = 0;

            $BD = new ListController;
            $BD = $BD->BDlog();

            $sql = "SELECT COUNT(*) AS total FROM produtos WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM produtos WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);

            $query->bindValue(':parametro', $parametro, PDO::PARAM_STR);
            $query->execute();

            $produto = $query->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($produto)) {

                $id = $produto["id"];
                $idPDR = $produto["idPDR"];
                $nome = $produto["nome"];
                $modelo = $produto["modelo"];
                $descricao = $produto["descricao"];
                $cor = $produto["cor"];
                $tamanho = $produto["tamanho"];
                $estoque = $produto["estoque"];
                $imagem = $produto["imagem"];
                $encomenda = $produto["encomenda"];
                $valor = $produto["valor"];
                $totalVendidos = $produto["totalVendidos"];
                $novidade = $produto["novidade"];
                $capa = $produto["capa"];

            }
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
                    <h6 class='card-preco'>R$ <?= number_format((float) $valor, 2, ',', '.') ?></h6>
                    <a href='infos.php?id=$id' class='btn btn-primary'>Ver mais</a>
                </div>
                </div>
                ";

                $i++;
                }
            $idP++;
            }
        }

                public function mostraColecaoNova()
        {

                $campo = 'id';
                $condicao = '>';
                $parametro = 0;

            $pagina = 1;
            $limite = 1;
            $inicio = ($pagina - 1) * $limite;
            $order = 'id';

            $BD = new ListController;
            $BD = $BD->BDlog();

            $sql = "SELECT COUNT(*) AS total FROM colecao WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            $totalRegistros = $resultado['total'];
            $maxPaginas = ceil($totalRegistros / $limite);

            $sql = "SELECT * FROM colecao WHERE $campo $condicao :parametro order by $order desc LIMIT :inicio, :limite";

            $query = $BD->prepare($sql);

            $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_STR);
            $query->execute();

            $colecao = $query->fetchAll(PDO::FETCH_ASSOC);

            $limite = count($colecao);

            if ($limite === 0) {
                echo "<p>Nenhum produto encontrado.</p>";
            }

            foreach ($colecao as $retorno) {

                $id = $retorno["id"];
                $nome = $retorno["nome"];
                $descricao = $retorno["descricao"];
                $data_criacao = $retorno["data_criacao"];
                $capa = $retorno["capa"];
                echo "
<div class='banner-section'>
    <div class='banner-frame'>
      <img class='img-card' src='$capa' alt=''>
      <div class='banner-text' style='cursor: pointer;' onclick='location.href=\"catalogoColecao.php?colecao=$id\"'>
        <div class='banner-eyebrow'>Coleção em destaque</div>
        <div class='banner-title'>Vitral Sombrio</div>
        <div class='banner-sub'>peças em resina inspiradas em rosáceas góticas</div>
        <div class='banner-cta'>Ver coleção <span>&rarr;</span></div>
      </div>
    </div>

    <div class='cat-row'>
      <div class='cat-btn'>
        <div class='cat-circle'>
          <svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><path d='M12 2C9 6 7 9 7 12a5 5 0 0 0 10 0c0-3-2-6-5-10z'/><circle cx='12' cy='20' r='1.4'/></svg>
        </div>
        <div class='cat-label'>Brincos</div>
      </div>
      <div class='cat-btn'>
        <div class='cat-circle'>
          <svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='12' cy='8' r='4'/><path d='M9 11 6 21h12l-3-10'/></svg>
        </div>
        <div class='cat-label'>Colares</div>
      </div>
      <div class='cat-btn'>
        <div class='cat-circle'>
          <svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><path d='M12 2 3 21l9-4 9 4z'/></svg>
        </div>
        <div class='cat-label'>Broches</div>
      </div>
    </div>
  </div>

                ";
            }
        }

        public function listProdutos($tela = null)
        {
            $pagina = 1;
            if (isset($_GET['pagina'])) {
                $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
            }

            $campo = 'id';
            $condicao = '>';
            $parametro = 0;

            if (isset($_GET['colecao'])) {
                $campo = 'colecao';
                $condicao = '=';
                $parametro = filter_input(INPUT_GET, 'colecao', FILTER_VALIDATE_INT);
            }

            if (isset($_GET['categoria'])) {
                $campo = 'categoria';
                $condicao = '=';
                $parametro = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);
            }

            if(!$pagina){
                $pagina = 1;
            }
            $limite = 12;
            $inicio = ($pagina - 1) * $limite;
            $order = 'id';

            $BD = new ListController;
            $BD = $BD->BDlog();

            $sql = "SELECT COUNT(*) AS total FROM produtos WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            $totalRegistros = $resultado['total'];
            $maxPaginas = ceil($totalRegistros / $limite);

            $sql = "SELECT * FROM produtos WHERE $campo $condicao :parametro order by $order desc LIMIT :inicio, :limite";

            $query = $BD->prepare($sql);

            $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_STR);
            $query->execute();

            $produtos = $query->fetchAll(PDO::FETCH_ASSOC);

            $limite = count($produtos);

            if ($limite === 0) {
                echo "<p>Nenhum produto encontrado.</p>";
            }

            foreach ($produtos as $retorno) {

                $id = $retorno["id"];
                $idPDR = $retorno["idPDR"];
                $nome = $retorno["nome"];
                $modelo = $retorno["modelo"];
                $descricao = $retorno["descricao"];
                $cor = $retorno["cor"];
                $tamanho = $retorno["tamanho"];
                $estoque = $retorno["estoque"];
                $imagem = $retorno["imagem"];
                $encomenda = $retorno["encomenda"];
                $valor = $retorno["valor"];
                $totalVendidos = $retorno["totalVendidos"];
                $novidade = $retorno["novidade"];
                $capa = $retorno["capa"];

                echo "
                        <div onclick='location.href=\"infos.php?id=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <!-- <div class='product-badge'>Novo</div> -->
                        <img class='img-card' src='$capa' alt=''>
                        </div>
                        <div class='product-info'>
                        <div class='name'>$nome</div>
                        <div class='price'>R$ $valor</div>
                        </div>
                    </div>
                ";
            }

            if($tela === 'home') {

            }elseif($tela === 'catalogo') {

            echo "
            </div>
            <div class='group'>
                <div class='btn-group' role='group' aria-label='Basic example'>";
                if ($pagina > 1) {
                    echo "<a href='?pagina=" . ($pagina - 1) . "' class='btn-link'>❮ Anterior</a>";
                }
                    echo "<button class='cta-btn cont-btn'>$pagina</button>";
                if ($pagina < $maxPaginas) {
                    echo "<a href='?pagina=" . ($pagina + 1) . "' class='btn-link'>Seguinte ❯</a>";
                }
                echo "</div>";
            }
        }

        public function listCategoria($tela = null)
        {
            $pagina = 1;
            if (isset($_GET['pagina'])) {
                $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
            }

            if(!$pagina){
                $pagina = 1;
            }
            $limite = 12;
            $campo = 'id';
            $condicao = '>';
            $parametro = 0;
            $inicio = ($pagina - 1) * $limite;
            $order = 'id';

            $BD = new ListController;
            $BD = $BD->BDlog();

            $sql = "SELECT COUNT(*) AS total FROM categoria WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            $totalRegistros = $resultado['total'];
            $maxPaginas = ceil($totalRegistros / $limite);

            $sql = "SELECT * FROM categoria WHERE $campo $condicao :parametro order by $order desc LIMIT :inicio, :limite";

            $query = $BD->prepare($sql);

            $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_STR);
            $query->execute();

            $categoria = $query->fetchAll(PDO::FETCH_ASSOC);

            $limite = count($categoria);

            if ($limite === 0) {
                echo "<p>Nenhum produto encontrado.</p>";
            }

            foreach ($categoria as $retorno) {

                $id = $retorno["id"];
                $nome = $retorno["nome"];
                $descricao = $retorno["descricao"];
                $data_criacao = $retorno["data_criacao"];
                $capa = $retorno["capa"];

                echo "
                        <div onclick='location.href=\"catalogoColecao.php?categoria=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <!-- <div class='product-badge'>Novo</div> -->
                        <img class='img-card' src='$capa' alt=''>
                        </div>
                        <div class='product-info'>
                        <div class='name'>$nome</div>
                        <div class='name'>$descricao</div>
                        </div>
                    </div>
                ";
            }

            if($tela === 'home') {

            }elseif($tela === 'catalogo') {

            echo "
            </div>
            <div class='group'>
                <div class='btn-group' role='group' aria-label='Basic example'>";
                if ($pagina > 1) {
                    echo "<a href='?pagina=" . ($pagina - 1) . "' class='btn-link'>❮ Anterior</a>";
                }
                    echo "<button class='cta-btn cont-btn'>$pagina</button>";
                if ($pagina < $maxPaginas) {
                    echo "<a href='?pagina=" . ($pagina + 1) . "' class='btn-link'>Seguinte ❯</a>";
                }
                echo "</div>";
            }
        }

        public function listColecao($tela = null)
        {
            $pagina = 1;
            if (isset($_GET['pagina'])) {
                $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
            }

            if(!$pagina){
                $pagina = 1;
            }
            $limite = 12;
            $campo = 'id';
            $condicao = '>';
            $parametro = 0;
            $inicio = ($pagina - 1) * $limite;
            $order = 'id';

            $BD = new ListController;
            $BD = $BD->BDlog();

            $sql = "SELECT COUNT(*) AS total FROM colecao WHERE $campo $condicao :parametro";

            $query = $BD->prepare($sql);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
            $query->execute();

            $resultado = $query->fetch(PDO::FETCH_ASSOC);

            $totalRegistros = $resultado['total'];
            $maxPaginas = ceil($totalRegistros / $limite);

            $sql = "SELECT * FROM colecao WHERE $campo $condicao :parametro order by $order desc LIMIT :inicio, :limite";

            $query = $BD->prepare($sql);

            $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
            $query->bindValue(':limite', $limite, PDO::PARAM_INT);
            $query->bindValue(':parametro', $parametro, PDO::PARAM_STR);
            $query->execute();

            $colecao = $query->fetchAll(PDO::FETCH_ASSOC);

            $limite = count($colecao);

            if ($limite === 0) {
                echo "<p>Nenhum produto encontrado.</p>";
            }

            foreach ($colecao as $retorno) {

                $id = $retorno["id"];
                $nome = $retorno["nome"];
                $descricao = $retorno["descricao"];
                $data_criacao = $retorno["data_criacao"];
                $capa = $retorno["capa"];

                echo "
                        <div onclick='location.href=\"catalogoColecao.php?colecao=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <!-- <div class='product-badge'>Novo</div> -->
                        <img class='img-card' src='$capa' alt=''>
                        </div>
                        <div class='product-info'>
                        <div class='name'>$nome</div>
                        <div class='name'>$descricao</div>
                        </div>
                    </div>
                ";
            }

            if($tela === 'home') {

            }elseif($tela === 'catalogo') {

            echo "
            </div>
            <div class='group'>
                <div class='btn-group' role='group' aria-label='Basic example'>";
                if ($pagina > 1) {
                    echo "<a href='?pagina=" . ($pagina - 1) . "' class='btn-link'>❮ Anterior</a>";
                }
                    echo "<button class='cta-btn cont-btn'>$pagina</button>";
                if ($pagina < $maxPaginas) {
                    echo "<a href='?pagina=" . ($pagina + 1) . "' class='btn-link'>Seguinte ❯</a>";
                }
                echo "</div>";
            }
        }
    }
?>