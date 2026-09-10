<?php
require_once __DIR__ . '/autoloader.php';
use Controllers\AuthController;

$controller = new AuthController();
$controller->criarAdminSeNecessario();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if ($email === '' || $senha === '') {
        $erro = 'Preencha email e senha.';
    } else {
        $usuario = $controller->login($email, $senha);
        if ($usuario) {
            session_start();
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_nome'] = (string) $usuario['nome'];
            $_SESSION['usuario_tipo'] = (string) $usuario['tipo'];
            $_SESSION['usuario_foto'] = $usuario['foto'] ?: null;
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Email ou senha inválidos.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Entrar - Resinoir</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --ink:#0a0908;
    --void:#050405;
    --bone:#e9e0c9;
    --bone-dim:#a89f8b;
    --gold:#b08d57;
    --gold-bright:#d4b077;
  }
  *{box-sizing:border-box;margin:0;padding:0;}
  body{
    background:radial-gradient(ellipse at 50% 0%, rgba(124,31,46,0.18), transparent 55%),
                linear-gradient(180deg,#0a0908 0%,#150b1a 50%,#1c0f22 100%);
    color:var(--bone);
    font-family:'Jost',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
  }
  .card{
    width:100%;
    max-width:420px;
    background:rgba(20,16,19,0.85);
    border:1px solid rgba(176,141,87,0.25);
    border-radius:18px;
    padding:28px 24px 24px;
    box-shadow:0 20px 60px rgba(0,0,0,0.55);
    backdrop-filter:blur(6px);
  }
  .card h1{
    font-family:'Cinzel',serif;
    font-size:22px;
    letter-spacing:2px;
    text-align:center;
    margin-bottom:18px;
    color:var(--bone);
  }
  label{
    font-size:11px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--bone-dim);
    display:block;
    margin-bottom:6px;
    margin-top:14px;
  }
  input[type="email"], input[type="password"]{
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1px solid rgba(176,141,87,0.35);
    background:rgba(255,255,255,0.03);
    color:#fff;
    font-family:'Jost',sans-serif;
    font-size:14px;
    outline:none;
  }
  input:focus{border-color:var(--gold-bright);}
  button.btn-entrar{
    width:100%;
    margin-top:20px;
    padding:13px;
    border:1px solid var(--gold);
    border-radius:10px;
    background:linear-gradient(180deg,var(--gold-bright),var(--gold));
    color:#17171a;
    font-weight:700;
    font-size:12px;
    letter-spacing:2px;
    text-transform:uppercase;
    cursor:pointer;
  }
  .erro{
    background:rgba(255,120,120,0.12);
    border:1px solid rgba(255,120,120,0.35);
    color:#ffd7d7;
    padding:10px 12px;
    border-radius:10px;
    font-size:13px;
    margin-top:12px;
    text-align:center;
  }
  .rodape{
    text-align:center;
    margin-top:16px;
    font-size:13px;
    color:var(--bone-dim);
  }
  .rodape a{
    color:var(--gold-bright);
    text-decoration:none;
  }
  .rodape a:hover{text-decoration:underline;}
</style>
</head>
<body>
  <div class="card">
    <h1>Entrar</h1>
    <?php if ($erro): ?>
      <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required autofocus>
      <label>Senha</label>
      <input type="password" name="senha" required>
      <button type="submit" class="btn-entrar">Entrar</button>
    </form>
    <div class="rodape">
      Ainda não tem conta? <a href="registro.php">Registrar</a>
    </div>
  </div>
</body>
</html>
