<?php

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\actions\DefaultAction;

class Dispatcher {

    public function run(): void {

        $action = $_GET['action'] ?? 'default';

        $html = match ($action) {
            'default' => (new DefaultAction())->execute(),
            default => '<p>Action inconnue</p>'
        };

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur</title>
        </head>
        <body>
            <nav>
                <a href="?action=default">Accueil</a>
            </nav>
            {$html}
        </body>
        </html>
        HTML;
    }
}