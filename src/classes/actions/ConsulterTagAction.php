<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\modele\Tag;
use iutnc\touiteur\render\TagRenderer;

class ConsulterTagAction extends Action {

    public function execute(): string {
        $tagDemande = htmlspecialchars($_GET['tag'] ?? '');

        if (empty($tagDemande)) {
            return "<h2>Erreur</h2><p>Aucun tag spécifié.</p>";
        }

        $tagObj = new Tag($tagDemande);
        $tagRenderer = new TagRenderer($tagObj);
        $tagHtml = $tagRenderer->render();

        $db = ConnectionFactory::makeConnection();
        $stmt = $db->prepare("
            SELECT t.id, t.texte, t.date, u.pseudo
            FROM touite t
            INNER JOIN touite_tag tt ON t.id = tt.id_touite
            INNER JOIN tag tag ON tt.id_tag = tag.id
            INNER JOIN utilisateur u ON t.id_auteur = u.id
            WHERE tag.libelle = :libelle
            ORDER BY t.date DESC
        ");
        $stmt->execute(['libelle' => $tagDemande]);
        $touites = $stmt->fetchAll();

        $html = "<section class=\"tag-results\">";
        $html .= "<h2>Touites associés au tag {$tagHtml}</h2>";

        if (empty($touites)) {
            $html .= "<p>Aucun touite ne correspond au tag #{$tagDemande}.</p>";
        } else {
            $html .= "<ul class=\"liste-touites\">";
            foreach ($touites as $touite) {
                $auteur = htmlspecialchars($touite['pseudo']);
                $texte = htmlspecialchars($touite['texte']);
                $date = htmlspecialchars($touite['date']);
                $html .= "<li><strong>@{$auteur}</strong> ({$date}) :<br>{$texte}</li>";
            }
            $html .= "</ul>";
        }

        $html .= "<p><a href=\"?action=default\">Retour à l'accueil</a></p>";
        $html .= "</section>";

        return $html;
    }
}