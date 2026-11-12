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
                "touite" => $touite,
                "auteur" => $ligne["auteur"],
                "id_auteur" => (int) $ligne["id_auteur"]
            ];
        }
        return $resultat;
    }


    public function findTouitesPagines(int $limite, int $offset): array {

        $requete = $this->pdo->prepare(
            "SELECT t.id, t.texte, t.date, t.id_auteur,
                    u.pseudo AS auteur
            FROM touite t
            INNER JOIN utilisateur u ON t.id_auteur = u.id
            ORDER BY t.date DESC, t.id DESC
            LIMIT :limite OFFSET :offset"
        );

        $requete->bindValue(':limite', $limite, PDO::PARAM_INT);
        $requete->bindValue(':offset', $offset, PDO::PARAM_INT);

        $requete->execute();

        $resultat = [];

        while ($ligne = $requete->fetch(PDO::FETCH_ASSOC)) {
            $touite = new Touite(
                (int) $ligne["id"],
                $ligne["texte"],
                (int) $ligne["id_auteur"],
                $ligne["date"]
            );

            $resultat[] = [
                "touite" => $touite,
                "auteur" => $ligne["auteur"],
                "id_auteur" => (int) $ligne["id_auteur"]
            ];
        }

        return $resultat;
    }


    public function countTouites(): int {
        $requete = $this->pdo->query(
            "SELECT COUNT(*) FROM touite"
        );

        return (int) $requete->fetchColumn();
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

    public function getTouitesByUser(int $uid) : array{

        $requete = $this->pdo->prepare("SELECT t.id, t.texte, t.date, t.id_auteur, u.pseudo as auteur from touite t inner join utilisateur u on t.id_auteur = u.id WHERE t.id_auteur = :uid ORDER BY t.date desc, t.id desc");

        $requete->execute([
            "uid" => $uid
        ]);

        $res = [];

        while ($ligne = $requete->fetch(PDO::FETCH_ASSOC)) {

            $touite = new Touite((int) $ligne["id"], $ligne["texte"], (int) $ligne["id_auteur"], $ligne["date"]);

            $res[] = [
                "touite" => $touite,
                "auteur" => $ligne["auteur"]
            ];
        }

        return $res;
    }

    public function voter(int $uid, int $idTouite, int $valeur): bool {

        if ($valeur !== 1 && $valeur !== -1) {
            return false;
        }

        $requete = $this->pdo->prepare(
            "SELECT COUNT(*) FROM vote WHERE id_utilisateur = :uid AND id_touite = :touite"
        );

        $requete->execute([
            "uid" => $uid,
            "touite" => $idTouite
        ]);

        if ($requete->fetchColumn() > 0) {
            return false;
        }

        $requete = $this->pdo->prepare(
            "INSERT INTO vote (id_utilisateur, id_touite, valeur) VALUES (:uid, :touite, :valeur)"
        );

        $requete->execute([
            "uid" => $uid,
            "touite" => $idTouite,
            "valeur" => (string) $valeur
        ]);

        return true;
    }


    public function estSuivi(int $idSuiveur, int $idSuivi): bool {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM suivi_utilisateur WHERE id_suiveur = :suiveur AND id_suivi = :suivi"
        );
        $stmt->execute([
            'suiveur' => $idSuiveur,
            'suivi' => $idSuivi
        ]);
        return (bool) $stmt->fetch();
    }


    public function estAbonneTag(int $idUtilisateur, int $idTag): bool {

        $requete = $this->pdo->prepare(
            "SELECT COUNT(*)
            FROM abonnement_tag
            WHERE id_utilisateur = :uid
            AND id_tag = :tag"
        );

        $requete->execute([
            'uid' => $idUtilisateur,
            'tag' => $idTag
        ]);

        return (int) $requete->fetchColumn() > 0;
    }


    public function abonnerTag(int $idUtilisateur, int $idTag): bool {

        if ($this->estAbonneTag($idUtilisateur, $idTag)) {
            return false;
        }

        $requete = $this->pdo->prepare(
            "INSERT INTO abonnement_tag (id_utilisateur, id_tag)
            VALUES (:uid, :tag)"
        );

        return $requete->execute([
            'uid' => $idUtilisateur,
            'tag' => $idTag
        ]);
    }


    public function tagExiste(int $idTag): bool {

        $requete = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tag WHERE id = :idTag"
        );

        $requete->execute([
            'idTag' => $idTag
        ]);

        return (int) $requete->fetchColumn() > 0;
    }



}