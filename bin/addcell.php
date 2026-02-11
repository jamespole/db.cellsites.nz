#!/usr/local/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

$addCellCommand = new JamesPole\DbCellsitesNz\AddCellCommand();
$addCellCommand->main();
