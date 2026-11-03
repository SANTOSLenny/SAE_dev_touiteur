<?php

namespace iutnc\touiteur\actions;

class SuivreAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user_id'])) {
            return "<p>Erreur : Vous devez être connecté pour suivre un utilisateur.</p>";
        }

        if (isset($_GET['id_auteur'])) {
            $idUtilisateurConnecte = (int) $_SESSION['user_id'];
            $idAuteurASuivre = (int) $_GET['id_auteur'];

            if ($idUtilisateurConnecte === $idAuteurASuivre) {
                return "<p>Erreur : Vous ne pouvez pas vous suivre vous-même.</p>";
            }

            if (!isset($_SESSION['abonnements'][$idUtilisateurConnecte])) {
                $_SESSION['abonnements'][$idUtilisateurConnecte] = [];
            }

            if (!in_array($idAuteurASuivre, $_SESSION['abonnements'][$idUtilisateurConnecte])) {
                $_SESSION['abonnements'][$idUtilisateurConnecte][] = $idAuteurASuivre;
                return "<p>Succès : Vous suivez désormais l'auteur (ID: $idAuteurASuivre).</p>";
            } else {
                return "<p>Information : Vous suivez déjà cet auteur.</p>";
            }
        }

        return "<p>Erreur : L'identifiant de l'auteur à suivre est manquant.</p>";
    }
}