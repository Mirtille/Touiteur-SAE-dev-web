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

    public function getTouiteById(int $id): ?Touite {
        $sql = "SELECT t.*, u.nom_user, u.prenom_user 
                FROM Touite t
                JOIN Utilisateur u ON t.id_auteur = u.id_user
                WHERE t.id_touite = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $nomComplet = $row['prenom_user'] . ' ' . $row['nom_user'];
        return new Touite(
            (int) $row['id_touite'],
            $row['texte_touite'],
            $row['date_publi'],
            (int) $row['score_touite'],
            (int) $row['id_auteur'],
            $nomComplet
        );
    }

    public function getTouitesParAuteur(int $idAuteur): array {
        $sql = "SELECT t.*, u.nom_user, u.prenom_user 
                FROM Touite t
                JOIN Utilisateur u ON t.id_auteur = u.id_user
                WHERE t.id_auteur = :idAuteur
                ORDER BY t.date_publi DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idAuteur' => $idAuteur]);

        $touites = [];
        while ($row = $stmt->fetch()) {
            $nomComplet = $row['prenom_user'] . ' ' . $row['nom_user'];
            $touites[] = new Touite(
                (int) $row['id_touite'],
                $row['texte_touite'],
                $row['date_publi'],
                (int) $row['score_touite'],
                (int) $row['id_auteur'],
                $nomComplet
            );
        }
        return $touites;
    }

    public function getTouitesParTag(string $libelleTag): array {
        $sql = "SELECT t.*, u.nom_user, u.prenom_user 
                FROM Touite t
                JOIN Utilisateur u ON t.id_auteur = u.id_user
                JOIN Touite2Tag t2t ON t.id_touite = t2t.id_touite
                JOIN Tag tag ON t2t.id_tag = tag.id_tag
                WHERE tag.libelle_tag = :libelleTag
                ORDER BY t.date_publi DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['libelleTag' => $libelleTag]);

        $touites = [];
        while ($row = $stmt->fetch()) {
            $nomComplet = $row['prenom_user'] . ' ' . $row['nom_user'];
            $touites[] = new Touite(
                (int) $row['id_touite'],
                $row['texte_touite'],
                $row['date_publi'],
                (int) $row['score_touite'],
                (int) $row['id_auteur'],
                $nomComplet
            );
        }
        return $touites;
    }

    public function estSuivi(int $idSuiveur, int $idSuivi): bool {
        $sql = "SELECT COUNT(*) FROM UserSuivi WHERE id_suiveur = :suiveur AND id_suivi = :suivi";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'suiveur' => $idSuiveur,
            'suivi'   => $idSuivi
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function suivreUtilisateur(int $idSuiveur, int $idSuivi): bool {
        if ($idSuiveur === $idSuivi) {
            return false;
        }
        $sql = "INSERT IGNORE INTO UserSuivi (id_suiveur, id_suivi) VALUES (:suiveur, :suivi)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'suiveur' => $idSuiveur,
            'suivi'   => $idSuivi
        ]);
    }
}