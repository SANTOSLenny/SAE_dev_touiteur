<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\db\ConnectionFactory;
session_start();

try {
    ConnectionFactory::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
    TouiteurRepository::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
} catch (\Exception $e) {
    die("erreur" . $e->getMessage());
}

$dispatcher = new Dispatcher();
$dispatcher->run();