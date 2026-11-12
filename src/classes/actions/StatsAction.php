<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;

class StatsAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION["user_id"])) {
            return '<p>Connecte-toi pour voir tes statistiques.</p>' . '<a href="?action=connexion">Connexion</a>';
        }

        $uid = (int) $_SESSION["user_id"];
        $repository = TouiteurRepository::getInstance();

        $abonnes = $repository->getAbonnes($uid);
        $moyenne = $repository->getScoreMoyen($uid);

        $html = "<h2>Mes statistiques</h2>";

        if ($moyenne === null) {
            $html .= "<p>Tu n’as encore publié aucun touite.</p>";
        } else {
            $html .= "<p>Score moyen de mes touites : " . number_format($moyenne, 2, ",", " ") . "</p>";
        }

        $html .= "<h3>Mes abonnés (" . count($abonnes) . ")</h3>";

        if (empty($abonnes)) {
            return $html . "<p>Tu n’as encore aucun abonné.</p>";
        }

        $html .= "<ul>";

        foreach ($abonnes as $pseudo) {
            $pseudo = str_replace(["&", "<", ">"], ["&amp;", "&lt;", "&gt;"], $pseudo);
            $html .= "<li>" . $pseudo . "</li>";
        }

        return $html . "</ul>";
    }
}