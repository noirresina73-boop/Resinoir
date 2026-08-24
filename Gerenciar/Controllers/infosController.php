<?php
namespace Controllers;
use PDO;

class infosController
{
    protected function BDlog(){
        try {
            $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');
        } catch (\Exception $mnsg) {
            echo "<li>Erro ao conectar ao banco: " . $mnsg->getMessage() . "</li>";
        }
        return $BD;
    }

    public function buscarPorId(int $id)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT * FROM produtos WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

public function listarTodos($pagina = 1, $nome = '', $categoria = 0, $colecao = 0)
{
    $BD = $this->BDlog();
    $limite = 20;
    $inicio = ($pagina - 1) * $limite;

    $condicoes = [];
    $parametros = [];

    if ($nome !== '') {
        $condicoes[] = 'nome LIKE :nome';
        $parametros[':nome'] = '%' . $nome . '%';
    }

    if ($categoria > 0) {
        $condicoes[] = 'categoria = :categoria';
        $parametros[':categoria'] = $categoria;
    }

    if ($colecao > 0) {
        $condicoes[] = 'colecao = :colecao';
        $parametros[':colecao'] = $colecao;
    }

    $where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

    $sql = "SELECT id, nome, valor, capa, estoque FROM produtos $where ORDER BY id DESC LIMIT :inicio, :limite";
    $query = $BD->prepare($sql);

    foreach ($parametros as $chave => $valor) {
        $query->bindValue($chave, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $query->bindValue(':limite', $limite, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

    public function criar(
        int $id, string $idPDR, string $nome, string $modelo, string $descricao, string $cor,
        int $tamanho, int $estoque, int $categoria, int $colecao, string $jsonImagens,
        int $encomenda, float $valor, int $totalVendidos, int $novidade, string $capa
    ){
        $BD = $this->BDlog();

        $query = $BD->prepare('
            INSERT INTO produtos (
                idPDR, nome, modelo, descricao, cor, tamanho, estoque,
                categoria, colecao, imagem, encomenda, valor, totalVendidos, novidade, capa
            ) VALUES (
                :idPDR, :nome, :modelo, :descricao, :cor, :tamanho, :estoque,
                :categoria, :colecao, :imagem, :encomenda, :valor, :totalVendidos, :novidade, :capa
            )
        ');

        $query->bindValue(':idPDR', $idPDR, PDO::PARAM_STR);
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':modelo', $modelo, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->bindValue(':cor', $cor, PDO::PARAM_STR);
        $query->bindValue(':tamanho', $tamanho, PDO::PARAM_INT);
        $query->bindValue(':estoque', $estoque, PDO::PARAM_INT);
        $query->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $query->bindValue(':colecao', $colecao, PDO::PARAM_INT);
        $query->bindValue(':imagem', $jsonImagens, PDO::PARAM_STR);
        $query->bindValue(':encomenda', $encomenda, PDO::PARAM_INT);
        $query->bindValue(':valor', $valor, PDO::PARAM_STR);
        $query->bindValue(':totalVendidos', $totalVendidos, PDO::PARAM_INT);
        $query->bindValue(':novidade', $novidade, PDO::PARAM_INT);
        $query->bindValue(':capa', $capa, PDO::PARAM_STR);

        if (!$query->execute()) {
            echo "<pre>"; print_r($query->errorInfo()); echo "</pre>";
            return 0;
        }

        return (int) $BD->lastInsertId();
    }

    public function atualizar(
        int $id, string $nome, string $modelo, string $descricao, string $cor,
        int $tamanho, int $estoque, int $categoria, int $colecao, ?string $jsonImagens,
        int $encomenda, float $valor, int $novidade, ?string $capa
    ){
        $BD = $this->BDlog();

        $sql = 'UPDATE produtos SET nome = :nome, modelo = :modelo, descricao = :descricao,
                cor = :cor, tamanho = :tamanho, estoque = :estoque, categoria = :categoria,
                colecao = :colecao, encomenda = :encomenda, valor = :valor, novidade = :novidade';

        if ($jsonImagens !== null) $sql .= ', imagem = :imagem';
        if ($capa !== null) $sql .= ', capa = :capa';
        $sql .= ' WHERE id = :id';

        $query = $BD->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':modelo', $modelo, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->bindValue(':cor', $cor, PDO::PARAM_STR);
        $query->bindValue(':tamanho', $tamanho, PDO::PARAM_INT);
        $query->bindValue(':estoque', $estoque, PDO::PARAM_INT);
        $query->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $query->bindValue(':colecao', $colecao, PDO::PARAM_INT);
        $query->bindValue(':encomenda', $encomenda, PDO::PARAM_INT);
        $query->bindValue(':valor', $valor, PDO::PARAM_STR);
        $query->bindValue(':novidade', $novidade, PDO::PARAM_INT);
        if ($jsonImagens !== null) $query->bindValue(':imagem', $jsonImagens, PDO::PARAM_STR);
        if ($capa !== null) $query->bindValue(':capa', $capa, PDO::PARAM_STR);

        if (!$query->execute()) {
            echo "<pre>"; print_r($query->errorInfo()); echo "</pre>";
        }
    }
}