<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class MurAction extends Action {

    public function execute(): string {

        if (!isset($_SESSION['user_id'])) {
            return "<p>Erreur : vous devez être connecté pour voir votre mur.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        $repository = TouiteurRepository::getInstance();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $idUtilisateurConnecte = (int) $_SESSION['user_id'];

        $touites = $repository->getMurUtilisateur($idUtilisateurConnecte);

        $html = "<h2>Votre Mur d'actualite</h2>";

        if (empty($touites)) {
            $html .= "<p>Votre mur est vide. Suivez des utilisateurs pour voir leurs touites ici</p>";
            return $html;
        }

        foreach ($touites as $ligne) {

            $idAuteur = (int) $ligne['id_auteur'];

            $affichageAuteur = htmlspecialchars(
                $ligne['auteur'],
                ENT_QUOTES,
                'UTF-8'
            );

            $renderer = new TouiteRenderer(
                $ligne['touite'],
                $affichageAuteur
            );

            $html .= $renderer->render(1);

            if ($idUtilisateurConnecte !== $idAuteur) {

                $dejaSuivi = $repository->estSuivi(
                    $idUtilisateurConnecte,
                    $idAuteur
                );

                $libelleBouton = $dejaSuivi ? 'Ne plus suivre' : 'Suivre';

                $csrfToken = htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                );

                $html .= "<form action='?action=suivre' method='POST'>
                    <input type='hidden' name='id_auteur' value='{$idAuteur}'>
                    <input type='hidden' name='csrf_token' value='{$csrfToken}'>
                    <button type='submit'>{$libelleBouton}</button>
                </form>";
            }
        }

        $html .= "<hr>";

        return $html;
    }
}
