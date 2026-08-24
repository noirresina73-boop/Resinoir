<?php
session_start();

$SENHA_CORRETA = 'NoirresinSite73$'; // troque pela senha que quiser

if (isset($_POST['senha'])) {
    if ($_POST['senha'] === $SENHA_CORRETA) {
        $_SESSION['logado'] = true;
    } else {
        $erro = 'Senha errada';
    }
}

if (!isset($_SESSION['logado'])) {
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <body style="display:flex;align-items:center;justify-content:center;height:100vh;background:#131F24;font-family:sans-serif;">
        <form method="post" style="text-align:center;">
            <input type="password" name="senha" placeholder="Senha" autofocus style="padding:8px;">
            <button type="submit" style="padding:8px 16px;">Entrar</button>
            <?php if (isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
        </form>
    </body>
    </html>
    <?php
    exit;
}