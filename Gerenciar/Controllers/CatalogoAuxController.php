<?php
namespace Controllers;
use PDO;

class CatalogoAuxController
{
    protected function BDlog(){
        try {
            $BD = new PDO('mysql:host=localhost;dbname=resinior','root','senha');
        } catch (\Exception $mnsg) {
            echo "<li>Erro ao conectar ao banco: " . $mnsg->getMessage() . "</li>";
        }
        return $BD;
    }

    public function listarCategorias()
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT id, nome FROM categoria ORDER BY nome ASC');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarColecoes()
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT id, nome FROM colecao ORDER BY nome ASC');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarCategoria($nome, $descricao)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('INSERT INTO categoria (nome, descricao, data_criacao, capa) VALUES (:nome, :descricao, NOW(), "")');
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->execute();
        return (int) $BD->lastInsertId();
    }

    public function criarColecao($nome, $descricao)
    {
        $BD = $this->BDlog();
        $query = $BD->prepare('INSERT INTO colecao (nome, descricao, data_criacao, capa) VALUES (:nome, :descricao, NOW(), "")');
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $query->execute();
        return (int) $BD->lastInsertId();
    }
}