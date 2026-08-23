<?php
use Controllers\infosController;
include 'autoloader.php';

$Controller = new infosController;
$produtos = $Controller->listarTodos();
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir — Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  </head>
  <body data-bs-theme="dark">
    <nav class="navbar bg-body-tertiary" data-bs-theme="dark">
      <div class="container-fluid">
        <a class="navbar-brand">Produtos</a>
        <a href="./infos.php" class="btn btn-outline-success">+ Novo produto</a>
      </div>
    </nav>

    <div class="container py-4">
      <table class="table table-dark table-hover align-middle">
        <thead>
          <tr>
            <th>Capa</th>
            <th>Nome</th>
            <th>Valor</th>
            <th>Estoque</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><img src=".<?= htmlspecialchars($p['capa']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td>R$ <?= htmlspecialchars($p['valor']) ?></td>
              <td><?= htmlspecialchars($p['estoque']) ?></td>
              <td><a href="./infos.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-light">Editar</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </body>
</html>