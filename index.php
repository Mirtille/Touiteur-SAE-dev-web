<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\repository\Connexion;
use iutnc\touiteur\dispatch\Dispatcher;

try {

    Connexion::setConfig(__DIR__ . '/config/db.ini');
    $dispatcher = new Dispatcher();
    $dispatcher->run();

} catch (Throwable $e) {
    /*error_log($e->getMessage());
    http_response_code(500);
    echo "Une erreur est survenue.";*/
    echo "<pre>";
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Fichier : " . $e->getFile() . "\n";
    echo "Ligne : " . $e->getLine();
    echo "</pre>";
}
