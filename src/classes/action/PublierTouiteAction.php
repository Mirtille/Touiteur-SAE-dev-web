<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class PublierTouiteAction extends Action {

    public function execute(): string {

        if (!isset($_SESSION['user']['id_user'])) {
            return "
                <h2>Accès refusé</h2>
                <p>Vous devez être connecté pour publier un touite.</p>
            ";
        }

        $message = '';

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(403);
                return "<h2>Requête invalide.</h2>";
            }

            $texte = $_POST['texte'] ?? '';

            if (!is_string($texte)) {
                $message = "<p>Texte invalide.</p>";
            } else {
                try {
                    $repository = new TouiteRepository();

                    $idAuteur = (int) $_SESSION['user']['id_user'];

                    $idTouite = $repository->publierTouite(
                        $texte,
                        $idAuteur
                    );

                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    $message = "
                        <p class='success'>
                            Votre touite a bien été publié !
                        </p>
                        <p>
                            Identifiant du touite : $idTouite
                        </p>
                    ";

                } catch (\InvalidArgumentException $e) {

                    $erreur = htmlspecialchars(
                        $e->getMessage(),
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $message = "<p class='error'>$erreur</p>";

                } catch (\Throwable $e) {

                    error_log($e->getMessage());

                    $message = "
                        <p class='error'>
                            Une erreur est survenue lors de la publication.
                        </p>
                    ";
                }
            }
        }

        $token = htmlspecialchars(
            $_SESSION['csrf_token'],
            ENT_QUOTES,
            'UTF-8'
        );

        return <<<HTML
            <h2>Publier un touite</h2>

            $message

            <form method="POST" action="index.php?action=publier">

                <label for="texte">Votre touite :</label>

                <textarea
                    id="texte"
                    name="texte"
                    maxlength="235"
                    rows="4"
                    placeholder="Utilisez # pour ajouter des tags."
                    required
                ></textarea>

                <input
                    type="hidden"
                    name="csrf_token"
                    value="$token"
                >

                <p>235 caractères maximum.</p>

                <button type="submit">
                    Publier
                </button>

            </form>

            <p>
                <a href="index.php">Retour à l'accueil</a>
            </p>
        HTML;
    }
}
