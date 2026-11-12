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

                $uid = $this->touite->getUid();
                $html = '<div class="touite">';
                $html .= '<h3><a href="?action=consulter-utilisateur&uid=' . $uid . '">' . $this->auteur . '</a></h3>';
                
                $texteBrut = htmlspecialchars(
                    $this->touite->getTexte(),
                    ENT_QUOTES,
                    'UTF-8'
                );
                $texteFormate = preg_replace('/#(\w+)/', '<a href="?action=consulter-tag&tag=$1">#$1</a>', $texteBrut);
                
                $html .= "<p>" . $texteFormate . "</p>";
                if (isset($_SESSION["user_id"])) {
                    $idTouite = $this->touite->getId();

                    $html .= '<form action="?action=voter" method="POST">';
                    $html .= '<input type="hidden" name="id_touite" value="'. $idTouite . '">';
                    $html .= '<button type="submit" name="valeur" value="1">Like +1</button>';
                    $html .= '<button type="submit" name="valeur" value="-1">Dislike -1</button>';
                    $html .= '</form>';
                }
                $html .= "</div>";
                
                return $html;
        }
    }
}