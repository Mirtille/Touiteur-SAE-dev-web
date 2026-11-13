<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class AbonnerTagAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['id_user'])) {
            return "<h2>Accès refusé</h2><p>Vous devez être connecté pour vous abonner à un tag.</p>";
        }

        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tagSaisi = trim($_POST['tag'] ?? '');
            if ($tagSaisi === '') {
                $message = "<p style='color:red;'>Veuillez saisir un tag valide.</p>";
            } else {
                $repo = new TouiteRepository();
                $succes = $repo->abonnerTag((int) $_SESSION['user']['id_user'], $tagSaisi);
                $tagAffiche = htmlspecialchars(ltrim($tagSaisi, '#'), ENT_QUOTES, 'UTF-8');

                if ($succes) {
                    $message = "<p style='color:green;'>Vous êtes maintenant abonné au tag <strong>#$tagAffiche</strong> !</p>";
                } else {
                    $message = "<p style='color:red;'>Impossible de s'abonner à ce tag.</p>";
                }
            }
        }

        return <<<HTML
        <h2>S'abonner à un tag</h2>
        $message
        <form method="post" action="index.php?action=abonner-tag">
            <label for="tag">Nom du tag (ex: php, web, iut) :</label><br>
            <input type="text" id="tag" name="tag" placeholder="#php" required>
            <button type="submit">S'abonner</button>
        </form>
        <p><a href="index.php">Retour à la liste des touites</a></p>
        HTML;
    }
}