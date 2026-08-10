<?php

use Controllers\ListController;

include '../autoloader.php';

$Controller = new ListController;
$Controller->apiPesquisa();

?>