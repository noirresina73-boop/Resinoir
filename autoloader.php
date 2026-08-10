<?php
spl_autoload_register(function ($nome_da_classe){
    $local = __DIR__ . '/' . str_replace('\\', '/', $nome_da_classe) . '.php';

        include $local;
});
?>