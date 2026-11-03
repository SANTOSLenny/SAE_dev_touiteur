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
                
                $texteBrut = $this->touite->getTexte();
                $texteFormate = preg_replace('/#(\w+)/', '<a href="?action=consulter-tag&tag=$1">#$1</a>', $texteBrut);
                
                $html .= "<p>" . $texteFormate . "</p>";
                $html .= "</div>";
                
                return $html;
        }
    }
}