<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;
use Override;

class ConsulterUtilisateurAction extends Action {

    #[Override]
    public function execute(): string
    {
        $uid = (int) ($_GET["uid"] ?? 0);

        if ($uid <= 0){
            return "<p>Utilisateur invalide !</p>";
        }

        $repo = TouiteurRepository::getInstance();
        $touites = $repo->getTouitesByUser($uid);


        $html = "<h2>Touites de l'utilisateur</h2>";

        if (empty($touites)){
            $html .= "<p>Aucun touite de cet utilisateur</p>";
        } else {
            foreach ($touites as $ligne){
                $renderer = new TouiteRenderer($ligne["touite"], $ligne["auteur"]);

                $html .= $renderer->render(1);
            }
        }

        $html .= '<p><a href="?action=default">Retour à l’accueil</a></p>';

        return $html;
    }
}