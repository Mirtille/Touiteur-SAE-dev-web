<?php

namespace iutnc\touiteur\touiteur;

class Tag {
    private ?int $id;
    private string $libelle;
    private ?string $description;

    public function __construct(?int $id, string $libelle, ?string $description = null){
        $this->id = $id;
        $this->libelle = $libelle;
        $this->description = $description;
    }

    public function __get(string $name): mixed {
        return $this->$name ?? null;
    }
}