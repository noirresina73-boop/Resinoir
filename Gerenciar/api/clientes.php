<?php
require_once __DIR__ . '/../auth.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE IF NOT EXISTS clientes (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL UNIQUE, telefone VARCHAR(50) NULL, email VARCHAR(255) NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$colunas = $pdo->query('SHOW COLUMNS FROM clientes')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('telefone', $colunas, true)) $pdo->exec('ALTER TABLE clientes ADD COLUMN telefone VARCHAR(50) NULL');
if (!in_array('email', $colunas, true)) $pdo->exec('ALTER TABLE clientes ADD COLUMN email VARCHAR(255) NULL');

header('Content-Type: application/json; charset=utf-8');
$acao = $_POST['acao'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$nome = trim((string) ($_POST['nome'] ?? ''));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));

if ($acao === 'excluir') {
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['erro' => 'Cliente inválido.']);
        exit;
    }
    $pdo->prepare('DELETE FROM clientes WHERE id = :id')->execute([':id' => $id]);
    echo json_encode(['ok' => true]);
    exit;
}

if (!in_array($acao, ['criar', 'editar'], true) || $nome === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe o nome do cliente.']);
    exit;
}

try {
    if ($acao === 'editar' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE clientes SET nome = :nome, telefone = :telefone, email = :email WHERE id = :id');
        $stmt->execute([':id' => $id, ':nome' => $nome, ':telefone' => $telefone ?: null, ':email' => $email ?: null]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO clientes (nome, telefone, email) VALUES (:nome, :telefone, :email)');
        $stmt->execute([':nome' => $nome, ':telefone' => $telefone ?: null, ':email' => $email ?: null]);
    }
    echo json_encode(['ok' => true]);
} catch (PDOException $erro) {
    http_response_code(400);
    echo json_encode(['erro' => $erro->getCode() === '23000' ? 'Já existe um cliente com esse nome.' : 'Não foi possível salvar o cliente.']);
}
