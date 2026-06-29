<?php
spl_autoload_register(function ($nome_da_classe){
    $local = './'. $nome_da_classe .'.php';

        include $local;
});
?>