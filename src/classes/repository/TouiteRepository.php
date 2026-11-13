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

    public function abonnerTag(int $idUser, string $libelleTag): bool {
        $libellePropre = mb_strtolower(ltrim(trim($libelleTag), '#'), 'UTF-8');
        if ($libellePropre === '') {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT id_tag FROM Tag WHERE libelle_tag = :libelle");
        $stmt->execute(['libelle' => $libellePropre]);
        $idTag = $stmt->fetchColumn();

        if (!$idTag) {
            $insertTag = $this->pdo->prepare("INSERT INTO Tag (libelle_tag) VALUES (:libelle)");
            $insertTag->execute(['libelle' => $libellePropre]);
            $idTag = (int) $this->pdo->lastInsertId();
        }

        $insertAbonnement = $this->pdo->prepare("INSERT IGNORE INTO UserTag (id_user, id_tag) VALUES (:idUser, :idTag)");
        return $insertAbonnement->execute([
            'idUser' => $idUser,
            'idTag'  => $idTag
        ]);
    }

    public function getInfluenceurs(): array {
        $sql = "SELECT u.id_user, u.nom_user, u.prenom_user, COUNT(us.id_suiveur) AS nb_suiveurs
                FROM Utilisateur u
                LEFT JOIN UserSuivi us ON u.id_user = us.id_suivi
                GROUP BY u.id_user, u.nom_user, u.prenom_user
                ORDER BY nb_suiveurs ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function extraireTags(string $texte): array {
        preg_match_all(
            '/#([a-zA-Z0-9_àâäéèêëïîôöùûüçÀÂÄÉÈÊËÏÎÔÖÙÛÜÇ-]+)/u',
            $texte,
            $matches
        );

        $tags = [];

        foreach ($matches[1] as $tag) {
            $tag = mb_strtolower($tag, 'UTF-8');

            if (mb_strlen($tag, 'UTF-8') > 50) {
                throw new \InvalidArgumentException("Un tag ne peut pas dépasser 50 caractères.");
            }
            $tags[] = $tag;
        }
        return array_unique($tags);
    }

    public function publierTouite(string $texte, int $idAuteur): int {

        $texte = trim($texte);

        if ($texte === '') {
            throw new \InvalidArgumentException(
                "Le touite ne doit pas être vide."
            );
        }

        if (mb_strlen($texte, 'UTF-8') > 235) {
            throw new \InvalidArgumentException(
                "Le touite ne peut pas dépasser 235 caractères."
            );
        }

        if ($idAuteur <= 0) {
            throw new \InvalidArgumentException(
                "Auteur invalide."
            );
        }

        $tags = $this->extraireTags($texte);

        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO Touite (texte_touite, id_auteur)
                    VALUES (:texte, :idAuteur)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'texte' => $texte,
                'idAuteur' => $idAuteur
            ]);

            $idTouite = (int) $this->pdo->lastInsertId();


            foreach ($tags as $libelle) {
                $sqlTag = "INSERT INTO Tag (libelle_tag)
                        VALUES (:libelle)
                        ON DUPLICATE KEY UPDATE
                        id_tag = LAST_INSERT_ID(id_tag)";

                $stmtTag = $this->pdo->prepare($sqlTag);
                $stmtTag->execute(['libelle' => $libelle]);

                $idTag = (int) $this->pdo->lastInsertId();
                $sqlAssociation = "
                INSERT IGNORE INTO Touite2Tag (id_touite, id_tag) VALUES (:idTouite, :idTag)";
                $stmtAssociation = $this->pdo->prepare($sqlAssociation);
                $stmtAssociation->execute([
                    'idTouite' => $idTouite,
                    'idTag' => $idTag
                ]);
            }
            $this->pdo->commit();
            return $idTouite;

        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function getMurUtilisateur(int $idUser): array {
     $sql = "SELECT DISTINCT t.*, u.nom_user, u.prenom_user
             FROM Touite t
             JOIN Utilisateur u ON t.id_auteur = u.id_user
             LEFT JOIN UserSuivi us ON t.id_auteur = us.id_suivi AND us.id_suiveur = :idUser1
             LEFT JOIN Touite2Tag t2t ON t.id_touite = t2t.id_touite
             LEFT JOIN UserTag ut ON t2t.id_tag = ut.id_tag AND ut.id_user = :idUser2
             WHERE us.id_suiveur IS NOT NULL OR ut.id_user IS NOT NULL
             ORDER BY t.date_publi DESC";

     $stmt = $this->pdo->prepare($sql);
     $stmt->execute([
         'idUser1' => $idUser,
         'idUser2' => $idUser
     ]);

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

}