<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\render\Renderer;

class AfficherTouitesTagAction extends Action {

    public function execute(): string {
        $tag = trim($_GET['tag'] ?? '');

        if ($tag === '') {
            return "<h2>Erreur</h2><p>Tag invalide.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $repository = new TouiteRepository();
        $touites = $repository->getTouitesParTag($tag);
        $tagSecurise = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');

        $html = "<h2>Touites associés à #$tagSecurise</h2>";

        if (empty($touites)) {
            $html .= "<p>Aucun touite trouvé pour ce tag.</p>";
        } else {
            foreach ($touites as $touite) {
                $renderer = new TouiteRenderer($touite);
                $html .= $renderer->render(Renderer::COMPACT);
            }
        }

        $html .= "<p><a href='index.php'>Retour à tous les touites</a></p>";
        return $html;
    }
}