<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;

class AbonnerTagAction extends Action {

    public function execute(): string {


        if (!isset($_SESSION['user_id'])) {
            return "<p>Vous devez être connecté pour vous abonner à un tag.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return "<p>Erreur : méthode non autorisée.</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }

        $tokenSession = $_SESSION['csrf_token'] ?? null;
        $tokenFormulaire = $_POST['csrf_token'] ?? null;

        if (!is_string($tokenSession) ||
            !is_string($tokenFormulaire) ||
            !hash_equals($tokenSession, $tokenFormulaire)) {

            return "<p>Erreur de sécurité : jeton CSRF invalide.</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }


        $idTag = filter_input(INPUT_POST, 'id_tag', FILTER_VALIDATE_INT);

        if ($idTag === false || $idTag === null || $idTag < 1) {
            return "<p>Erreur : identifiant du tag invalide.</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }

        $idUtilisateur = (int) $_SESSION['user_id'];

        $repository = TouiteurRepository::getInstance();


        if (!$repository->tagExiste($idTag)) {
            return "<p>Erreur : ce tag n'existe pas.</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }


        if ($repository->estAbonneTag($idUtilisateur, $idTag)) {
            return "<p>Vous êtes déjà abonné à ce tag.</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }

        $succes = $repository->abonnerTag($idUtilisateur, $idTag);

        if ($succes) {
            return "<p>Vous êtes maintenant abonné à ce tag !</p>
                    <a href='?action=default'>Retour à l'accueil</a>";
        }

        return "<p>Erreur lors de l'abonnement.</p>
                <a href='?action=default'>Retour à l'accueil</a>";
    }
}
