<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\Action;
use iutnc\touiteur\action\AfficherListeTouitesAction;
use iutnc\touiteur\action\AfficherTouiteAction;
use iutnc\touiteur\action\AfficherTouitesAuteurAction;
use iutnc\touiteur\action\AfficherTouitesTagAction;
use iutnc\touiteur\action\SuivreUtilisateurAction;
use iutnc\touiteur\action\AbonnerTagAction;
use iutnc\touiteur\action\AfficherInfluenceursAction;
use iutnc\touiteur\action\PublierTouiteAction;
use iutnc\touiteur\action\AfficherMurAction;

class Dispatcher {

    private ?string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run() : void {

        switch ($this->action) {
            case 'default':
                $action = isset($_SESSION['user']) ? new AfficherMurAction() : new AfficherListeTouitesAction();
                break;

            case 'liste':
                $action = new AfficherListeTouitesAction();
                break;

            case 'detail':
                $action = new AfficherTouiteAction();
                break;

            case 'touites-auteur':
                $action = new AfficherTouitesAuteurAction();
                break;

            case 'touites-tag':
                $action = new AfficherTouitesTagAction();
                break;

            case 'suivre':
                $action = new SuivreUtilisateurAction();
                break;

            case 'abonner-tag':
                $action = new AbonnerTagAction();
                break;

            case 'influenceurs':
             $action = new AfficherInfluenceursAction();
             break;

            case 'publier':
             $action = new PublierTouiteAction();
             break;

            case 'mur':
                $action = new AfficherMurAction();
                break;

            default:
                http_response_code(404);
                $html = "<h2>Erreur 404</h2><p>Action inconnue.</p>";
                $this->renderPage($html);
                return;
        }

        $html = $action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html) : void {
        $lienAdmin = '';
        if (($_SESSION['user']['role_user'] ?? '') === 'admin') {
            $lienAdmin = '<a href="index.php?action=influenceurs">Influenceurs (Admin)</a>';
        }
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Touiteur</title>
            <link rel="stylesheet" href="css/style.css">
        </head>

        <body>

            <header>
                <h1><a href="index.php">Touiteur</a></h1>

                <nav>
                    <a href="index.php">Accueil</a>
                    <a href="index.php?action=abonner-tag">S'abonner à un tag</a>
                    <a href="index.php?action=mur">Mon mur</a>
                    <a href="index.php?action=publier">Publier un touite</a>
                    <a href="index.php?action=liste">Tous les touites</a>
                    $lienAdmin
                </nav>
            </header>

            <main>
                $html
            </main>

        </body>
        </html>
        HTML;
    }
}
