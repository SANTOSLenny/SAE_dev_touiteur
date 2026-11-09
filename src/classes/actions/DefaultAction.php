<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class DefaultAction extends Action {

    public function execute(): string {

        $repository = TouiteurRepository::getInstance();

        $limite = 5;
        $totalTouites = $repository->countTouites();
        $totalPages = max(1, (int) ceil($totalTouites / $limite));

        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

        if ($page === false || $page === null || $page < 1) {
            $page = 1;
        }

        $page = min($page, $totalPages);


        $offset = ($page - 1) * $limite;

        $touites = $repository->findTouitesPagines($limite, $offset);


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
            
            
        }


        $html .= "<hr>";

        if ($page > 1) {
            $pagePrecedente = $page - 1;
            $html .= "<a href='?action=default&page={$pagePrecedente}'>Précédent</a> ";
        }

        $html .= " Page {$page} sur {$totalPages} ";

        if ($page < $totalPages) {
            $pageSuivante = $page + 1;
            $html .= "<a href='?action=default&page={$pageSuivante}'>Suivant</a>";
        }

        return $html;

    }
}