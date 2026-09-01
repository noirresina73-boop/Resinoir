<?php
require_once __DIR__ . '/../auth.php';

$pdo = new PDO('mysql:host=localhost;dbname=resinior', 'root', 'senha');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?msg=' . urlencode('Método inválido.'));
    exit;
}

$acao = $_POST['acao'] ?? '';
if ($acao !== 'salvar_frete') {
    header('Location: ../index.php?msg=' . urlencode('Ação inválida.'));
    exit;
}

$precoGasolina = trim((string) ($_POST['preco_gasolina'] ?? ''));
$cepOrigem = trim((string) ($_POST['cep_origem'] ?? '85506290'));

foreach ([
    ['preco_gasolina', $precoGasolina !== '' ? $precoGasolina : '5.80'],
    ['cep_origem', $cepOrigem !== '' ? preg_replace('/\D+/', '', $cepOrigem) : '85506290'],
] as [$chave, $valor]) {
    $stmt = $pdo->prepare('INSERT INTO configuracoes (chave, valor, atualizado_em) VALUES (:chave, :valor, NOW()) ON DUPLICATE KEY UPDATE valor = VALUES(valor), atualizado_em = NOW()');
    $stmt->execute([':chave' => $chave, ':valor' => $valor]);
}

header('Location: ../index.php?msg=' . urlencode('Configurações de frete atualizadas com sucesso.'));
exit;
