<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;

class SuivreAction extends Action {

    public function execute(): string {

        if (!isset($_SESSION['user_id'])) {
            return "<p>Erreur : vous devez être connecté.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return "<p>Erreur : méthode non autorisée.</p>";
        }

        $tokenSession = $_SESSION['csrf_token'] ?? null;
        $tokenFormulaire = $_POST['csrf_token'] ?? null;

        if (!is_string($tokenSession) ||
            !is_string($tokenFormulaire) ||
            !hash_equals($tokenSession, $tokenFormulaire)) {
            return "<p>Erreur : jeton CSRF invalide.</p>";
        }

        $idAuteurASuivre = filter_input(
            INPUT_POST,
            'id_auteur',
            FILTER_VALIDATE_INT
        );

        if ($idAuteurASuivre === false ||
            $idAuteurASuivre === null ||
            $idAuteurASuivre <= 0) {
            return "<p>Erreur : utilisateur invalide.</p>";
        }

        $idUtilisateurConnecte = (int) $_SESSION['user_id'];

        if ($idUtilisateurConnecte === $idAuteurASuivre) {
            return "<p>Erreur : vous ne pouvez pas vous suivre vous-même.</p>";
        }

        $repository = TouiteurRepository::getInstance();

        if ($repository->estSuivi($idUtilisateurConnecte, $idAuteurASuivre)) {

            $succes = $repository->nePlusSuivreUtilisateur(
                $idUtilisateurConnecte,
                $idAuteurASuivre
            );

            $message = $succes
                ? "Vous ne suivez plus cet utilisateur."
                : "Erreur lors de la suppression du suivi.";

        } else {

            $succes = $repository->suivreUtilisateur(
                $idUtilisateurConnecte,
                $idAuteurASuivre
            );

            $message = $succes
                ? "Vous suivez maintenant cet utilisateur."
                : "Erreur lors de l'ajout du suivi.";
        }

        return "<p>{$message}</p>
                <a href='?action=default'>Retour à l'accueil</a>";
    }
}
