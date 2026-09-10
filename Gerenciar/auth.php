<?php
session_start();

require_once __DIR__ . '/autoloader.php';
use Controllers\AuthController;

$controller = new AuthController();
$controller->criarAdminSeNecessario();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim((string) $_POST['email']);
    $senha = (string) $_POST['senha'];
    $usuario = $controller->login($email, $senha);
    if ($usuario && $usuario['tipo'] === 'admin') {
        $_SESSION['usuario_id'] = (int) $usuario['id'];
        $_SESSION['usuario_nome'] = (string) $usuario['nome'];
        $_SESSION['usuario_tipo'] = (string) $usuario['tipo'];
        $_SESSION['usuario_foto'] = $usuario['foto'] ?: null;
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Acesso restrito a administradores.';
    }
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Área administrativa - Resinoir</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
      body{margin:0;background:#131F24;color:#e9e0c9;font-family:'Jost',sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;}
      .card{width:100%;max-width:360px;background:#1c1f24;border:1px solid rgba(176,141,87,0.25);border-radius:16px;padding:24px;}
      h1{font-family:'Cinzel',serif;font-size:18px;text-align:center;margin-bottom:16px;}
      input{width:100%;padding:10px;margin-bottom:10px;border-radius:8px;border:1px solid rgba(176,141,87,0.35);background:#131F24;color:#fff;}
      button{width:100%;padding:10px;border-radius:8px;border:1px solid #b08d57;background:linear-gradient(180deg,#d4b077,#b08d57);color:#131F24;font-weight:700;cursor:pointer;}
      .erro{color:#ffb7b7;text-align:center;margin-top:10px;font-size:13px;}
    </style>
    </head>
    <body>
      <div class="card">
        <h1>Acesso restrito</h1>
        <form method="post">
          <input type="email" name="email" placeholder="Email" required autofocus>
          <input type="password" name="senha" placeholder="Senha" required>
          <button type="submit">Entrar</button>
        </form>
        <?php if (isset($erro)) echo "<div class='erro'>$erro</div>"; ?>
      </div>
    </body>
    </html>
    <?php
    exit;
}
