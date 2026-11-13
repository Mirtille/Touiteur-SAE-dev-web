<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class SuivreUtilisateurAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['id_user'])) {
            return "<h2>Accès refusé</h2><p>Vous devez être connecté pour suivre un utilisateur.</p>";
        }

        $idSuiveur = (int) $_SESSION['user']['id_user'];
        $idSuivi = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idSuivi || $idSuivi <= 0) {
            return "<h2>Erreur</h2><p>Utilisateur invalide.</p><a href='index.php'>Retour</a>";
        }

        if ($idSuiveur === $idSuivi) {
            return "<h2>Action impossible</h2><p>Vous ne pouvez pas vous suivre vous-même.</p><a href='index.php'>Retour</a>";
        }

        $repo = new TouiteRepository();
        $repo->suivreUtilisateur($idSuiveur, $idSuivi);

        return "<h2>Abonnement confirmé</h2><p>Vous suivez désormais cet utilisateur !</p><p><a href='index.php'>Retour à la liste des touites</a></p>";
    }
}