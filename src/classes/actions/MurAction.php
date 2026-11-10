<?php
namespace iutnc\touiteur\actions;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteRenderer;

class MurAction extends Action {
    public function execute(): string {
        //verification que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            return "<p>erreur : Vous devez être connecte pour voir votre mur.</p>
                    <a href='?action=connexion'>Se connecter</a>";
        }

        $repository = TouiteurRepository::getInstance();
        $idUtilisateurConnecte = (int) $_SESSION['user_id'];
        
        $touites = $repository->getMurUtilisateur($idUtilisateurConnecte);

        $html = "<h2>Votre Mur d'actualite</h2>";

        //Si le mur est vide on recommande de suivre d'autre utilisateur
        if (empty($touites)) {
            $html .= "<p>Votre mur est vide. Suivez des utilisateurs pour voir leurs touites ici</p>";
            return $html;
        }

        //On affiche les touites des utilisateur que l'utilisateur suit
        foreach ($touites as $ligne) {
            $idAuteur = (int) $ligne["id_auteur"];
            
            if ($idUtilisateurConnecte !== $idAuteur) {
                $dejaSuivi = $repository->estSuivi($idUtilisateurConnecte, $idAuteur);
                $libelleBouton = $dejaSuivi ? "[Ne plus suivre]" : "[Suivre]";
                $affichageAuteur = "<a href='?action=suivre&id_auteur={$idAuteur}'>{$libelleBouton}</a> " . $ligne["auteur"];
            } else {
                $affichageAuteur = $ligne["auteur"];
            }

            $renderer = new TouiteRenderer(
                $ligne["touite"],
                $affichageAuteur
            );

            $html .= $renderer->render(1);
        }




        //Suite on affiche les tag que l'util suit...............
        
        $html .= "<hr>";
        return $html;
    }
}

?>