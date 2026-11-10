<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\actions\DefaultAction;
use iutnc\touiteur\actions\ConsulterTagAction;
use iutnc\touiteur\actions\ConnexionAction;
use iutnc\touiteur\actions\SuivreAction;
use iutnc\touiteur\actions\ConsulterUtilisateurAction;
use iutnc\touiteur\actions\VoterAction;
use iutnc\touiteur\actions\AddTouiteAction;
use iutnc\touiteur\actions\MurAction;

class Dispatcher {

    private string $action;

    public function __construct()
    {
        $this->action =$_GET["action"] ?? "default";
    }

    public function run(): void {
        switch($this->action){
            case "consulter-tag":
                $action = new ConsulterTagAction();
                break;    
            case "connexion":
                $action = new ConnexionAction();
                break;
            case "suivre": 
                $action = new SuivreAction();
                break;
            case "consulter-utilisateur":
                $action = new ConsulterUtilisateurAction();
                break;
            case "voter":
                $action = new VoterAction();
                break;
            case "add-touite":
                $action = new AddTouiteAction();
                break;
            case "mur":
                $action = new MurAction();
                break;
            case "default":
            default:
                $action = new DefaultAction();
                break;
        }

        $html =$action->execute();
        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $infoUser = isset($_SESSION['user_pseudo']) ? " | Utilisateur actif : <strong>" . htmlspecialchars($_SESSION['user_pseudo'], ENT_QUOTES, 'UTF-8') . "</strong>" : "";
        $lienPublier = isset($_SESSION['user_id']) ? '<a href="?action=add-touite">Publier un Touite</a>' : '';
        
        if (isset($_SESSION['user_id'])) {
            $navLinks .= ' | <a href="?action=mur">Mon Mur</a>';
        }

        $page = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <title>Touiteur</title>
        </head>

        <body>
            <h1>Touiteur</h1>
            <nav>
                <a href="?action=default">Accueil</a>
                <a href="?action=connexion">Choisir un utilisateur</a>
                {$lienPublier}
                {$navLinks}
                <span>{$infoUser}</span>
            </nav>
            <main>
                {$html}
            </main>
        </body>
        
        </html>
        HTML;

        echo $page;
    }
}