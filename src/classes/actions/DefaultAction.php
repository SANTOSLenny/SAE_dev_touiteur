<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class DefaultAction extends Action {

    public function execute(): string {

        $repository = TouiteurRepository::getInstance();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

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
            $idAuteur = (int) $ligne["id_auteur"];
            $libelleBouton = "";
            $affichageAuteur = htmlspecialchars(
                $ligne["auteur"],
                ENT_QUOTES,
                'UTF-8'
            );

            if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] !== $idAuteur) {
                $dejaSuivi = isset($_SESSION['user_id']) && $repository->estSuivi((int) $_SESSION['user_id'], $idAuteur);
                $libelleBouton = $dejaSuivi ? "[Ne plus suivre]" : "[Suivre]";
            }
            
            $renderer = new TouiteRenderer(
                $ligne["touite"],
                $affichageAuteur
            );

            $html .= $renderer->render(1);

            if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] !== (int) $idAuteur) {
                $csrfToken = htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                );

                $html .= "<form action='?action=suivre' method='POST'>
                    <input type='hidden' name='id_auteur' value='{$idAuteur}'>
                    <input type='hidden' name='csrf_token' value='{$csrfToken}'>
                    <button type='submit'>{$libelleBouton}</button>
                </form>";

            }   
                        
            
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