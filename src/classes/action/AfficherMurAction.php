<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\render\Renderer;

class AfficherMurAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['id_user'])) {
            return "<h2>Accès refusé</h2><p>Vous devez être connecté pour accéder à votre mur.</p><a href='index.php?action=liste'>Voir tous les touites</a>";
        }

        $idUser = (int) $_SESSION['user']['id_user'];
        $prenom = htmlspecialchars($_SESSION['user']['prenom_user'] ?? '', ENT_QUOTES, 'UTF-8');

        $repository = new TouiteRepository();
        $touites = $repository->getMurUtilisateur($idUser);

        $html = "<h2>Mon mur ($prenom)</h2>";

        $html .= <<<HTML
        <form method="post" action="index.php?action=abonner-tag" style="margin-bottom: 1.5rem;">
            <label for="tag-mur">S'abonner à un tag :</label>
            <input type="text" id="tag-mur" name="tag" placeholder="#php" required>
            <button type="submit">S'abonner</button>
        </form>
        HTML;

        if (empty($touites)) {
            $html .= "<p>Votre mur est vide pour le moment. Suivez des auteurs ou abonnez-vous à des tags pour voir leurs touites ici !</p>";
        } else {
            foreach ($touites as $touite) {
                $renderer = new TouiteRenderer($touite);
                $html .= $renderer->render(Renderer::COMPACT);
            }
        }

        return $html;
    }
}