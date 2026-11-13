<?php
namespace iutnc\touiteur\render;

use iutnc\touiteur\touiteur\Touite;

class TouiteRenderer implements Renderer {
    private Touite $touite;

    public function __construct(Touite $touite) {
        $this->touite = $touite;
    }

    public function render(int $selector): string {
        $auteur = htmlspecialchars($this->touite->auteurNom ?? 'Anonyme', ENT_QUOTES, 'UTF-8');
        $date = date('d/m/Y à H:i', strtotime($this->touite->date));
        $score = (int) $this->touite->score;
        $id = (int) $this->touite->id;

        if ($selector === self::COMPACT) {
            // Version courte pour F1 (tronquée à 100 caractères si trop long)
            $texteBrut = mb_strlen($this->touite->texte) > 100 
                ? mb_substr($this->touite->texte, 0, 100) . '...' 
                : $this->touite->texte;
            $texte = htmlspecialchars($texteBrut, ENT_QUOTES, 'UTF-8');

            return <<<HTML
            <article class="touite">
                <div class="touite-header">
                    <strong>$auteur</strong>
                    <span>$date</span>
                </div>
                <p class="touite-texte">$texte</p>
                <div class="touite-footer">
                    <span>Score : $score</span>
                    <a href="index.php?action=detail&id=$id">Voir le détail</a>
                </div>
            </article>
            HTML;
        }

        // Version longue (déjà prête pour F2 !)
        $texteComplet = htmlspecialchars($this->touite->texte, ENT_QUOTES, 'UTF-8');
        return <<<HTML
        <article class="touite-detail">
            <h2>Touite de $auteur</h2>
            <p class="date">Publié le $date</p>
            <p class="touite-texte">$texteComplet</p>
            <p class="score">Score : $score</p>
            <a href="index.php">Retour à la liste</a>
        </article>
        HTML;
    }
}