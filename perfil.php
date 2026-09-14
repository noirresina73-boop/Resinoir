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
$editando = $_GET['editar'] ?? '';

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
    } elseif ($acao === 'telefone') {
        $telefone = trim((string) ($_POST['telefone'] ?? ''));
        $controller->atualizarTelefone((int) $_SESSION['usuario_id'], $telefone !== '' ? $telefone : null);
        $usuario['telefone'] = $telefone !== '' ? $telefone : null;
        $sucesso = 'Telefone atualizado com sucesso!';
        $editando = '';
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
                $editando = '';
            } else {
                $erro = 'Senha atual incorreta.';
            }
        }
    } elseif ($acao === 'excluir') {
        $controller->excluirConta((int) $_SESSION['usuario_id']);
        session_destroy();
        header('Location: index.php');
        exit;
    } elseif ($acao === 'cep') {
        $cep = trim((string) ($_POST['cep'] ?? ''));
        $controller->atualizarCep((int) $_SESSION['usuario_id'], $cep !== '' ? $cep : null);
        $usuario['cep'] = $cep !== '' ? $cep : null;
        $_SESSION['usuario_cep'] = $cep !== '' ? $cep : '';
        $sucesso = 'CEP atualizado com sucesso!';
        $editando = '';
    }
}

$fotoAtual = $usuario['foto'] ?? null;
$inicial = strtoupper(mb_substr($usuario['nome'] ?? 'U', 0, 1));
$telefoneAtual = $usuario['telefone'] ?? '';
$cepAtual = $usuario['cep'] ?? '';
$_SESSION['usuario_cep'] = $cepAtual !== '' ? $cepAtual : '';
$_SESSION['usuario_foto'] = $usuario['foto'] ?? null;
$_SESSION['usuario_nome'] = $usuario['nome'] ?? '';
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
  .info-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:10px;
  }
  .info-valor{
    font-size:14px;
    color:var(--bone);
    word-break:break-word;
  }
  .info-vazio{
    font-size:13px;
    color:var(--bone-dim);
    font-style:italic;
  }
  .btn-editar{
    padding:6px 12px;
    border:1px solid rgba(176,141,87,0.5);
    border-radius:20px;
    background:transparent;
    color:#d4b077;
    font-family:'Jost',sans-serif;
    font-size:10px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    cursor:pointer;
    white-space:nowrap;
  }
  .btn-editar:hover{
    background:rgba(176,141,87,0.12);
  }
  .form-edicao{
    margin-top:10px;
    padding:12px;
    background:rgba(255,255,255,0.02);
    border:1px solid rgba(176,141,87,0.25);
    border-radius:12px;
  }
  label{
    font-size:11px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    color:var(--bone-dim);
    display:block;
    margin-bottom:6px;
    margin-top:10px;
  }
  input[type="text"], input[type="email"], input[type="password"], input[type="tel"]{
    width:100%;
    padding:10px 12px;
    border-radius:10px;
    border:1px solid rgba(176,141,87,0.35);
    background:rgba(255,255,255,0.03);
    color:#fff;
    font-family:'Jost',sans-serif;
    font-size:14px;
    outline:none;
  }
  input:focus{border-color:var(--gold-bright);}
  .file-input-wrapper{
    position:relative;
    overflow:hidden;
    display:inline-block;
    width:100%;
    margin-top:10px;
  }
  .file-input-wrapper input[type="file"]{
    position:absolute;
    inset:0;
    opacity:0;
    cursor:pointer;
    padding:0;
    border:none;
    background:transparent;
    width:100%;
    height:100%;
  }
  .file-input-label{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1px dashed rgba(176,141,87,0.55);
    background:rgba(176,141,87,0.06);
    color:var(--bone-dim);
    font-size:13px;
    cursor:pointer;
    transition:.2s;
  }
  .file-input-wrapper:hover .file-input-label{
    border-color:var(--gold-bright);
    color:var(--gold-bright);
    background:rgba(176,141,87,0.12);
  }
  .foto-preview{
    width:80px;
    height:80px;
    border-radius:50%;
    border:1px solid rgba(176,141,87,0.35);
    margin-top:8px;
    object-fit:cover;
    display:none;
  }
  .botoes-edicao{
    display:flex;
    gap:8px;
    margin-top:12px;
  }
  .btn-salvar{
    flex:1;
    padding:10px;
    border:1px solid var(--gold);
    border-radius:10px;
    background:linear-gradient(180deg,var(--gold-bright),var(--gold));
    color:#17171a;
    font-weight:700;
    font-size:11px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    cursor:pointer;
  }
  .btn-cancelar{
    flex:1;
    padding:10px;
    border:1px solid rgba(176,141,87,0.5);
    border-radius:10px;
    background:transparent;
    color:var(--bone-dim);
    font-size:11px;
    letter-spacing:1.5px;
    text-transform:uppercase;
    cursor:pointer;
  }
  .btn-excluir{
    width:100%;
    margin-top:18px;
    padding:10px;
    border:1px solid rgba(255,120,120,0.5);
    border-radius:10px;
    background:rgba(255,120,120,0.08);
    color:#ffb7b7;
    font-weight:700;
    font-size:11px;
    letter-spacing:1.5px;
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

    <div class="secao">
      <h3>Foto de perfil</h3>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="foto">
        <label>Nova foto</label>
        <div class="file-input-wrapper">
          <input type="file" name="foto" accept="image/*" required onchange="previewFoto(this)">
          <div class="file-input-label">Escolher arquivo</div>
        </div>
        <img id="previewFoto" class="foto-preview" alt="">
        <div class="botoes-edicao">
          <button type="submit" class="btn-salvar">Salvar foto</button>
        </div>
      </form>
    </div>

    <div class="secao">
      <h3>Telefone</h3>
      <?php if ($editando === 'telefone'): ?>
        <form method="post">
          <input type="hidden" name="acao" value="telefone">
          <div class="form-edicao">
            <label>Novo telefone</label>
            <input type="tel" name="telefone" value="<?= htmlspecialchars($telefoneAtual) ?>" autocomplete="off">
            <div class="botoes-edicao">
              <button type="submit" class="btn-salvar">Salvar</button>
              <a href="perfil.php" class="btn-cancelar" style="text-align:center;text-decoration:none;">Cancelar</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="info-row">
          <span class="info-valor <?= $telefoneAtual === '' ? 'info-vazio' : '' ?>">
            <?= $telefoneAtual !== '' ? htmlspecialchars($telefoneAtual) : 'Nenhum telefone cadastrado' ?>
          </span>
          <button class="btn-editar" onclick="window.location.href='perfil.php?editar=telefone'">Editar telefone</button>
        </div>
      <?php endif; ?>
    </div>

    <div class="secao">
      <h3>CEP</h3>
      <?php if ($editando === 'cep'): ?>
        <form method="post">
          <input type="hidden" name="acao" value="cep">
          <div class="form-edicao">
            <label>CEP</label>
            <input type="text" name="cep" value="<?= htmlspecialchars($cepAtual) ?>" autocomplete="off">
            <div class="botoes-edicao">
              <button type="submit" class="btn-salvar">Salvar</button>
              <a href="perfil.php" class="btn-cancelar" style="text-align:center;text-decoration:none;">Cancelar</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="info-row">
          <span class="info-valor <?= $cepAtual === '' ? 'info-vazio' : '' ?>">
            <?= $cepAtual !== '' ? htmlspecialchars($cepAtual) : 'Nenhum CEP cadastrado' ?>
          </span>
          <button class="btn-editar" onclick="window.location.href='perfil.php?editar=cep'">Editar CEP</button>
        </div>
      <?php endif; ?>
    </div>

    <div class="secao">
      <h3>Senha</h3>
      <?php if ($editando === 'senha'): ?>
        <form method="post">
          <input type="hidden" name="acao" value="senha">
          <div class="form-edicao">
            <label>Senha atual</label>
            <input type="password" name="senha_atual" required autocomplete="current-password">
            <label>Nova senha</label>
            <input type="password" name="nova_senha" required autocomplete="new-password">
            <label>Confirmar nova senha</label>
            <input type="password" name="confirmar_senha" required autocomplete="new-password">
            <div class="botoes-edicao">
              <button type="submit" class="btn-salvar">Salvar senha</button>
              <a href="perfil.php" class="btn-cancelar" style="text-align:center;text-decoration:none;">Cancelar</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="info-row">
          <span class="info-valor info-vazio">••••••••</span>
          <button class="btn-editar" onclick="window.location.href='perfil.php?editar=senha'">Editar senha</button>
        </div>
      <?php endif; ?>
    </div>

    <div class="secao">
      <h3>Excluir conta</h3>
      <p style="font-size:12px;color:var(--bone-dim);margin-bottom:10px;">Ao excluir, todos os seus dados serão removidos permanentemente.</p>
      <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.');">
        <input type="hidden" name="acao" value="excluir">
        <button type="submit" class="btn-excluir">Excluir minha conta</button>
      </form>
    </div>

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
