<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class AfficherSuiveursAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['id_user'])) {
            return "<h2>Accès refusé</h2><p>Vous devez être connecté pour voir vos abonnés.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $idUser = (int) $_SESSION['user']['id_user'];
        $repository = new TouiteRepository();
        $suiveurs = $repository->getSuiveurs($idUser);
        $nbSuiveurs = count($suiveurs);

        $html = "<h2>Mes abonnés ($nbSuiveurs)</h2>";

        if ($nbSuiveurs === 0) {
            $html .= "<p>Personne ne vous suit pour le moment.</p>";
        } else {
            $html .= "<table><thead><tr><th>Utilisateur</th></tr></thead><tbody>";
            foreach ($suiveurs as $suiveur) {
                $nomComplet = htmlspecialchars($suiveur['prenom_user'] . ' ' . $suiveur['nom_user'], ENT_QUOTES, 'UTF-8');
                $idSuiveur = (int) $suiveur['id_user'];
                $html .= "<tr><td><a href='index.php?action=touites-auteur&id=$idSuiveur'>$nomComplet</a></td></tr>";
            }
            $html .= "</tbody></table>";
        }

        $html .= "<p><a href='index.php'>Retour à l'accueil</a></p>";
        return $html;
    }
}