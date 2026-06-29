<?php

use Controllers\ListController;

include 'autoloader.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resinoir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styleIndex.css">
  </head>
  <body>
    <h1>Helloo, world!</h1>
    <img src="https://raw.githubusercontent.com/noirresina73-boop/fts/refs/heads/main/FT_broche_craneo%20(1).jpeg?token=GHSAT0AAAAAAEAZHUEYUYBYW22ZLHOLYKGC2SBZ2FQ" alt="">
    <p>
        <?php
        $Listar = new ListController;
        $Listar = $Listar->listProdutos();
        ?>
    </p>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>