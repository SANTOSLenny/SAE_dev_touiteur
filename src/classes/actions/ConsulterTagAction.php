<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\db\ConnectionFactory;
use iutnc\touiteur\modele\Tag;
use iutnc\touiteur\render\TagRenderer;
use iutnc\touiteur\repository\TouiteurRepository;

class ConsulterTagAction extends Action {

    public function execute(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $tagDemande = htmlspecialchars($_GET['tag'] ?? '');

        if (empty($tagDemande)) {
            return "<h2>Erreur</h2><p>Aucun tag spécifié.</p>";
        }

        $tagObj = new Tag($tagDemande);
        $tagRenderer = new TagRenderer($tagObj);
        $tagHtml = $tagRenderer->render();

        $db = TouiteurRepository::getInstance()->getPdo();

        $stmtTag = $db->prepare(
            "SELECT id FROM tag WHERE libelle = :libelle"
        );

        $stmtTag->execute([
            'libelle' => $tagDemande
        ]);

        $idTag = $stmtTag->fetchColumn();

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


        if (isset($_SESSION['user_id']) && $idTag !== false) {

            $repository = TouiteurRepository::getInstance();

            $idUtilisateur = (int) $_SESSION['user_id'];

            // Vérifier si l'utilisateur est déjà abonné
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