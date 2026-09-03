<?php
    namespace Controllers;

    

use PDO;

    class ListController
    {
            private function imagemPublica($imagem): string
            {
                $imagem = trim((string) $imagem);
                if ($imagem === '') return './assets/imgs/placeholder.jpg';
                if (preg_match('#^(https?:)?//#', $imagem) || str_starts_with($imagem, '/')) return $imagem;
                return './' . ltrim(str_replace(['../', './'], '', $imagem), '/');
            }

        protected function BDlog(){
            try {
                    $BD = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4','if0_42359254','1ZHLF0ZU3S1Rw');

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

public function listNovidadesVitral($limite = 3)
{
    $BD = new ListController;
    $BD = $BD->BDlog();

    $sql = "SELECT id, nome, valor, capa, estoque
            FROM produtos
            WHERE novidade = 1
            ORDER BY id DESC
            LIMIT :limite";

    $query = $BD->prepare($sql);
    $query->bindValue(':limite', $limite, PDO::PARAM_INT);
    $query->execute();

    $produtos = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($produtos)) {
        echo "<p style='padding: 0 24px; color: var(--bone-dim); font-size: 12px;'>Nenhuma novidade no momento.</p>";
        return;
    }

    foreach ($produtos as $p) {
        $id = (int) $p['id'];
        $nome = htmlspecialchars($p['nome']);
        $valor = number_format((float) $p['valor'], 2, ',', '.');
        $estoque = (int) ($p['estoque'] ?? 0);
        $status = (string) ($p['status'] ?? ($estoque <= 0 ? 'esgotado' : 'disponivel'));
        $capa = htmlspecialchars($this->imagemPublica($p['capa'] ?? ''));
        $badgeHtml = $status === 'sob_encomenda' ? "<div class='tag-esgotado'>Sob encomenda</div>" : ($status === 'esgotado' ? "<div class='tag-esgotado'>Esgotado</div>" : '');

        echo "
        <div class='vitral-card' onclick='location.href=\"Infos.php?id=$id\"' style='cursor:pointer;'>
          <div class='vitral-frame'>
            <img class='img-card-vitral' src='$capa' alt='$nome'>
            $badgeHtml
          </div>
          <div class='vitral-caption'>
            <div class='name'>$nome</div>
            <div class='price'>R\$ $valor</div>
          </div>
        </div>
        ";
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

    $sql = "SELECT * FROM colecao WHERE destaque = 1 AND $campo $condicao :parametro ORDER BY $order DESC LIMIT :inicio, :limite";

    $query = $BD->prepare($sql);

    $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $query->bindValue(':limite', $limite, PDO::PARAM_INT);
    $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
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
        $capa = htmlspecialchars($this->imagemPublica($retorno["capa"] ?? ''));

        // busca as categorias que essa coleção realmente tem
        $sqlCategorias = "SELECT DISTINCT ca.id, ca.nome
                           FROM produtos p
                           INNER JOIN categoria ca ON p.categoria = ca.id
                           WHERE p.colecao = :colecaoId
                           ORDER BY ca.nome ASC";

        $queryCat = $BD->prepare($sqlCategorias);
        $queryCat->bindValue(':colecaoId', $id, PDO::PARAM_INT);
        $queryCat->execute();
        $categorias = $queryCat->fetchAll(PDO::FETCH_ASSOC);

        $totalCategorias = count($categorias);
        $catRowHtml = '';

        if ($totalCategorias <= 3) {
            foreach ($categorias as $cat) {
                $catRowHtml .= $this->montaCatBtn($cat['nome'], "categoria.php?categoria={$cat['id']}");
            }
        } else {
            // mostra só as 2 primeiras + "E mais"
            for ($i = 0; $i < 2; $i++) {
                $catRowHtml .= $this->montaCatBtn($categorias[$i]['nome'], "categoria.php?categoria={$categorias[$i]['id']}");
            }
            $catRowHtml .= $this->montaCatBtn('E mais', "catalogoColecao.php?colecao=$id", true);
        }

        echo "
<div class='banner-section'>
    <div class='banner-frame'>
    <img class='img-card' src='$capa' alt=''>
      <div class='banner-text' style='cursor: pointer;' onclick='location.href=\"catalogoColecao.php?colecao=$id\"'>
        <div class='banner-eyebrow'>Coleção em destaque</div>
        <div class='banner-title'>$nome</div>
        <div class='banner-sub'>$descricao</div>
        <div class='banner-cta'>Ver coleção <span>&rarr;</span></div>
      </div>
    </div>

    <div class='cat-row'>
      $catRowHtml
    </div>
  </div>
                ";
    }
}

private function montaCatBtn($nome, $link, $eMais = false)
{
    $icone = $eMais
        ? "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='6' cy='12' r='1.4'/><circle cx='12' cy='12' r='1.4'/><circle cx='18' cy='12' r='1.4'/></svg>"
        : $this->iconeCategoria($nome);

    return "
      <div class='cat-btn' onclick='location.href=\"$link\"' style='cursor:pointer;'>
        <div class='cat-circle'>
          $icone
        </div>
        <div class='cat-label'>$nome</div>
      </div>
    ";
}

private function iconeCategoria($nome)
{
    $chave = mb_strtolower($nome);

    $icones = [
        'brinco'  => "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><path d='M12 2C9 6 7 9 7 12a5 5 0 0 0 10 0c0-3-2-6-5-10z'/><circle cx='12' cy='20' r='1.4'/></svg>",
        'colar'   => "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='12' cy='8' r='4'/><path d='M9 11 6 21h12l-3-10'/></svg>",
        'broche'  => "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><path d='M12 2 3 21l9-4 9 4z'/></svg>",
        'anel'    => "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='12' cy='15' r='6'/><path d='M9 9l3-7 3 7'/></svg>",
        'pulseira'=> "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='12' cy='12' r='8'/></svg>",
    ];

    foreach ($icones as $chaveIcone => $svg) {
        if (str_contains($chave, $chaveIcone)) {
            return $svg;
        }
    }

    // ícone padrão pra categoria sem ícone específico mapeado
    return "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><path d='M12 2l3 6 6 1-4.5 4.5L18 20l-6-3-6 3 1.5-6.5L3 9l6-1z'/></svg>";
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
                $estoque = (int) ($retorno["estoque"] ?? 0);
                $imagem = $retorno["imagem"];
                $encomenda = $retorno["encomenda"];
                $valor = $retorno["valor"];
                $totalVendidos = $retorno["totalVendidos"];
                $novidade = $retorno["novidade"];
                $capa = htmlspecialchars($this->imagemPublica($retorno["capa"] ?? ''));
                $badgeTexto = $estoque <= 0 ? 'Esgotado · Fazer pedido' : 'Disponível';
                $badgeClass = $estoque <= 0 ? 'sold-out' : 'available';

                echo "
                        <div onclick='location.href=\"Infos.php?id=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <div class='product-badge $badgeClass'>$badgeTexto</div>
                        <img class='img-card' src='$capa' alt=''>
                        </div>
                        <div class='product-info'>
                        <div class='name'>$nome</div>
                        <div class='price'>R$ " . number_format((float) $valor, 2, ',', '.') . "</div>
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
            $inverter=0;
            foreach ($colecao as $retorno) {

                $id = $retorno["id"];
                $nome = $retorno["nome"];
                $descricao = $retorno["descricao"];
                $data_criacao = $retorno["data_criacao"];
                $capa = $retorno["capa"];
            if($inverter%2==0){
                echo "
                    <div onclick='location.href=\"catalogoColecao.php?colecao=$id\"' class='collection-row'>
                        <div class='row-photo'>
                            <img src='$capa' alt='$nome'>
                        </div>
                        <div class='row-info'>
                            <div class='name'>$nome</div>
                            <div class='desc'>$descricao</div>
                        </div>
                        <svg class='row-arrow' viewBox='0 0 24 24' fill='none' stroke='#a89f8b' stroke-width='1.4'>
                            <path d='M9 6l6 6-6 6'/>
                        </svg>
                    </div>
                ";
            }else{
                echo "
                    <div onclick='location.href=\"catalogoColecao.php?colecao=$id\"' class='collection-row'>
                        <div class='row-info'>
                            <div class='name'>$nome</div>
                            <div class='desc'>$descricao</div>
                        </div>
                        <div class='row-photo'>
                            <img src='$capa' alt='$nome'>
                        </div>
                        <svg class='row-arrow' viewBox='0 0 24 24' fill='none' stroke='#a89f8b' stroke-width='1.4'>
                            <path d='M9 6l6 6-6 6'/>
                        </svg>
                    </div>
                ";
            }
            $inverter++;
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

public function apiPesquisa()
{
    header('Content-Type: application/json; charset=utf-8');

    $termo = isset($_GET['q']) ? trim($_GET['q']) : '';
    $categoriaFiltro = (int) ($_GET['categoria'] ?? 0);
    $colecaoFiltro = (int) ($_GET['colecao'] ?? 0);

    $BD = new ListController;
    $BD = $BD->BDlog();

    $semTermo = ($termo === '' || mb_strlen($termo) < 2);

    $resultados = $this->buscarProdutos($BD, $termo, $semTermo, $categoriaFiltro, $colecaoFiltro);

    $termoSugerido = null;
    if (empty($resultados) && !$semTermo) {
        $termoSugerido = $this->encontrarTermoSimilar($BD, $termo);
        if ($termoSugerido) {
            $resultados = $this->buscarProdutos($BD, $termoSugerido, false, $categoriaFiltro, $colecaoFiltro);
        }
    }

    echo json_encode([
        'termoBuscado'  => $termo,
        'termoSugerido' => $termoSugerido,
        'resultados'    => $resultados,
    ]);
}

private function buscarProdutos($BD, $termo, $semTermo, $categoriaFiltro = 0, $colecaoFiltro = 0)
{
    $condicoes = [];
    $parametros = [];

    if (!$semTermo) {
        $condicoes[] = '(p.nome LIKE :termo OR p.descricao LIKE :termo OR p.modelo LIKE :termo OR p.cor LIKE :termo OR c.nome LIKE :termo OR co.nome LIKE :termo)';
        $parametros[':termo'] = '%' . $termo . '%';
    }

    if ($categoriaFiltro > 0) {
        $condicoes[] = 'p.categoria = :categoriaFiltro';
        $parametros[':categoriaFiltro'] = $categoriaFiltro;
    }

    if ($colecaoFiltro > 0) {
        $condicoes[] = 'p.colecao = :colecaoFiltro';
        $parametros[':colecaoFiltro'] = $colecaoFiltro;
    }

    $where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';
    $limite = $semTermo ? 20 : 40;

    $sql = "SELECT DISTINCT p.id, p.nome, p.descricao, p.valor, p.capa, p.totalVendidos
            FROM produtos p
            LEFT JOIN categoria c ON p.categoria = c.id
            LEFT JOIN colecao co ON p.colecao = co.id
            $where
            ORDER BY p.totalVendidos DESC
            LIMIT $limite";

    $query = $BD->prepare($sql);
    foreach ($parametros as $chave => $valor) {
        $query->bindValue($chave, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $query->execute();

    $resultado = [];
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $p) {
        $resultado[] = [
            'id'        => (int) $p['id'],
            'nome'      => $p['nome'],
            'descricao' => $p['descricao'],
            'valor'     => number_format((float) $p['valor'], 2, ',', '.'),
            'capa'      => $p['capa'],
        ];
    }

    return $resultado;
}

private function encontrarTermoSimilar($BD, $termo)
{
    $termo = mb_strtolower($termo);
    $tamanhoTermo = mb_strlen($termo);
    $candidatos = [];

    foreach (['produtos', 'categoria', 'colecao'] as $tabela) {
        $query = $BD->prepare("SELECT DISTINCT nome FROM $tabela");
        $query->execute();
        foreach ($query->fetchAll(PDO::FETCH_COLUMN) as $nome) {
            foreach (explode(' ', $nome) as $palavra) {
                $candidatos[] = mb_strtolower(trim($palavra));
            }
        }
    }

    $melhor = null;
    $menorDistancia = null;

    foreach (array_unique($candidatos) as $palavra) {
        if ($palavra === '' || mb_strlen($palavra) < 3) continue;

        $distanciaCompleta = levenshtein($termo, $palavra);

        $prefixo = mb_substr($palavra, 0, $tamanhoTermo);
        $distanciaPrefixo = levenshtein($termo, $prefixo);

        $distancia = min($distanciaCompleta, $distanciaPrefixo);
        $tolerancia = max(1, (int) floor($tamanhoTermo / 3));

        if ($distancia <= $tolerancia && ($menorDistancia === null || $distancia < $menorDistancia)) {
            $menorDistancia = $distancia;
            $melhor = $palavra;
        }
    }

    return $melhor;
}

    }
?>