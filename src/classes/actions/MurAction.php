<?php
namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class MurAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['user_id'])) {
            return "<p>erreur : Vous devez être connecte pour voir votre mur.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        $repository = TouiteurRepository::getInstance();
        $idUtilisateurConnecte = (int) $_SESSION['user_id'];
        
        $touites = $repository->getMurUtilisateur($idUtilisateurConnecte);

        $html = "<h2>Votre Mur d'actualite</h2>";

        if (empty($touites)) {
            $html .= "<p>Votre mur est vide. Suivez des utilisateurs pour voir leurs touites ici</p>";
            return $html;
        }

        foreach ($touites as $ligne) {
            $idAuteur = (int) $ligne['id_auteur'];

            $renderer = new TouiteRenderer($ligne['touite'], $ligne['auteur']);
            $html .= $renderer->render(1);

            if ($idUtilisateurConnecte !== $idAuteur) {
                $dejaSuivi = $repository->estSuivi($idUtilisateurConnecte, $idAuteur);
                $libelleBouton = $dejaSuivi ? 'Ne plus suivre' : 'Suivre';

                $html .= "<p><a href='?action=suivre&id_auteur={$idAuteur}'>{$libelleBouton}</a></p>";
            }
        }
        
        $html .= "<hr>";
        return $html;
    }
}

?>