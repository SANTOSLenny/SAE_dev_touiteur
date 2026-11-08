<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class DefaultAction extends Action {

    public function execute(): string {
        $repository = TouiteurRepository::getInstance();
        $touites = $repository->findAllTouites();

        $html = "<h2>Les derniers touites</h2>";

        foreach ($touites as $ligne) {
            $idAuteur = $ligne["id_auteur"];
            $affichageAuteur = $ligne["auteur"];

            if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] !== $idAuteur) {
                $dejaSuivi = isset($_SESSION['user_id']) && $repository->estSuivi((int) $_SESSION['user_id'], $idAuteur);
                $libelleBouton = $dejaSuivi ? "[Ne plus suivre]" : "[Suivre]";
                
                $affichageAuteur = "<a href='?action=suivre&id_auteur={$idAuteur}'>{$libelleBouton}</a> " . $ligne["auteur"];
            }
            
            $renderer = new TouiteRenderer(
                $ligne["touite"],
                $affichageAuteur
            );

            $html .= $renderer->render(1);
            $idAuteur = $ligne["id_auteur"];
            
            
        }
        $html .= "<hr>";
        return $html;
    }
}