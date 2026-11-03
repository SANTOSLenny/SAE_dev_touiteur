<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;

session_start();

$dispatcher = new Dispatcher();
\iutnc\touiteur\db\ConnectionFactory::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
$dispatcher->run();