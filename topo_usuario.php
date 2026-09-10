<?php
session_start();
?>
<?php if (isset($_SESSION['usuario_id'])): 
    $foto = $_SESSION['usuario_foto'] ?? null;
    $nome = $_SESSION['usuario_nome'] ?? 'Usuário';
    $tipo = $_SESSION['usuario_tipo'] ?? 'cliente';
    $isAdmin = $tipo === 'admin';
?>
<div style="position:fixed;top:12px;right:14px;z-index:20;display:flex;align-items:center;gap:10px;">
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
    <div style="position:relative;display:inline-block;" onclick="document.getElementById('menuPerfil').style.display = document.getElementById('menuPerfil').style.display === 'none' ? 'block' : 'none'">
        <?php if ($foto): ?>
            <img src="<?= htmlspecialchars($foto) ?>" style="width:36px;height:36px;border-radius:50%;border:1px solid rgba(176,141,87,0.5);object-fit:cover;cursor:pointer;display:block;">
        <?php else: ?>
            <div style="width:36px;height:36px;border-radius:50%;border:1px solid rgba(176,141,87,0.5);background:rgba(176,141,87,0.12);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#d4b077;font-size:12px;font-family:'Jost',sans-serif;"><?= strtoupper(mb_substr($nome,0,1)) ?></div>
        <?php endif; ?>
        <div id="menuPerfil" style="display:none;position:absolute;right:0;top:44px;background:#17171a;border:1px solid rgba(176,141,87,0.35);border-radius:12px;padding:8px;min-width:160px;box-shadow:0 10px 30px rgba(0,0,0,0.5);">
            <div style="padding:8px 10px;font-size:12px;color:#a89f8b;border-bottom:1px solid rgba(176,141,87,0.15);margin-bottom:4px;"><?= htmlspecialchars($nome) ?></div>
            <button onclick="document.getElementById('modalAlterarFoto').style.display='flex';document.getElementById('menuPerfil').style.display='none';" style="width:100%;text-align:left;padding:8px 10px;background:transparent;border:0;color:#f5efe6;font-family:'Jost',sans-serif;font-size:13px;cursor:pointer;border-radius:8px;">Alterar foto</button>
            <button onclick="window.location.href='logout.php'" style="width:100%;text-align:left;padding:8px 10px;background:transparent;border:0;color:#ffb7b7;font-family:'Jost',sans-serif;font-size:13px;cursor:pointer;border-radius:8px;">Sair</button>
        </div>
    </div>
</div>

<div id="modalAlterarFoto" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.85);z-index:9999;align-items:center;justify-content:center;padding:24px;">
    <div style="background:#17171a;border:1px solid rgba(176,141,87,0.35);border-radius:18px;padding:20px;width:100%;max-width:360px;position:relative;">
        <button onclick="document.getElementById('modalAlterarFoto').style.display='none'" style="position:absolute;top:10px;right:14px;background:transparent;border:0;color:#f5efe6;font-size:20px;cursor:pointer;">×</button>
        <h3 style="font-family:'Cinzel',serif;font-size:16px;margin-bottom:12px;color:#f5efe6;">Alterar foto de perfil</h3>
        <form method="post" action="atualizar_foto.php" enctype="multipart/form-data" style="text-align:center;">
            <input type="file" name="foto" accept="image/*" required style="margin-bottom:10px;">
            <button type="submit" style="width:100%;padding:10px;border:1px solid var(--gold);border-radius:10px;background:linear-gradient(180deg,#d4b077,#b08d57);color:#17171a;font-weight:700;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;">Salvar</button>
        </form>
    </div>
</div>
<?php else: ?>
<div style="position:fixed;top:12px;right:14px;z-index:20;display:flex;gap:8px;">
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
        backdrop-filter:blur(6px);
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
        backdrop-filter:blur(6px);
    ">Registrar</button>
</div>
<?php endif; ?>
