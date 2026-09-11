<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    $foto = $_SESSION['usuario_foto'] ?? null;
    $nome = $_SESSION['usuario_nome'] ?? 'Usuário';
    $tipo = $_SESSION['usuario_tipo'] ?? 'cliente';
    $isAdmin = $tipo === 'admin';
?>
<?php if ($isAdmin): ?>
    <button onclick="window.location.href='./Gerenciar/produtos-lista.php'" style="
        background:rgba(176,141,87,0.12);
        border:1px solid rgba(176,141,87,0.5);
        color:#d4b077;
        padding:6px 12px;
        border-radius:20px;
        font-family:'Jost',sans-serif;
        font-size:10px;
        letter-spacing:1.5px;
        text-transform:uppercase;
        cursor:pointer;
        backdrop-filter:blur(6px);
    ">Gerenciar</button>
<?php endif; ?>
<div style="position:relative;display:inline-block;" onclick="event.stopPropagation();document.getElementById('menuPerfil').style.display = document.getElementById('menuPerfil').style.display === 'none' ? 'block' : 'none'">
    <?php if ($foto): ?>
        <img src="<?= htmlspecialchars($foto) ?>" style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(176,141,87,0.5);object-fit:cover;cursor:pointer;display:block;">
    <?php else: ?>
        <div style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(176,141,87,0.5);background:rgba(176,141,87,0.12);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#d4b077;font-size:10px;font-family:'Jost',sans-serif;"><?= strtoupper(mb_substr($nome,0,1)) ?></div>
    <?php endif; ?>
    <div id="menuPerfil" style="display:none;position:absolute;right:0;top:38px;background:#17171a;border:1px solid rgba(176,141,87,0.35);border-radius:12px;padding:8px;min-width:160px;box-shadow:0 10px 30px rgba(0,0,0,0.5);">
        <div style="padding:8px 10px;font-size:12px;color:#a89f8b;border-bottom:1px solid rgba(176,141,87,0.15);margin-bottom:4px;"><?= htmlspecialchars($nome) ?></div>
        <button onclick="window.location.href='perfil.php'" style="width:100%;text-align:left;padding:8px 10px;background:transparent;border:0;color:#f5efe6;font-family:'Jost',sans-serif;font-size:13px;cursor:pointer;border-radius:8px;">Meu perfil</button>
        <button onclick="window.location.href='logout.php'" style="width:100%;text-align:left;padding:8px 10px;background:transparent;border:0;color:#ffb7b7;font-family:'Jost',sans-serif;font-size:13px;cursor:pointer;border-radius:8px;">Sair</button>
    </div>
</div>
<?php } else { ?>
    <button onclick="window.location.href='login.php'" style="
        background:transparent;
        border:1px solid rgba(176,141,87,0.5);
        color:#d4b077;
        padding:6px 12px;
        border-radius:20px;
        font-family:'Jost',sans-serif;
        font-size:10px;
        letter-spacing:1.5px;
        text-transform:uppercase;
        cursor:pointer;
    ">Entrar</button>
    <button onclick="window.location.href='registro.php'" style="
        background:rgba(176,141,87,0.12);
        border:1px solid rgba(176,141,87,0.5);
        color:#d4b077;
        padding:6px 12px;
        border-radius:20px;
        font-family:'Jost',sans-serif;
        font-size:10px;
        letter-spacing:1.5px;
        text-transform:uppercase;
        cursor:pointer;
    ">Registrar</button>
<?php } ?>