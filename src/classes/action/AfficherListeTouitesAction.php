<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class AfficherListeTouitesAction extends Action {

    public function execute(): string {
        $repository = new TouiteRepository();
        $touites = $repository->getDerniersTouites();

        ob_start();
        require __DIR__ . '/../../../vues/liste.php';
        return ob_get_clean();
    }
}
