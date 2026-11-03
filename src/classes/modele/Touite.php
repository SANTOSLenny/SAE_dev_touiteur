<?php

namespace iutnc\touiteur\modele;

class Touite {

    protected int $id;
    protected string $texte;
    protected string $auteur;
    protected string $date;

    public function __construct(int $id, string $texte, string $auteur, string $date) {
        if (mb_strlen($texte) > 235) {
            throw new \Exception("Le touite dépasse 235 caractères.");
        }
        $this->id = $id;
        $this->texte = $texte;
        $this->auteur = $auteur;
        $this->date = $date;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTexte(): string {
        return $this->texte;
    }

    public function getAuteur(): string {
        return $this->auteur;
    }

    public function getDate(): string {
        return $this->date;
    }
}