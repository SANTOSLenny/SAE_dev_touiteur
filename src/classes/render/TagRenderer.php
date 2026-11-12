<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\modele\Tag;

class TagRenderer {
    private Tag $tag;

    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }

    public function render(): string {
        $libelle = $this->tag->getLibelle();

        $libelleSecurise = htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8');

        $url = "?action=consulter-tag&tag=" . urlencode($libelle);
        $urlSecurisee = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return "<a class=\"tag\" href=\"{$urlSecurisee}\">#{$libelleSecurise}</a>";
    }
}