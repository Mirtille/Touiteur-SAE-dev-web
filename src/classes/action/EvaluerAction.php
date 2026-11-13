<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class EvaluerAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user']['id_user'])) {
            return "<h2>Accès refusé</h2>
                    <p>Vous devez être connecté pour évaluer un touite.</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return "<h2>Erreur</h2><p>Méthode non autorisée.</p>";
        }

        $idUser = (int) $_SESSION['user']['id_user'];
        $idTouite = filter_var($_POST['id_touite'] ?? null, FILTER_VALIDATE_INT);
        $valeur = filter_var($_POST['valeur'] ?? null, FILTER_VALIDATE_INT);
        $token = $_POST['csrf_token'] ?? '';

        if (
            !isset($_SESSION['csrf_token']) ||
            !is_string($token) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            return "<h2>Erreur de sécurité</h2>
                    <p>Votre demande n'a pas pu être vérifiée.</p>
                    <a href='index.php'>Accueil</a>";
        }

        if (!$idTouite || $idTouite <= 0 || !in_array($valeur, [-1, 1], true)) {
            return "<h2>Erreur</h2><p>Évaluation invalide.</p>";
        }

        $repository = new TouiteRepository();
        $succes = $repository->evaluerTouite(
            $idUser,
            $idTouite,
            $valeur
        );

        if (!$succes) {
            return "<h2>Évaluation impossible</h2>
                    <p>Vous avez déjà évalué ce touite.</p>
                    <a href='index.php?action=detail&id=$idTouite'>Retour au touite</a>";
        }

        return "<h2>Merci pour votre évaluation !</h2>
                <p>Votre vote a bien été enregistré.</p>
                <a href='index.php?action=detail&id=$idTouite'>Retour au touite</a>";
    }
}