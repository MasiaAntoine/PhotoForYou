<?php
    /**

    Class BoutiqueManager
    Cette classe va servir à gérer tout ce qui concerne la boutique en interrogeant la base de données.
    Elle hérite de la classe Outils pour utiliser les méthodes y afférentes.
    @property PDO $_db La connexion à la base de données.
    */
    class BoutiqueManager extends Outils {
        private $_db;

        /**

        Constructeur de la classe BoutiqueManager
        Permet de récupérer les valeurs passées à l'instanciation de l'objet.
        @param PDO $db La connexion à la base de données.
        */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**

        Méthode setDB
        Permet de définir la connexion à la base de données.
        @param PDO $db La connexion à la base de données.
        */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**

        Méthode recupArticles
        Permet de récupérer les articles de la boutique en interrogeant la base de données.
        @return array Les articles de la boutique sous forme de tableau associatif.
        */
        public function recupArticles() {
            $q = $this->_db->prepare('SELECT * FROM boutique');
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }

        /**

        Méthode recupArticle
        Permet de récupérer un article spécifique de la boutique en interrogeant la base de données.
        @param integer $id L'identifiant de l'article à récupérer.
        @return array Les informations de l'article sous forme de tableau associatif.
        */
        public function recupArticle($id) {
            $q = $this->_db->prepare('SELECT * FROM boutique WHERE idArticle = :id');
            $q->execute([
                ':id' => $id
            ]);
            return $q->fetch(PDO::FETCH_ASSOC);
        }

        /**

        Méthode ifArticleExistInBdd
        Permet de vérifier si un article existe bien en base de données en comparant son identifiant avec ceux présents dans la boutique.
        Si l'article n'existe pas, l'utilisateur est redirigé vers la page "/shop.php".
        @param integer $idVerif L'identifiant de l'article à vérifier.
        */
        public function ifArticleExistInBdd($idVerif) {
            (int) $idVerif;
            $list = [];
            $dataArticle = $this->recupArticles();

            foreach($dataArticle as $data) {
                $value = (int) $data['idArticle'];
                array_push($list,$value);
            }
            if(!in_array($idVerif, $list)){
                $this->redirectUrl("/shop.php");
            }
        }
    }