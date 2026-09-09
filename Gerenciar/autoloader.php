<?php
spl_autoload_register(function ($nome_da_classe){
    $localGerenciar = __DIR__ . '/' . str_replace('\\', '/', $nome_da_classe) . '.php';
    $localRaiz = dirname(__DIR__) . '/' . str_replace('\\', '/', $nome_da_classe) . '.php';

    if (file_exists($localGerenciar)) {
        include $localGerenciar;
    } elseif (file_exists($localRaiz)) {
        include $localRaiz;
    }
});
?>