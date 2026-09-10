<?php
session_start();
require_once __DIR__ . '/autoloader.php';
use Controllers\AuthController;

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$controller = new AuthController();
$usuario = $controller->buscarPorId((int) $_SESSION['usuario_id']);

if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'foto' && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $pasta = __DIR__ . '/assets/usuarios';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid('foto_') . '.' . $extensao;
        move_uploaded_file($_FILES['foto']['tmp_name'], $pasta . '/' . $nomeArquivo);
        $caminho = './assets/usuarios/' . $nomeArquivo;
        $controller->atualizarFoto((int) $_SESSION['usuario_id'], $caminho);
        $_SESSION['usuario_foto'] = $caminho;
        $usuario['foto'] = $caminho;
        $sucesso = 'Foto atualizada com sucesso!';
    } elseif ($acao === 'senha') {
        $senhaAtual = (string) ($_POST['senha_atual'] ?? '');
        $novaSenha = (string) ($_POST['nova_senha'] ?? '');
        $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

        if ($senhaAtual === '' || $novaSenha === '' || $confirmarSenha === '') {
            $erro = 'Preencha todos os campos de senha.';
        } elseif ($novaSenha !== $confirmarSenha) {
            $erro = 'As senhas não conferem.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
        } else {
            $ok = $controller->alterarSenha((int) $_SESSION['usuario_id'], $senhaAtual, $novaSenha);
            if ($ok) {
                $sucesso = 'Senha alterada com sucesso!';
            } else {
                $erro = 'Senha atual incorreta.';
            }
        }
    }
}

$fotoAtual = $usuario['foto'] ?? null;
$inicial = strtoupper(mb_substr($usuario['nome'] ?? 'U', 0, 1));
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Perfil - Resinoir</title>
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
  .perfil-header{
    text-align:center;
    margin-bottom:20px;
  }
  .perfil-foto{
    width:96px;
    height:96px;
    border-radius:50%;
    border:1px solid rgba(176,141,87,0.5);
    object-fit:cover;
    margin-bottom:10px;
    background:rgba(176,141,87,0.12);
    display:flex;
    align-items:center;
    justify-content:center;
    margin-left:auto;
    margin-right:auto;
    color:var(--gold-bright);
    font-size:32px;
    font-family:'Cinzel',serif;
  }
  .perfil-nome{
    font-family:'Cormorant Garamond',serif;
    font-size:20px;
    color:var(--bone);
  }
  .perfil-email{
    font-size:13px;
    color:var(--bone-dim);
    margin-top:4px;
  }
  .secao{
    margin-top:18px;
    padding-top:16px;
    border-top:1px solid rgba(176,141,87,0.18);
  }
  .secao h3{
    font-family:'Cinzel',serif;
    font-size:14px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--gold-bright);
    margin-bottom:10px;
  }
  label{
    font-size:11px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--bone-dim);
    display:block;
    margin-bottom:6px;
    margin-top:12px;
  }
  input[type="file"], input[type="password"]{
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
  .foto-preview{
    width:80px;
    height:80px;
    border-radius:50%;
    border:1px solid rgba(176,141,87,0.35);
    margin-top:8px;
    object-fit:cover;
    display:none;
  }
  button.btn-salvar{
    width:100%;
    margin-top:18px;
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
  .sucesso{
    background:rgba(157,227,166,0.12);
    border:1px solid rgba(157,227,166,0.35);
    color:#d7f5dc;
    padding:10px 12px;
    border-radius:10px;
    font-size:13px;
    margin-top:12px;
    text-align:center;
  }
  .voltar{
    display:block;
    text-align:center;
    margin-top:16px;
    font-size:13px;
    color:var(--bone-dim);
  }
  .voltar a{
    color:var(--gold-bright);
    text-decoration:none;
  }
  .voltar a:hover{text-decoration:underline;}
</style>
</head>
<body>
  <div class="card">
    <h1>Perfil</h1>
    <?php if ($erro): ?>
      <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
      <div class="sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <div class="perfil-header">
      <?php if ($fotoAtual): ?>
        <img src="<?= htmlspecialchars($fotoAtual) ?>" class="perfil-foto" alt="Foto de perfil">
      <?php else: ?>
        <div class="perfil-foto"><?= $inicial ?></div>
      <?php endif; ?>
      <div class="perfil-nome"><?= htmlspecialchars($usuario['nome']) ?></div>
      <div class="perfil-email"><?= htmlspecialchars($usuario['email']) ?></div>
    </div>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="acao" value="foto">
      <div class="secao">
        <h3>Alterar foto</h3>
        <label>Nova foto</label>
        <input type="file" name="foto" accept="image/*" required onchange="previewFoto(this)">
        <img id="previewFoto" class="foto-preview" alt="">
      </div>
      <button type="submit" class="btn-salvar">Salvar foto</button>
    </form>

    <form method="post">
      <input type="hidden" name="acao" value="senha">
      <div class="secao">
        <h3>Alterar senha</h3>
        <label>Senha atual</label>
        <input type="password" name="senha_atual" required>
        <label>Nova senha</label>
        <input type="password" name="nova_senha" required>
        <label>Confirmar nova senha</label>
        <input type="password" name="confirmar_senha" required>
      </div>
      <button type="submit" class="btn-salvar">Salvar senha</button>
    </form>

    <div class="voltar">
      <a href="index.php">Voltar para a loja</a>
    </div>
  </div>

  <script>
    function previewFoto(input){
      const preview = document.getElementById('previewFoto');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e){
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.style.display = 'none';
      }
    }
  </script>
</body>
</html>
