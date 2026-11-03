<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;

session_start();

try {
    \iutnc\touiteur\db\ConnectionFactory::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
} catch (\Exception $e) {
    die("erreur" . $e->getMessage());
}

$dispatcher = new Dispatcher();
$dispatcher->run();