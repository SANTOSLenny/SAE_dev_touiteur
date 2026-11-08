<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class SuivreAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['user_id'])) {
            return "<p>Erreur : Vous devez être connecté pour suivre un utilisateur.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        if (isset($_GET['id_auteur'])) {
            $idUtilisateurConnecte = (int) $_SESSION['user_id'];
            $idAuteurASuivre = (int) $_GET['id_auteur'];

            if ($idUtilisateurConnecte === $idAuteurASuivre) {
                return "<p>Erreur : vous ne pouvez pas vous suivre vous meme</p>
                        <a href='?action=default'>Retour à l'accueil</a>";
            }

            $db = ConnectionFactory::makeConnection();

            $stmt = $db->prepare("SELECT * FROM suivi_utilisateur WHERE id_suiveur = :suiveur AND id_suivi = :suivi");
            $stmt->execute([
                'suiveur' => $idUtilisateurConnecte,
                'suivi' => $idAuteurASuivre
            ]);

            if (!$stmt->fetch()) {
                $insert = $db->prepare("INSERT INTO suivi_utilisateur (id_suiveur, id_suivi) VALUES (:suiveur, :suivi)");
                $insert->execute([
                    'suiveur' => $idUtilisateurConnecte,
                    'suivi' => $idAuteurASuivre
                ]);
                return "<p>succes : vous suivez l'auteur (ID: $idAuteurASuivre).</p>
                        <a href='?action=default'>Retour à l'accueil</a>";
            } else {
                $delete = $db->prepare("DELETE FROM suivi_utilisateur WHERE id_suiveur = :suiveur AND id_suivi = :suivi");
                $delete->execute([
                    'suiveur' => $idUtilisateurConnecte,
                    'suivi' => $idAuteurASuivre
                ]);
                return "<p>Succes : vous ne suivez plus cet auteur (ID: $idAuteurASuivre).</p>
                        <a href='?action=default'>Retour à l'accueil</a>";
            }
        }

        return "<p>Erreur : L'identifiant de l'auteur à suivre est manquant.</p>";
    }
}