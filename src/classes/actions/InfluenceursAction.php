<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;

class InfluenceursAction extends Action {

    public function execute(): string {
        if (($_SESSION["user_role"] ?? "") !== "admin") {
            http_response_code(403);
            return "<p>Cette page est réservée aux administrateurs.</p>";
        }

        $repository = TouiteurRepository::getInstance();
        $influenceurs = $repository->getTopInfluenceurs();

        $html = "<h2>Top 5 des influenceurs</h2>";

        if (empty($influenceurs)) {
            return $html . "<p>Aucun compte avec des abonnés</p>";
        }

        $html .= "<ol>";

        foreach ($influenceurs as $influenceur) {
            $id = (int) $influenceur["id"];
            $pseudo = $influenceur["pseudo"];
            $pseudo = str_replace(["&", "<", ">"], ["&amp;", "&lt;", "&gt;"], $pseudo);
            $nbAbonnes = (int) $influenceur["nb_abonnes"];

            $html .= '<li><a href="?action=consulter-utilisateur&amp;uid=' . $id . '">' . $pseudo . '</a> — ' . $nbAbonnes . ' abonné(s)</li>';
        }

        return $html . "</ol>";
    }
}