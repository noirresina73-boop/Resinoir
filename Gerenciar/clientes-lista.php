<?php
require_once __DIR__ . '/auth.php';

$pdo = new PDO('mysql:host=sql302.infinityfree.com;port=3306;dbname=if0_42359254_resinoir;charset=utf8mb4', 'if0_42359254', '1ZHLF0ZU3S1Rw');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE IF NOT EXISTS clientes (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL UNIQUE, telefone VARCHAR(50) NULL, email VARCHAR(255) NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$clientes = $pdo->query('SELECT id, nome, telefone, email FROM clientes ORDER BY nome ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resinoir — Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/gerenciar.css">
</head>
<body data-bs-theme="dark">
  <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
      <a class="navbar-brand">Clientes</a>
      <div class="topbar-actions">
        <a href="./index.php" class="btn btn-outline-light">Administração</a>
        <a href="./vendas.php" class="btn btn-outline-secondary">Vendas</a>
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalCliente" onclick="abrirModalCriar()">+ Novo cliente</button>
      </div>
    </div>
  </nav>

  <div class="page-shell">
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Clientes</h1>
        <div class="admin-subtitle">cadastro para suas vendas</div>
      </div>
    </div>

    <div class="panel">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Nome</th><th>Telefone</th><th>E-mail</th><th>Ações</th></tr></thead>
          <tbody>
            <?php if (!$clientes): ?><tr><td colspan="4" class="empty-state">Nenhum cliente cadastrado.</td></tr><?php endif; ?>
            <?php foreach ($clientes as $cliente): ?>
              <tr>
                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                <td><?= htmlspecialchars($cliente['telefone'] ?? '') ?: '<span class="text-secondary">—</span>' ?></td>
                <td><?= htmlspecialchars($cliente['email'] ?? '') ?: '<span class="text-secondary">—</span>' ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-light" onclick='abrirModalEditar(<?= json_encode($cliente, JSON_UNESCAPED_UNICODE) ?>)' data-bs-toggle="modal" data-bs-target="#modalCliente">Editar</button>
                  <button class="btn btn-sm btn-outline-danger" onclick="excluir(<?= (int) $cliente['id'] ?>, <?= htmlspecialchars(json_encode($cliente['nome'], JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>)">Excluir</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalCliente" tabindex="-1" data-bs-theme="dark">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header"><h1 class="modal-title fs-5" id="modalTitulo">Novo cliente</h1><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" id="clienteId">
        <label class="form-label">Nome</label><input type="text" class="form-control mb-3" id="clienteNome" autofocus>
        <label class="form-label">Telefone</label><input type="text" class="form-control mb-3" id="clienteTelefone">
        <label class="form-label">E-mail</label><input type="email" class="form-control" id="clienteEmail">
        <div id="erroCliente" class="form-text" style="color:#f88;"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success" onclick="salvar()">Salvar</button></div>
    </div></div>
  </div>

  <script>
    function abrirModalCriar() {
      document.getElementById('modalTitulo').textContent = 'Novo cliente';
      ['clienteId', 'clienteNome', 'clienteTelefone', 'clienteEmail'].forEach((id) => document.getElementById(id).value = '');
      document.getElementById('erroCliente').textContent = '';
    }
    function abrirModalEditar(cliente) {
      document.getElementById('modalTitulo').textContent = 'Editar cliente';
      document.getElementById('clienteId').value = cliente.id;
      document.getElementById('clienteNome').value = cliente.nome || '';
      document.getElementById('clienteTelefone').value = cliente.telefone || '';
      document.getElementById('clienteEmail').value = cliente.email || '';
      document.getElementById('erroCliente').textContent = '';
    }
    async function salvar() {
      const id = document.getElementById('clienteId').value;
      const nome = document.getElementById('clienteNome').value.trim();
      const erro = document.getElementById('erroCliente');
      if (!nome) { erro.textContent = 'Digite o nome do cliente.'; return; }
      const form = new FormData();
      form.append('acao', id ? 'editar' : 'criar');
      form.append('id', id); form.append('nome', nome);
      form.append('telefone', document.getElementById('clienteTelefone').value.trim());
      form.append('email', document.getElementById('clienteEmail').value.trim());
      const resposta = await fetch('./api/clientes.php', { method: 'POST', body: form });
      const dados = await resposta.json();
      if (dados.erro) { erro.textContent = dados.erro; return; }
      window.location.reload();
    }
    async function excluir(id, nome) {
      if (!confirm(`Excluir o cliente "${nome}"?`)) return;
      const form = new FormData(); form.append('acao', 'excluir'); form.append('id', id);
      const resposta = await fetch('./api/clientes.php', { method: 'POST', body: form });
      const dados = await resposta.json();
      if (dados.erro) { alert(dados.erro); return; }
      window.location.reload();
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
