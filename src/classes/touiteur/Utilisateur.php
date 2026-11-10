<?php

namespace iutnc\touiteur\touiteur;

class Utilisateur {
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $motDePasse;
    private string $role;
    private int $nbTouites;

    public function __construct(
        ?int $id,
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        string $role = 'standard',
        int $nbTouites = 0
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
        $this->nbTouites = $nbTouites;
    }

public function __get(string $name): mixed {
        return $this->$name ?? null;
    }

    public function getNomComplet(): string {
        return "{$this->prenom} {$this->nom}";
    }
}
