<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\actions\DefaultAction;

class Dispatcher{

    private string $action;

    public function __construct()
    {
        $this->action = $_GET["action"] ?? "default";
    }

    public function run(): void{

    switch($this->action){       
        case "default":
        default:
            $action = new DefaultAction();
            break;
    }

    $html = $action->execute();

    $this->renderPage($html);
    }

private function renderPage(string $html): void {

    $page = <<<HTML
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Deefy</title>
    </head>

    <body>

        <h1>Deefy</h1>

        <nav>
            <a href="?action=default">Accueil</a>
            <a href="?action=add-user">Inscription</a>
            <a href="?action=add-playlist">Créer une playlist</a>
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