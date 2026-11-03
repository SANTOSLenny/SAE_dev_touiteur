<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\repository\TouiteurRepository;

session_start();

TouiteurRepository::setConfig(
    __DIR__ . '/src/config/touiteur.db.ini'
);

$dispatcher = new Dispatcher();
\iutnc\touiteur\db\ConnectionFactory::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
$dispatcher->run();