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
        $html = $renderer->render(Renderer::LONG);

        if (isset($_SESSION['user']['id_user'])) {
            $idConnecte = (int) $_SESSION['user']['id_user'];
            $idAuteur = (int) $touite->idAuteur;

            if ($idConnecte !== $idAuteur) {
                if ($repository->estSuivi($idConnecte, $idAuteur)) {
                    $html .= "<p><em>Vous suivez déjà cet auteur.</em></p>";
                } else {
                    $html .= "<p><a href='index.php?action=suivre&id=$idAuteur'>Suivre cet auteur</a></p>";
                }
            }
        }


        if (isset($_SESSION['user']['id_user'])) {
            $idUser = (int) $_SESSION['user']['id_user'];
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $token = htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            );

            $evaluation = $repository->getEvaluationUtilisateur(
                $idUser,
                $id
            );

            if ($evaluation === null) {
                $html .= <<<HTML
                <section class="evaluation">
                    <h3>Que pensez-vous de ce touite ?</h3>

                    <form method="POST" action="index.php?action=evaluation">
                        <input type="hidden" name="id_touite" value="$id">
                        <input type="hidden" name="csrf_token" value="$token">

                        <button type="submit" name="valeur" value="1">
                            Like
                        </button>

                        <button type="submit" name="valeur" value="-1">
                            Dislike
                        </button>
                    </form>
                </section>
                HTML;

            } else {
                $message = $evaluation === 1 ? "Vous avez liké ce touite." : "Vous avez disliké ce touite.";
                $html .= "<p><em>$message Vous ne pouvez voter qu'une seule fois.</em></p>";
            }
        }

        return $html;
    }
}