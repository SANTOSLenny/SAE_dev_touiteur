<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\modele\Tag;
use iutnc\touiteur\render\TagRenderer;

class TendancesAction extends Action {

    public function execute(): string {
        if (($_SESSION["user_role"] ?? "") !== "admin") {
            http_response_code(403);
            return "<p>Cette page est réservée aux administrateurs.</p>";
        }

        $repository = TouiteurRepository::getInstance();
        $tendances = $repository->getTendances();

        $html = "<h2>Les tendances (Tags mentionnés)</h2>";

        if (empty($tendances)) {
            return $html . "<p>Aucun tag n'a encore été mentionné.</p>";
        }

        $html .= "<ol>";

        foreach ($tendances as $tendance) {
            $tag = new Tag($tendance["libelle"]);
            $renderer = new TagRenderer($tag);
            $nbMentions = (int) $tendance["nb_mentions"];

            $html .= "<li>" . $renderer->render() . " — " . $nbMentions . " mention(s)</li>";
        }

        return $html . "</ol>";
    }
}