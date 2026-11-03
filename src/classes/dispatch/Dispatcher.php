<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\actions\DefaultAction;
use iutnc\touiteur\actions\ConsulterTagAction;
use iutnc\touiteur\actions\ConnexionAction;

class Dispatcher{

    private string $action;

    public function __construct()
    {
        $this->action = $_GET["action"] ?? "default";
    }

    public function run(): void{

        switch($this->action){       
            case "consulter-tag":
                $action = new ConsulterTagAction();
                break;    
            case "connexion":
                $action = new ConnexionAction();
                break;
            case "default":
                
            default:
                $action = new DefaultAction();
                break;

        }

        $html = $action->execute();

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $infoUser = isset($_SESSION['user_pseudo']) ? " | Utilisateur actif : <strong>" . $_SESSION['user_pseudo'] . "</strong>" : "";
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
                <span>$infoUser</span>
            </nav>

            <main>
                $html
            </main>

        </body>
        </html>
        HTML;

        echo $page;
    }
}