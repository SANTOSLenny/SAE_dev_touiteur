<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;

class VoterAction extends Action {

    public function execute(): string {

        if (!isset($_SESSION["user_id"])) {
            return '<p>Connecte-toi pour voter.</p><a href="?action=connexion">Connexion</a>';
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p>Le vote doit être envoyé depuis le formulaire.</p>";
        }

        $uid = (int) $_SESSION["user_id"];
        $idTouite = (int) ($_POST["id_touite"] ?? 0);
        $valeur = (int) ($_POST["valeur"] ?? 0);

        if ($idTouite <= 0 || ($valeur !== 1 && $valeur !== -1)) {
            return "<p>Vote non valide.</p>";
        }

        $repository = TouiteurRepository::getInstance();
        $voteEnregistre = $repository->voter($uid, $idTouite, $valeur);

        if ($voteEnregistre) {
            $html = "<p>Ton vote a été enregistré.</p>";
        } else {
            $html = "<p>Tu as déjà voté pour ce touite.</p>";
        }

        $html .= '<a href="?action=default">Retour à l’accueil</a>';

        return $html;
    }
}