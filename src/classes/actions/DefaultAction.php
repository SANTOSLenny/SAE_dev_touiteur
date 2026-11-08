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
            $renderer = new TouiteRenderer(
                $ligne["touite"],
                $ligne["auteur"]
            );

            $html .= $renderer->render(1);
            $idAuteur = $ligne["id_auteur"];
            
            if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $idAuteur) {
                    $html .= "<p><a href='?action=suivre&id_auteur={$idAuteur}'>[Suivre {$ligne['auteur']}]</a></p>";
            }
        }
        $html .= "<hr>";
        return $html;
    }
}