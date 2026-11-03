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
        }

        return $html;
    }
}