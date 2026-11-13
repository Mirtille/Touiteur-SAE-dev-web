<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\render\Renderer;

class AfficherListeTouitesAction extends Action {

    public function execute(): string {
        $repository = new TouiteRepository();
        $touites = $repository->getDerniersTouites();

        $html = "<h2>Les derniers touites</h2>";

        if (empty($touites)) {
            $html .= "<p>Aucun touite publié pour le moment.</p>";
        } else {
            foreach ($touites as $touite) {
                $renderer = new TouiteRenderer($touite);
                $html .= $renderer->render(Renderer::COMPACT);
            }
        }

        return $html;
    }
}
