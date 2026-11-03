<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\modele\Touite;

class AddTouiteAction extends Action {

    public function execute(): string {
        // Vérification si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            return "<p style='color:red;'><strong>Erreur : vous devez être connecté pour publier un touite.</strong></p>";
        }

        // Si on arrive sur la page, on affiche le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->renderPage();
        }

        // Récupération des données du formulaire
        $auteur = $_SESSION['user']->getNom();
        $texte = $_POST["texte"] ?? '';
        $id = time(); // ou count($_SESSION['touites'] ?? []) + 1;
        $date = date('Y-m-d H:i:s'); // Date actuelle

        // Si les données sont vides
        if (empty($texte) || empty($auteur)) {
            return "<p style='color:red;'><strong>Erreur : Le texte et l'auteur sont requis.</strong></p>" . $this->renderPage();
        }

        // Vérification de la longueur du texte
        if (mb_strlen($texte) > 235) {
            return "<p style='color:red;'><strong>Erreur : Le touite dépasse 235 caractères.</strong></p>" . $this->renderPage();
        }

        // Création du touite
        try {
            $touite = new Touite($id, $texte, $auteur, $date);
        } catch (\Exception $e) {
            return "<p style='color:red;'><strong>Erreur : " . $e->getMessage() . "</strong></p>" . $this->renderPage();
        }

        // Stockage temporaire en session 
        if (!isset($_SESSION['touites'])) {
            $_SESSION['touites'] = [];
        }
        $_SESSION['touites'][] = $touite;

        $html = "<p style='color:green;'><strong>Le touite a été ajouté avec succès !</strong></p>";

        return $html;
    }

    public function renderPage(): string {
        return <<<HTML
        <h2>Publier un Touite</h2>
        <form method="POST" action="?action=add-touite">
            <div>
                <label for="texte">Message (max 235 caractères) :</label><br>
                <input type="text" id="texte" name="texte" maxlength="235" required>
            </div>
            <br>
            <button type="submit">Publier</button>
        </form>
HTML;
    }

}