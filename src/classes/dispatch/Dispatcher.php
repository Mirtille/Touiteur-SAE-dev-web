<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\Action;

class Dispatcher {
    private ?string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
            case 'default':
            default:
                // Action par défaut temporaire avant d'avoir AfficherListeTouitesAction
                $html = "<h2>Bienvenue sur Touiteur</h2><p>Page d'accueil par défaut.</p>";
                break;
        }

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touiteur</title>
</head>
<body>
    <header>
        <h1><a href="index.php">Touiteur</a></h1>
        <nav>
            <a href="index.php">Accueil</a>
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