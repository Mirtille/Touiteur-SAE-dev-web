<?php

namespace iutnc\touiteur\controller;

use iutnc\touiteur\repository\TouiteRepository;

class TouiteController {

    private TouiteRepository $touiteRepository;

    public function __construct() {
        $this->touiteRepository = new TouiteRepository();
    }

    public function afficherListeTouites(): void {
        $touites = $this->touiteRepository->getDerniersTouites();
        require __DIR__ . '/../../vues/liste.php';
    }
}
