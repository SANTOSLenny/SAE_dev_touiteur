<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\modele\Tag;

class TagRenderer {
    private Tag $tag;

    public function __construct(Tag $tag) {
        $this->tag = $tag;
    }

    public function render(): string {
        $libelle = htmlspecialchars($this->tag->getLibelle());
        $url = "?action=consulter-tag&tag=" . urlencode($libelle);

        return "<a class=\"tag\" href=\"{$url}\">#{$libelle}</a>";
    }
}