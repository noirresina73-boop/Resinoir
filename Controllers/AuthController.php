<?php
namespace Controllers;
use PDO;

class AuthController
{
    protected function BDlog(){
        try {
            $BD = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4','if0_42359254','1ZHLF0ZU3S1Rw');
        } catch (\Exception $mnsg) {
            echo "<li>Erro ao conectar ao banco: " . $mnsg->getMessage() . "</li>";
        }
        return $BD;
    }

    public function criarTabelaUsuario(){
        $BD = $this->BDlog();
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(120) NOT NULL,
            telefone VARCHAR(20) DEFAULT NULL,
            email VARCHAR(180) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            foto VARCHAR(255) DEFAULT NULL,
            tipo ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
            cep VARCHAR(20) DEFAULT NULL,
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $BD->exec($sql);
    }

    public function registrar(string $nome, ?string $telefone, string $email, string $senha, ?string $foto = null): int{
        $this->criarTabelaUsuario();
        $BD = $this->BDlog();
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $query = $BD->prepare('INSERT INTO usuarios (nome, telefone, email, senha, foto) VALUES (:nome, :telefone, :email, :senha, :foto)');
        $query->bindValue(':nome', $nome, PDO::PARAM_STR);
        $query->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':senha', $hash, PDO::PARAM_STR);
        $query->bindValue(':foto', $foto, PDO::PARAM_STR);
        $query->execute();
        return (int) $BD->lastInsertId();
    }

    public function login(string $email, string $senha): ?array{
        $this->criarTabelaUsuario();
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return null;
        }
        return $usuario;
    }

    public function buscarPorId(int $id): ?array{
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT id, nome, telefone, email, foto, tipo, cep FROM usuarios WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    public function atualizarFoto(int $id, ?string $foto): void{
        $BD = $this->BDlog();
        $query = $BD->prepare('UPDATE usuarios SET foto = :foto WHERE id = :id');
        $query->bindValue(':foto', $foto, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function alterarSenha(int $id, string $senhaAtual, string $novaSenha): bool{
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT senha FROM usuarios WHERE id = :id LIMIT 1');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
        if (!$usuario || !password_verify($senhaAtual, $usuario['senha'])) {
            return false;
        }
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $query = $BD->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
        $query->bindValue(':senha', $hash, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function atualizarTelefone(int $id, ?string $telefone): void{
        $BD = $this->BDlog();
        $query = $BD->prepare('UPDATE usuarios SET telefone = :telefone WHERE id = :id');
        $query->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function excluirConta(int $id): void{
        $BD = $this->BDlog();
        $query = $BD->prepare('DELETE FROM usuarios WHERE id = :id');
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function atualizarCep(int $id, ?string $cep): void{
        $BD = $this->BDlog();
        $query = $BD->prepare('UPDATE usuarios SET cep = :cep WHERE id = :id');
        $query->bindValue(':cep', $cep, PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
    }

    public function criarAdminSeNecessario(){
        $this->criarTabelaUsuario();
        $BD = $this->BDlog();
        $query = $BD->prepare('SELECT COUNT(*) AS total FROM usuarios WHERE tipo = :tipo');
        $query->execute([':tipo' => 'admin']);
        $total = (int) $query->fetch(PDO::FETCH_ASSOC)['total'];
        if ($total === 0) {
            $senha = 'AdminResinoir#2026';
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $query = $BD->prepare('INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)');
            $query->execute([
                ':nome' => 'Administrador',
                ':email' => 'admin@resinoir.com',
                ':senha' => $hash,
                ':tipo' => 'admin'
            ]);
            echo "Admin criado: admin@resinoir.com / senha: $senha";
        }
    }
}
