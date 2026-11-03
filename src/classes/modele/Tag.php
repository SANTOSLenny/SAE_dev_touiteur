<?php

namespace iutnc\touiteur\modele;

class Tag {

    protected string $libelle;

    public function __construct(string $libelle) {
        $this->libelle = trim(ltrim($libelle, '#'));
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    public function __toString(): string {
        return '#' . $this->libelle;
    }

}