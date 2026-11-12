<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\modele\Touite;
use iutnc\touiteur\repository\TouiteurRepository;

class AddTouiteAction extends Action {

    public function execute(): string {
        // Vérification avec la nouvelle convention de session
        if (!isset($_SESSION['user_id'])) {
            return "<p style='color:red;'><strong>Erreur : vous devez être connecté pour publier un touite.</strong></p>
                    <p><a href='?action=connexion'>Se connecter</a></p>";
        }

        // Affichage initial en GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->renderPage();
        }

        // Récupération et nettoyage des données en POST
        $texte = trim($_POST['texte'] ?? '');
        $idAuteur = (int) $_SESSION['user_id'];
        $date = date('Y-m-d H:i:s');
        $imagePath = null;

        // Validation du contenu
        if (empty(trim($texte))) {
            return "<p style='color:red;'><strong>Erreur : Le texte ne peut pas être vide.</strong></p>" . $this->renderPage();
        }

        if (mb_strlen($texte) > 235) {
            return "<p style='color:red;'><strong>Erreur : Le touite dépasse 235 caractères.</strong></p>" . $this->renderPage();
        }

        // Gestion de l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/img/uploads/';
            !is_dir($uploadDir) && mkdir($uploadDir, 0755, true); // Création du répertoire si inexistant
    
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)); // Récupération de l'extension du fichier
            $destPath = $uploadDir . uniqid('img_', true) . '.' . $ext; // Génération d'un nom unique pour éviter les collisions
    
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
                $imagePath = $destPath;
            }
        }

        // Création de l'objet Touite et enregistrement en BDD
        try {
            // id temporaire à 0 car la BDD génère l'id auto-incrémenté
            $touite = new Touite(0, $texte, $idAuteur, $date, $imagePath);

            $repo = TouiteurRepository::getInstance();
            $repo->ajouterTouite($touite);

            $html = "<p style='color:green;'><strong>Votre touite a été publié avec succès !</strong></p>";
            $html .= "<p><a href='?action=default'>Voir sur l'accueil</a> | <a href='?action=add-touite'>Publier un autre touite</a></p>";
            return $html;
        } catch (\Exception $e) {
            return "<p style='color:red;'><strong>Erreur lors de la publication : " . htmlspecialchars($e->getMessage()) . "</strong></p>" . $this->renderPage();
        }
    }

    public function renderPage(): string {
        $pseudo = htmlspecialchars($_SESSION['user_pseudo'] ?? 'Utilisateur', ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <h2>Publier un Touite</h2>
        <p>Connecté en tant que : <strong>{$pseudo}</strong></p>
        <form method="POST" action="?action=add-touite" enctype="multipart/form-data"> 
                <label for="texte">Message (max 235 caractères) :</label><br><br>
                <textarea id="texte" name="texte" rows="4" cols="50" maxlength="235" placeholder="HELLLOOOOOOOOO !!! Ex: #php #iut" required></textarea>
            <br><br>
            <div>
                <label for="image">Ajouter une image (optionnel) :</label><br><br>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <br>
            <button type="submit">Publier</button>
        </form>
HTML;
    }
}