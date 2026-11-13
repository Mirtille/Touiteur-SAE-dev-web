<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class AfficherInfluenceursAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['role_user']) || $_SESSION['user']['role_user'] !== 'admin') {
            http_response_code(403);
            return "<h2>Accès refusé</h2><p>Cette page est réservée aux administrateurs.</p><a href='index.php'>Retour à l'accueil</a>";
        }

        $repository = new TouiteRepository();
        $influenceurs = $repository->getInfluenceurs();

        $html = "<h2>Les influenceurs (ordre croissant de suiveurs)</h2>";

        if (empty($influenceurs)) {
            $html .= "<p>Aucun utilisateur trouvé.</p>";
        } else {
            $html .= "<table><thead><tr><th>Utilisateur</th><th>Nombre de suiveurs</th></tr></thead><tbody>";
            foreach ($influenceurs as $user) {
                $nomComplet = htmlspecialchars($user['prenom_user'] . ' ' . $user['nom_user'], ENT_QUOTES, 'UTF-8');
                $idUser = (int) $user['id_user'];
                $nbSuiveurs = (int) $user['nb_suiveurs'];

                $html .= "<tr>
                            <td><a href='index.php?action=touites-auteur&id=$idUser'>$nomComplet</a></td>
                            <td>$nbSuiveurs</td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        }

        $html .= "<p><a href='index.php'>Retour à l'accueil</a></p>";
        return $html;
    }
}