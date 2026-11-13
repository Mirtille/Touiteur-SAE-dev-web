<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\repository\Connexion;
use iutnc\touiteur\dispatch\Dispatcher;

session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id_user' => 1,
        'nom_user' => 'Dupont',
        'prenom_user' => 'Alice',
        'email_user' => 'alice@touiteur.fr',
        'role_user' => 'admin'
    ];
}

try {

    Connexion::setConfig(__DIR__ . '/config.db.ini');
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
