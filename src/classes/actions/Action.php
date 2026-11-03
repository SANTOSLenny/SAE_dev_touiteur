<?php

namespace iutnc\touiteur\actions;

abstract class Action {

    abstract public function execute(): string;
}