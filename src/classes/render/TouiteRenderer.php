<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\modele\Touite;

class TouiteRenderer {

    private Touite $touite;

    public function __construct(Touite $touite){
        $this->touite = $touite;
    }

    public function render(int $selecteur): String {

    switch($selecteur){
        default:
            $html = '<div class="touite">';
            $html .= "<h3>" . $this->touite->getAuteur() . "</h3>";
            $html .= "<p>" . $this->touite->getTexte() . "</p>";
            $html .= "</div>";

            return $html;
        }
    }
}