<?php
require_once __DIR__ . '/auth.php';

use Controllers\infosController;
use Controllers\CatalogoAuxController;
include 'autoloader.php';

$nome = trim($_GET['nome'] ?? '');
$categoria = (int) ($_GET['categoria'] ?? 0);
$colecao = (int) ($_GET['colecao'] ?? 0);

$Controller = new infosController;
$produtos = $Controller->listarTodos(1, $nome, $categoria, $colecao);

$Aux = new CatalogoAuxController;
$categorias = $Aux->listarCategorias();
$colecoes = $Aux->listarColecoes();
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

      <form method="get" class="row g-2 mb-4">
        <div class="col-md-5">
          <input type="text" name="nome" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($nome) ?>">
        </div>
        <div class="col-md-3">
          <select name="categoria" class="form-select">
            <option value="0">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $categoria === (int) $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select name="colecao" class="form-select">
            <option value="0">Todas as coleções</option>
            <?php foreach ($colecoes as $co): ?>
              <option value="<?= $co['id'] ?>" <?= $colecao === (int) $co['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($co['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-1">
          <button type="submit" class="btn btn-outline-light w-100">Filtrar</button>
        </div>
      </form>

      <?php if ($nome !== '' || $categoria > 0 || $colecao > 0): ?>
        <div class="mb-3">
          <a href="./produtos-lista.php" class="btn btn-sm btn-outline-secondary">Limpar filtros</a>
        </div>
      <?php endif; ?>

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
          <?php if (empty($produtos)): ?>
            <tr><td colspan="5" class="text-center text-secondary">Nenhum produto encontrado.</td></tr>
          <?php endif; ?>
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