<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\render\Renderer;

class AfficherTouitesAuteurAction extends Action {

    public function execute(): string {
        $idAuteur = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idAuteur || $idAuteur <= 0) {
            return "<h2>Erreur</h2><p>Auteur invalide.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $repository = new TouiteRepository();
        $touites = $repository->getTouitesParAuteur($idAuteur);

        if (empty($touites)) {
            return "<h2>Touites de l'utilisateur</h2><p>Aucun touite trouvé pour cet auteur.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $nomAuteur = htmlspecialchars($touites[0]->auteurNom ?? 'Utilisateur', ENT_QUOTES, 'UTF-8');
        $html = "<h2>Touites de $nomAuteur</h2>";

        foreach ($touites as $touite) {
            $renderer = new TouiteRenderer($touite);
            $html .= $renderer->render(Renderer::COMPACT);
        }

        $html .= "<p><a href='index.php'>Retour à tous les touites</a></p>";
        return $html;
    }
}