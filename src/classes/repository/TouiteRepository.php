<?php
namespace iutnc\touiteur\repository;

use iutnc\touiteur\touiteur\Touite;
use PDO;

class TouiteRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connexion::getConnection();
    }

    public function getDerniersTouites(): array {
        $sql = "SELECT t.*, u.nom_user, u.prenom_user 
                FROM Touite t
                JOIN Utilisateur u ON t.id_auteur = u.id_user
                ORDER BY t.date_publi DESC";
        $stmt = $this->pdo->query($sql);

        $touites = [];
        while ($row = $stmt->fetch()) {
            $nomComplet = $row['prenom_user'] . ' ' . $row['nom_user'];
            $touites[] = new Touite(
                $row['id_touite'],
                $row['texte_touite'],
                $row['date_publi'],
                $row['score_touite'],
                $row['id_auteur'],
                $nomComplet
            );
        }
        return $touites;
    }
}