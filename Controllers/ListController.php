<?php
    namespace Controllers;

    

use PDO;

    class ListController
    {
        public const ICONE_BIBLIOTECA = [
            'colar' => [
                'nome' => 'Colar',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8.5" r="3.8"/><path d="M9.2 11.2 6.5 21h11l-2.7-9.8"/></svg>',
            ],
            'brinco' => [
                'nome' => 'Brinco',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5c-2.2 2.8-3.7 5.5-3.7 9a3.7 3.7 0 1 0 7.4 0c0-3.5-1.5-6.2-3.7-9z"/><circle cx="12" cy="20" r="1.3"/></svg>',
            ],
            'broche' => [
                'nome' => 'Broche',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5 3 21l9-3.8 9 3.8z"/><path d="M7.5 17 12 8l4.5 9"/></svg>',
            ],
            'anel' => [
                'nome' => 'Anel',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="15" r="5.8"/><path d="M9.2 9.2 12 2.5l2.8 6.7"/></svg>',
            ],
            'pulseira' => [
                'nome' => 'Pulseira',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><circle cx="6" cy="12" r=".9" fill="currentColor"/><circle cx="12" cy="4" r=".9" fill="currentColor"/><circle cx="18" cy="12" r=".9" fill="currentColor"/><circle cx="12" cy="20" r=".9" fill="currentColor"/></svg>',
            ],
            'pingente' => [
                'nome' => 'Pingente',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4c3 3.5 5 5 8 5s5-1.5 8-5"/><path d="M12 9v3"/><path d="M9.5 12l-1.5 5 4 2.5 4-2.5-1.5-5z"/><path d="M12 14.5v3.5"/></svg>',
            ],
            'cruz' => [
                'nome' => 'Cruz Gótica',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5v6"/><path d="M12 8.5v13"/><path d="M5 9.5h14"/><path d="M12 2.5l-1.2 1.3m1.2-1.3L13.2 3.8"/><path d="M5 9.5l1.3 1.2M5 9.5l1.3-1.2"/><path d="M19 9.5l-1.3 1.2M19 9.5l-1.3-1.2"/><path d="M12 21.5l-1.2-1.3m1.2 1.3 1.2-1.3"/></svg>',
            ],
            'estrela' => [
                'nome' => 'Estrela',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.7 5.7 6.3.7-4.8 4.3 1.3 6.3L12 17l-5.5 2.5 1.3-6.3-4.8-4.3 6.3-.7z"/></svg>',
            ],
            'lua' => [
                'nome' => 'Lua Crescente',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14.5a7.5 7.5 0 1 1-9.5-9.5 6 6 0 0 0 9.5 9.5z"/><circle cx="18" cy="6" r=".8" fill="currentColor"/><circle cx="20.5" cy="10" r=".6" fill="currentColor"/></svg>',
            ],
            'coroa' => [
                'nome' => 'Coroa / Tiara',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19h16"/><path d="M5 19 3 9l5 3 4-6 4 6 5-3-2 10"/><circle cx="3" cy="9" r="1" fill="currentColor"/><circle cx="12" cy="6" r="1" fill="currentColor"/><circle cx="21" cy="9" r="1" fill="currentColor"/></svg>',
            ],
            'rosa' => [
                'nome' => 'Rosa / Flor',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 5a7 7 0 0 0 0 14 7 7 0 0 0 0-14z"/><path d="M5 12a7 7 0 0 0 14 0 7 7 0 0 0-14 0z"/><path d="M12 20v2M4.5 16.5l-1.5 1.5M19.5 16.5l1.5 1.5"/></svg>',
            ],
            'coracao' => [
                'nome' => 'Coração Gótico',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20.5s-7.5-4.6-7.5-10a4.5 4.5 0 0 1 7.5-3.2 4.5 4.5 0 0 1 7.5 3.2c0 5.4-7.5 10-7.5 10z"/><path d="M10.5 8.5 12 7l1.5 1.5"/></svg>',
            ],
            'chave' => [
                'nome' => 'Chave Antiga',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6.5" cy="8" r="3.5"/><circle cx="6.5" cy="8" r="1.2" fill="currentColor"/><path d="M10 8h11"/><path d="M17 8v3.5M21 8v2"/></svg>',
            ],
            'ampulheta' => [
                'nome' => 'Ampulheta',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12M6 22h12"/><path d="M6 2v2c0 3 2.4 5 6 5s6-2 6-5V2"/><path d="M6 22v-2c0-3 2.4-5 6-5s6 2 6 5v2"/><path d="M8 7h8M8 17h8"/></svg>',
            ],
            'caveira' => [
                'nome' => 'Caveira Gótica',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5a7.5 7.5 0 0 0-7.5 7.5c0 3.3 1.7 5.8 4 7v2h7v-2c2.3-1.2 4-3.7 4-7A7.5 7.5 0 0 0 12 2.5z"/><circle cx="9.5" cy="10.5" r="1.3" fill="currentColor"/><circle cx="14.5" cy="10.5" r="1.3" fill="currentColor"/><path d="M10 14h4M12 12v2M9 19v1M12 19v1M15 19v1"/></svg>',
            ],
            'rosario' => [
                'nome' => 'Rosário / Contas',
                'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v4"/><path d="M12 14v7"/><path d="M7 10.5a4.5 4.5 0 0 1 9 0c0 2-1.2 3.2-3 4-1.8-.8-3-2-3-4z"/><circle cx="12" cy="21" r="1.2" fill="currentColor"/><circle cx="9.5" cy="6" r=".8" fill="currentColor"/><circle cx="14.5" cy="6" r=".8" fill="currentColor"/><path d="M9 10h6"/></svg>',
            ],
        ];

        public static function pegarSvgPorChave($chave): ?string
        {
            if (!is_string($chave) || !str_starts_with($chave, 'svg:')) {
                return null;
            }
            $key = substr($chave, 4);
            if (!isset(self::ICONE_BIBLIOTECA[$key])) {
                return null;
            }
            return self::ICONE_BIBLIOTECA[$key]['svg'];
        }

        public static function chaveIconeValida($chave): bool
        {
            if (!is_string($chave) || !str_starts_with($chave, 'svg:')) {
                return false;
            }
            $key = substr($chave, 4);
            return isset(self::ICONE_BIBLIOTECA[$key]);
        }

        public static function listarChavesIcones(): array
        {
            return array_keys(self::ICONE_BIBLIOTECA);
        }

        public static function htmlIconePorChave(string $chave, string $modo = 'img-card'): string
        {
            if (!self::chaveIconeValida($chave)) {
                return '';
            }
            $key = substr($chave, 4);

            $classes = [
                'thumb-admin' => 'svg-thumb-admin',
                'img-card' => 'svg-img-card',
                'banner-img' => 'svg-img-card svg-img-banner',
                'row-photo' => 'svg-img-card svg-img-rowphoto',
                'cat-circle' => '',
            ];

            $class = $classes[$modo] ?? 'svg-img-card';
            $svg = self::ICONE_BIBLIOTECA[$key]['svg'];

            if ($modo === 'cat-circle') {
                return $svg;
            }

            return '<div class="' . $class . '">' . $svg . '</div>';
        }

        public static function capaParaHtml($capa, $modo = 'img-card'): string
        {
            $capa = trim((string)$capa);
            if ($capa === '') {
                return '';
            }
            $svg = self::pegarSvgPorChave($capa);
            if ($svg !== null) {
                if ($modo === 'thumb-admin') {
                    return '<div class="svg-thumb-admin">' . $svg . '</div>';
                }
                if ($modo === 'img-card') {
                    return '<div class="svg-img-card">' . $svg . '</div>';
                }
                if ($modo === 'banner-img') {
                    return '<div class="svg-img-card svg-img-banner">' . $svg . '</div>';
                }
                if ($modo === 'row-photo') {
                    return '<div class="svg-img-card svg-img-rowphoto">' . $svg . '</div>';
                }
                if ($modo === 'cat-circle') {
                    return $svg;
                }
                return '<div class="svg-img-card">' . $svg . '</div>';
            }
            $imagemPublica = self::resolverCapaPublicaStatic($capa);
            if ($modo === 'thumb-admin') {
                return '<img class="card-thumb" src="' . htmlspecialchars($imagemPublica) . '" alt="">';
            }
            if ($modo === 'row-photo') {
                return '<img src="' . htmlspecialchars($imagemPublica) . '" alt="">';
            }
            return '<img class="img-card" src="' . htmlspecialchars($imagemPublica) . '" alt="">';
        }

        private static function resolverCapaPublicaStatic(string $capa): string
        {
            if (preg_match('#^(https?:)?//#', $capa) || str_starts_with($capa, '/')) {
                return $capa;
            }
            return './' . ltrim(str_replace(['../', './'], '', $capa), '/');
        }

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

        public function getConfig(string $chave, string $padrao = ''): string
        {
            try {
                $BD = $this->BDlog();
                if (!$BD) return $padrao;
                $query = $BD->prepare('SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1');
                $query->bindValue(':chave', $chave, PDO::PARAM_STR);
                $query->execute();
                $valor = $query->fetchColumn();
                return $valor !== false && $valor !== null ? (string) $valor : $padrao;
            } catch (\Exception $e) {
                return $padrao;
            }
        }

        public static function getConfigStatic(string $chave, string $padrao = ''): string
        {
            try {
                $BD = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
                $BD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $query = $BD->prepare('SELECT valor FROM configuracoes WHERE chave = :chave LIMIT 1');
                $query->bindValue(':chave', $chave, PDO::PARAM_STR);
                $query->execute();
                $valor = $query->fetchColumn();
                return $valor !== false && $valor !== null ? (string) $valor : $padrao;
            } catch (\Exception $e) {
                return $padrao;
            }
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
    if (!$BD) {
        return;
    }

    $novidadesJson = $this->getConfig('home_novidades', '');
    $idsOrdenados = [];

    if ($novidadesJson !== '') {
        $decoded = json_decode($novidadesJson, true);
        if (is_array($decoded)) {
            $idsOrdenados = array_values(array_filter($decoded, 'is_numeric'));
        }
    }

    if (!empty($idsOrdenados)) {
        $placeholders = implode(', ', $idsOrdenados);
        $sql = "SELECT id, nome, valor, capa, estoque, status
                FROM produtos
                WHERE id IN ($placeholders)";

        $query = $BD->prepare($sql);
        $query->execute();
        $produtos = $query->fetchAll(PDO::FETCH_ASSOC);

        $orderMap = array_flip($idsOrdenados);
        usort($produtos, function($a, $b) use ($orderMap) {
            $posA = $orderMap[(int)$a['id']] ?? 999;
            $posB = $orderMap[(int)$b['id']] ?? 999;
            return $posA <=> $posB;
        });
    } else {
        $sql = "SELECT id, nome, valor, capa, estoque, status
                FROM produtos
                WHERE novidade = 1
                ORDER BY id DESC
                LIMIT :limite";

        $query = $BD->prepare($sql);
        $query->bindValue(':limite', $limite, PDO::PARAM_INT);
        $query->execute();
        $produtos = $query->fetchAll(PDO::FETCH_ASSOC);
    }

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
            $capa = self::capaParaHtml($p['capa'] ?? '', 'img-card');
            $badgeHtml = $status === 'sob_encomenda' ? "<div class='tag-esgotado'>Sob encomenda</div>" : ($status === 'esgotado' ? "<div class='tag-esgotado'>Esgotado</div>" : '');

            echo "
            <div class='vitral-card' onclick='location.href=\"Infos.php?id=$id\"' style='cursor:pointer;'>
              <div class='vitral-frame'>
                $capa
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
    $BD = new ListController;
    $BD = $BD->BDlog();
    if (!$BD) {
        return;
    }

    $bannerProdutoId = (int) $this->getConfig('home_banner_produto_id', '0');

    $colecaoId = 0;
    $capaProdutoBanner = '';

    if ($bannerProdutoId > 0) {
        $queryProduto = $BD->prepare('SELECT id, nome, colecao, capa FROM produtos WHERE id = :id LIMIT 1');
        $queryProduto->bindValue(':id', $bannerProdutoId, PDO::PARAM_INT);
        $queryProduto->execute();
        $produto = $queryProduto->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            $capaProdutoBanner = $produto['capa'] ?? '';
            if ((int) ($produto['colecao'] ?? 0) > 0) {
                $colecaoId = (int) $produto['colecao'];
            }
        }
    }

    if ($colecaoId > 0) {
        $sql = "SELECT * FROM colecao WHERE id = :colecaoId LIMIT 1";
        $query = $BD->prepare($sql);
        $query->bindValue(':colecaoId', $colecaoId, PDO::PARAM_INT);
        $query->execute();
        $colecao = $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $campo = 'id';
        $condicao = '>';
        $parametro = 0;
        $order = 'id';

        $sql = "SELECT * FROM colecao WHERE destaque = 1 AND $campo $condicao :parametro ORDER BY $order DESC LIMIT 1";

        $query = $BD->prepare($sql);
        $query->bindValue(':parametro', $parametro, PDO::PARAM_INT);
        $query->execute();
        $colecao = $query->fetchAll(PDO::FETCH_ASSOC);
    }

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
        $sqlCategorias = "SELECT DISTINCT ca.id, ca.nome, ca.capa
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
        $capaColecaoBruta = $capaProdutoBanner !== '' ? $capaProdutoBanner : ($retorno["capa"] ?? '');
        $capaColecaoHtml = self::capaParaHtml($capaColecaoBruta, 'banner-img');

        if ($totalCategorias <= 3) {
            foreach ($categorias as $cat) {
                $catRowHtml .= $this->montaCatBtn($cat['nome'], "categoria.php?categoria={$cat['id']}", false, $cat['capa'] ?? '');
            }
        } else {
            // mostra só as 2 primeiras + "E mais"
            for ($i = 0; $i < 2; $i++) {
                $catRowHtml .= $this->montaCatBtn($categorias[$i]['nome'], "categoria.php?categoria={$categorias[$i]['id']}", false, $categorias[$i]['capa'] ?? '');
            }
            $catRowHtml .= $this->montaCatBtn('E mais', "catalogoColecao.php?colecao=$id", true);
        }

        echo "
<div class='banner-section'>
    <div class='banner-frame'>
    $capaColecaoHtml
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

private function montaCatBtn($nome, $link, $eMais = false, $capa = '')
{
    $icone = $eMais
        ? "<svg viewBox='0 0 24 24' fill='none' stroke='#d4b077' stroke-width='1.2'><circle cx='6' cy='12' r='1.4'/><circle cx='12' cy='12' r='1.4'/><circle cx='18' cy='12' r='1.4'/></svg>"
        : $this->iconeCategoria($nome, $capa);

    return "
      <div class='cat-btn' onclick='location.href=\"$link\"' style='cursor:pointer;'>
        <div class='cat-circle'>
          $icone
        </div>
        <div class='cat-label'>$nome</div>
      </div>
    ";
}

private function iconeCategoria($nome, $capaDoBanco = '')
{
    $svgCapa = self::pegarSvgPorChave((string)$capaDoBanco);
    if ($svgCapa !== null) {
        return $svgCapa;
    }

    $chave = mb_strtolower((string)$nome);

    $icones = [
        'brinco'  => self::ICONE_BIBLIOTECA['brinco']['svg'],
        'colar'   => self::ICONE_BIBLIOTECA['colar']['svg'],
        'broche'  => self::ICONE_BIBLIOTECA['broche']['svg'],
        'anel'    => self::ICONE_BIBLIOTECA['anel']['svg'],
        'pulseira'=> self::ICONE_BIBLIOTECA['pulseira']['svg'],
        'pingente'=> self::ICONE_BIBLIOTECA['pingente']['svg'],
        'cruz'    => self::ICONE_BIBLIOTECA['cruz']['svg'],
        'estrela' => self::ICONE_BIBLIOTECA['estrela']['svg'],
        'lua'     => self::ICONE_BIBLIOTECA['lua']['svg'],
        'coroa'   => self::ICONE_BIBLIOTECA['coroa']['svg'],
        'tiara'   => self::ICONE_BIBLIOTECA['coroa']['svg'],
        'rosa'    => self::ICONE_BIBLIOTECA['rosa']['svg'],
        'flor'    => self::ICONE_BIBLIOTECA['rosa']['svg'],
        'coracao' => self::ICONE_BIBLIOTECA['coracao']['svg'],
        'chave'   => self::ICONE_BIBLIOTECA['chave']['svg'],
        'ampulheta'=> self::ICONE_BIBLIOTECA['ampulheta']['svg'],
        'caveira' => self::ICONE_BIBLIOTECA['caveira']['svg'],
        'rosario' => self::ICONE_BIBLIOTECA['rosario']['svg'],
    ];

    foreach ($icones as $chaveIcone => $svg) {
        if (str_contains($chave, $chaveIcone)) {
            return $svg;
        }
    }

    return self::ICONE_BIBLIOTECA['estrela']['svg'];
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
                $capa = self::capaParaHtml($retorno["capa"] ?? '', 'img-card');
                $badgeTexto = $estoque <= 0 ? 'Esgotado · Fazer pedido' : 'Disponível';
                $badgeClass = $estoque <= 0 ? 'sold-out' : 'available';

                echo "
                        <div onclick='location.href=\"Infos.php?id=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <div class='product-badge $badgeClass'>$badgeTexto</div>
                        $capa
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
                $capaBruta = $retorno["capa"] ?? '';
                $capaHtml = self::capaParaHtml($capaBruta, 'img-card');

                echo "
                        <div onclick='location.href=\"catalogoColecao.php?categoria=$id\"' class='product-card'>
                        <div class='product-photo'>
                        <!-- <div class='product-badge'>Novo</div> -->
                        $capaHtml
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
                $capaBruta = $retorno["capa"] ?? '';
                $capaHtml = self::capaParaHtml($capaBruta, 'row-photo');
            if($inverter%2==0){
                echo "
                    <div onclick='location.href=\"catalogoColecao.php?colecao=$id\"' class='collection-row'>
                        <div class='row-photo'>
                            $capaHtml
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
                            $capaHtml
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
        $condicoes[] = '(p.idPDR LIKE :termo OR p.nome LIKE :termo OR p.descricao LIKE :termo OR p.modelo LIKE :termo OR p.cor LIKE :termo OR c.nome LIKE :termo OR co.nome LIKE :termo)';
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

    $sql = "SELECT DISTINCT p.id, p.idPDR, p.nome, p.descricao, p.valor, p.capa, p.totalVendidos
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
            'idPDR'     => $p['idPDR'],
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