<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\modele\Touite;

class TouiteRenderer {

    private Touite $touite;
    private String $auteur;

    public function __construct(Touite $touite, String $auteur){
        $this->touite = $touite;
        $this->auteur = $auteur;
    }

    public function render(int $selecteur): String {

    switch($selecteur){
        default:
            $html = '<div class="touite">';
            $html .= "<h3>" . $this->auteur . "</h3>";
            $html .= "<p>" . $this->touite->getTexte() . "</p>";
            $html .= "</div>";

            return $html;
        }
    }
}