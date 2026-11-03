<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\modele\Touite;
use PDO;

class TouiteurRepository {

    private static ?TouiteurRepository $instance = null;
    private static array $config = [];

    private PDO $pdo;

    public static function setConfig(string $file): void {
        self::$config = parse_ini_file($file);
    }

    private function __construct() {

        $dsn = "mysql:host=" . self::$config["host"]
             . ";dbname=" . self::$config["database"]
             . ";charset=utf8mb4";

        $this->pdo = new PDO(
            $dsn,
            self::$config["username"],
            self::$config["password"]
        );

        $this->pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public static function getInstance(): TouiteurRepository {

        if (self::$instance === null) {
            self::$instance = new TouiteurRepository();
        }

        return self::$instance;
    }

public function findAllTouites(): array {

    $requete = $this->pdo->query(
        "SELECT t.id, t.texte, t.date, t.id_auteur, u.pseudo AS auteur FROM touite t INNER JOIN utilisateur u ON t.id_auteur = u.id ORDER BY t.date DESC, t.id DESC");

    $resultat = [];

    while ($ligne = $requete->fetch(PDO::FETCH_ASSOC)) {

        $touite = new Touite((int) $ligne["id"],$ligne["texte"],(int) $ligne["id_auteur"],$ligne["date"]);

        $resultat[] = [
        "touite" => $touite, "auteur" => $ligne["auteur"]
        ];
    }
    return $resultat;
}
}