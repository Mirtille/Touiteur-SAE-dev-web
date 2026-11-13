<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\render\Renderer;

class AfficherTouiteAction extends Action {

    public function execute(): string {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id <= 0) {
            return "<h2>Erreur</h2><p>Identifiant de touite invalide.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $repository = new TouiteRepository();
        $touite = $repository->getTouiteById($id);

        if ($touite === null) {
            return "<h2>Touite introuvable</h2><p>Ce touite n'existe pas ou a été supprimé.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $renderer = new TouiteRenderer($touite);
        return $renderer->render(Renderer::LONG);
    }
}