<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\modele\Touite;

class AddTouiteAction extends Action {

    public function execute(): string {

        // 1. Si on arrive sur la page, on affiche le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->renderForm();
        }

        // Récupération des données du formulaire
        $texte = $_POST["texte"] ?? '';
        $auteur = $_POST["auteur"] ?? '';
        $id = time(); // ou count($_SESSION['touites'] ?? []) + 1;
        $date = date('Y-m-d H:i:s'); // Date actuelle

        // si les données sont vides
        if (empty($texte) || empty($auteur)) {
            return "<p style='color:red;'>Erreur : Le texte et l'auteur sont requis.</p>" . $this->renderForm();
        }

        // Vérification de la longueur du texte
        if (mb_strlen($texte) > 235) {
            return "<p style='color:red;'>Erreur : Le touite dépasse 235 caractères.</p>" . $this->renderForm();
        }

        // Création du touite
        try {
            $touite = new Touite($id, $texte, $auteur, $date);
        } catch (\Exception $e) {
            return "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>" . $this->renderForm();
        }

        // Stockage temporaire en session 
        if (!isset($_SESSION['touites'])) {
            $_SESSION['touites'] = [];
        }
        $_SESSION['touites'][] = $touite;

        $html = "<p style='color:green;'>Le touite a été ajouté avec succès !</p>";

        return $html;
    }

    public function renderForm(): string {
        return "";
    }

}