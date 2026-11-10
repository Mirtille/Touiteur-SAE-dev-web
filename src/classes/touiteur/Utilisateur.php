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

    public function getId() : ?int {
        return $this->id;
    }

    public function getNom() : string {
        return $this->nom;
    }

    public function getPrenom() : string {
        return $this->prenom;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function getRole() : string {
        return $this->role;
    }

    public function getNbTouites() : int {
        return $this->nbTouites;
    }

    public function getMotDePasse() : string {
        return $this->motDePasse;
    }

    public function estAdmin() : bool {
        return $this->role === 'admin';
    }
}
