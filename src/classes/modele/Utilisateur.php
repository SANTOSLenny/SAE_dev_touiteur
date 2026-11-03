<?php

namespace iutnc\touiteur\modele;

class Utilisateur {

    protected int $uid;
    protected string $nom;

    public function __construct(int $uid, string $nom) {
        $this->uid = $uid;
        $this->nom = $nom;
    }

    public function getUid(): int {
        return $this->uid;
    }

    public function getNom(): string {
        return $this->nom;
    }
}