<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\modele\Tag;
use iutnc\touiteur\render\TagRenderer;
use iutnc\touiteur\repository\TouiteurRepository;

class ConsulterTagAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $tagDemande = trim($_GET['tag'] ?? '');

        if (empty($tagDemande)) {
            return "<h2>Erreur</h2><p>Aucun tag spécifié.</p>";
        }

        $tagSecurise = htmlspecialchars($tagDemande, ENT_QUOTES, 'UTF-8');
        $tagObj = new Tag($tagDemande);
        $tagRenderer = new TagRenderer($tagObj);
        $tagHtml = $tagRenderer->render();

        $repository = TouiteurRepository::getInstance();
        $idTag = $repository->getIdTagParLibelle($tagDemande);

        $touites = $repository->getTouitesByTag($tagDemande);

        $html = "<section class=\"tag-results\">";
        $html .= "<h2>Touites associés au tag {$tagHtml}</h2>";


        if (isset($_SESSION['user_id']) && $idTag !== false) {

            $repository = TouiteurRepository::getInstance();

            $idUtilisateur = (int) $_SESSION['user_id'];

            $dejaAbonne = $repository->estAbonneTag(
                $idUtilisateur,
                (int) $idTag
            );

            if ($dejaAbonne) {

                $html .= "<p>Vous êtes déjà abonné à ce tag.</p>";

            } else {
                $html .= '<form method="POST" action="?action=abonner-tag">';

                $html .= '<input type="hidden" name="id_tag" value="'
                    . (int) $idTag . '">';

                $csrfToken = htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                );

                $html .= '<input type="hidden" name="csrf_token" value="'
                    . $csrfToken . '">';

                $html .= '<button type="submit">S\'abonner à ce tag</button>';

                $html .= '</form>';

            }
        }

        if (empty($touites)) {
            $html .= "<p>Aucun touite ne correspond au tag #{$tagSecurise}.</p>";
        } else {
            $html .= "<ul class=\"liste-touites\">";
            foreach ($touites as $element) {
                $touite = $element['touite'];
                $auteur = htmlspecialchars($element['auteur'], ENT_QUOTES, 'UTF-8');
                $texte = htmlspecialchars($touite->getTexte(), ENT_QUOTES, 'UTF-8');
                $date = htmlspecialchars($touite->getDate(), ENT_QUOTES, 'UTF-8');
                $html .= "<li><strong>@{$auteur}</strong> ({$date}) :<br>{$texte}</li>";
            }
            $html .= "</ul>";
        }

        $html .= "<p><a href=\"?action=default\">Retour à l'accueil</a></p>";
        $html .= "</section>";

        return $html;
    }
}