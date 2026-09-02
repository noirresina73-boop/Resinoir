<?php
namespace Controllers;
use PDO;

class CatalogoAuxController
{
    protected function BDlog(){
        try {
            $BD = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4','if0_42359254','1ZHLF0ZU3S1Rw');
        } catch (\Exception $mnsg) {
            echo "<li>Erro ao conectar ao banco: " . $mnsg->getMessage() . "</li>";
        }
        return $BD;
    }

    public function listarCategorias()
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT id, nome, descricao, capa FROM categoria ORDER BY nome ASC');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarColecoes()
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT id, nome, descricao, capa FROM colecao ORDER BY nome ASC');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarCategoriaPorId(int $id)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT * FROM categoria WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarColecaoPorId(int $id)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT * FROM colecao WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function criarCategoria($nome, $descricao, $capa = '')
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('INSERT INTO categoria (nome, descricao, data_criacao, capa) VALUES (:nome, :descricao, NOW(), :capa)');
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->bindValue(':capa', $capa, PDO::PARAM_STR);
        $query->execute();
        return (int) $BD->lastInsertId();
    }

    public function criarColecao($nome, $descricao, $capa = '', $destaque = 0)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('INSERT INTO colecao (nome, descricao, data_criacao, capa, destaque) VALUES (:nome, :descricao, NOW(), :capa, :destaque)');
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->bindValue(':capa', $capa, PDO::PARAM_STR);
        $query->bindValue(':destaque', $destaque, PDO::PARAM_INT);
        $query->execute();
        return (int) $BD->lastInsertId();
    }

    public function atualizarCategoria(int $id, $nome, $descricao, $capa = null)
    {
        $BD = $this->BDlog();
        $sql = 'UPDATE categoria SET nome = :nome, descricao = :descricao';
        if ($capa !== null) $sql .= ', capa = :capa';
        $sql .= ' WHERE id = :id';

        $query = $BD->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        if ($capa !== null) $query->bindValue(':capa', $capa, PDO::PARAM_STR);
        $query->execute();
    }

    public function atualizarColecao(int $id, $nome, $descricao, $capa = null, $destaque = null)
    {
        $BD = $this->BDlog();
        $sql = 'UPDATE colecao SET nome = :nome, descricao = :descricao';
        if ($capa !== null) $sql .= ', capa = :capa';
        if ($destaque !== null) $sql .= ', destaque = :destaque';
        $sql .= ' WHERE id = :id';

        $query = $BD->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        if ($capa !== null) $query->bindValue(':capa', $capa, PDO::PARAM_STR);
        if ($destaque !== null) $query->bindValue(':destaque', $destaque, PDO::PARAM_INT);
        $query->execute();
    }

    public function excluirCategoria(int $id)
    {
        $BD = $this->BDlog();

        // impede excluir categoria que ainda tem produtos vinculados
        $check = $BD->prepare('SELECT COUNT(*) AS total FROM produtos WHERE categoria = :id');
        $check->bindValue(':id', $id, PDO::PARAM_INT);
        $check->execute();
        if ($check->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
            return 'em_uso';
        }

        $query = $BD->prepare('DELETE FROM categoria WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return 'ok';
    }

    public function excluirColecao(int $id)
    {
        $BD = $this->BDlog();

        $check = $BD->prepare('SELECT COUNT(*) AS total FROM produtos WHERE colecao = :id');
        $check->bindValue(':id', $id, PDO::PARAM_INT);
        $check->execute();
        if ($check->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
            return 'em_uso';
        }

        $query = $BD->prepare('DELETE FROM colecao WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return 'ok';
    }

    public function setColecaoDestaque(int $id)
    {
        $BD = $this->BDlog();
        // Remove destaque de todas as coleções
        $query = $BD->prepare('UPDATE colecao SET destaque = 0');
        $query->execute();
        // Define a coleção como destaque
        $query = $BD->prepare('UPDATE colecao SET destaque = 1 WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function salvarImagemCapa($nomeReferencia, $arquivo)
    {
        $nomePasta = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nomeReferencia);
        $pastaCapa = __DIR__ . "/../../assets/imgs/$nomePasta/capa";

        if (!is_dir($pastaCapa)) {
            mkdir($pastaCapa, 0777, true);
        }

        $nomeArquivo = basename($arquivo['name']);
        move_uploaded_file($arquivo['tmp_name'], "$pastaCapa/$nomeArquivo");

        return "./assets/imgs/$nomePasta/capa/$nomeArquivo";
    }
}