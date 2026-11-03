<?php

namespace iutnc\touiteur\actions;

class DefaultAction extends Action {

    public function execute(): string {
        return '<h1>Bienvenue sur Touiteur</h1>';
    }
}