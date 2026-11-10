<?php
namespace iutnc\touiteur\touiteur;

class Touite {
    private int $id;
    private string $texte;
    private string $date;
    private int $score;
    private int $idAuteur;
    private ?string $auteurNom = null;

    public function __construct(int $id, string $texte, string $date, int $score, int $idAuteur, ?string $auteurNom = null) {
        $this->id = $id;
        $this->texte = $texte;
        $this->date = $date;
        $this->score = $score;
        $this->idAuteur = $idAuteur;
        $this->auteurNom = $auteurNom;
    }

    public function __get(string $name): mixed {
        return $this->$name ?? null;
    }
}