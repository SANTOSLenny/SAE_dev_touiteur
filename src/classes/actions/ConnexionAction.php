<?php

namespace iutnc\touiteur\actions;

use iutnc\touiteur\db\ConnectionFactory;

class ConnexionAction extends Action {
     public function execute(): string {
       try {
           $db = ConnectionFactory::makeConnection();


           if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_utilisateur'])) {
               $id = (int) $_POST['id_utilisateur'];
              
               $stmt = $db->prepare("SELECT pseudo FROM utilisateur WHERE id = :id");
               $stmt->execute(['id' => $id]);
               $user = $stmt->fetch();


               if ($user) {
                   $_SESSION['user_id'] = $id;
                   $_SESSION['user_pseudo'] = $user['pseudo'];
                  
                   $pseudoSecurise = htmlspecialchars($user['pseudo'], ENT_QUOTES, 'UTF-8');
                  
                   return "<p>connecté en tant que {$pseudoSecurise}.</p>
                           <a href='?action=default'>Retour à l'accueil</a>";
               } else {
                   return "<p>erreur util introuvable</p><a href='?action=connexion'>Réessayer</a>";
               }
           }
           $stmt = $db->query("SELECT id, pseudo FROM utilisateur");
           $utilisateurs = $stmt->fetchAll();


           $html = "<h2>Choisir un utilisateur de test</h2>";
           $html .= "<form action='?action=connexion' method='POST'>";
           $html .= "<label for='user-select'>Se connecter en tant que : </label>";
           $html .= "<select name='id_utilisateur' id='user-select' required>";
          
           foreach ($utilisateurs as $u) {
               $pseudoSecurise = htmlspecialchars($u['pseudo'], ENT_QUOTES, 'UTF-8');
               $html .= "<option value='{$u['id']}'>{$pseudoSecurise}</option>";
           }
          
           $html .= "</select> ";
           $html .= "<button type='submit'>Se connecter</button>";
           $html .= "</form>";


           return $html;
               } catch (\Exception $e) {
           return "<h3>Erreur SQL / Base de données</h3>
                   <p style='color: red;'>" . $e->getMessage() . "</p>";
       }
     }



}