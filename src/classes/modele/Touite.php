<?php

namespace iutnc\touiteur\modele;

class Touite {

    protected int $id;
    protected string $texte;
    protected int $uid;
    protected string $date;

    protected ?string $image; 

    public function __construct(int $id, string $texte, int $uid, string $date, ?string $image = null) {
        if (mb_strlen($texte) > 235) {
            throw new \Exception("Le touite dépasse 235 caractères.");
        }
        $this->id = $id;
        $this->texte = $texte;
        $this->uid = $uid;
        $this->date = $date;
        $this->image = $image;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTexte(): string {
        return $this->texte;
    }

    public function getUid(): string {
        return $this->uid;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function getImage(): ?string {
        return $this->image;
    }
}