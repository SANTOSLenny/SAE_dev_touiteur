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

    public function getTouitesByTag(string $tag): array {

    $requete = $this->pdo->prepare(
        "SELECT t.id, t.texte, t.date, t.id_auteur, u.pseudo AS auteur 
         FROM touite t 
         INNER JOIN utilisateur u ON t.id_auteur = u.id 
         INNER JOIN touite_tag t2t ON t.id = t2t.id_touite
         INNER JOIN tag tg ON t2t.id_tag = tg.id
         WHERE tg.libelle = :tag 
         ORDER BY t.date DESC, t.id DESC"
    );

    $requete->execute(['tag' => $tag]);

    $resultat = [];
    while ($ligne = $requete->fetch(PDO::FETCH_ASSOC)) {
        $touite = new Touite((int) $ligne["id"], $ligne["texte"], (int) $ligne["id_auteur"], $ligne["date"]);
        
        $resultat[] = [
            "touite" => $touite, 
            "auteur" => $ligne["auteur"]
        ];
    }
    
    return $resultat;
    }

    public function ajouterTouite(Touite $touite): int {
        $requete = $this->pdo->prepare("INSERT INTO touite (texte, date, id_auteur) VALUES (:texte, :date, :id_auteur)");
        $requete->execute([
            'texte' => $touite->getTexte(),
            'date' => $touite->getDate(), 
            'id_auteur' => $touite->getUid()
        ]);

        $id_touite = $this->pdo->lastInsertId();
        $texte = $touite->getTexte();

        preg_match_all('/#(\w+)/', $texte, $matches);
        $tagsTrouves = $matches[1];

        foreach ($tagsTrouves as $libelleTag) {
            $stmt = $this->pdo->prepare("SELECT id FROM tag WHERE libelle = :libelle");
            $stmt->execute(['libelle' => $libelleTag]);
            $tag = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tag) {
                $id_tag = $tag['id'];
            } else {
                $stmtInsert = $this->pdo->prepare("INSERT INTO tag (libelle) VALUES (:libelle)");
                $stmtInsert->execute(['libelle' => $libelleTag]);
                $id_tag = $this->pdo->lastInsertId();
            }

            $stmtLiaison = $this->pdo->prepare("INSERT INTO touite_tag (id_touite, id_tag) VALUES (:id_touite, :id_tag)");
            $stmtLiaison->execute([
                'id_touite' => $id_touite,
                'id_tag' => $id_tag
            ]);
        }
        
        return $id_touite;
    }
}