<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\repository\TouiteurRepository;
session_start();

try {
    TouiteurRepository::setConfig(__DIR__ . '/src/config/touiteur.db.ini');
} catch (\Exception $e) {
    die("erreur" . $e->getMessage());
}

$dispatcher = new Dispatcher();
$dispatcher->run();