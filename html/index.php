<?php

declare(strict_types=1);

require '../vendor/autoload.php';
// TODO: establish PDO object here
$output = JamesPole\DbCellsitesNz\Output\Router::getOutput($_SERVER['REQUEST_URI']);
$output->output();
