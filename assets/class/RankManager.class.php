<?php
    /**
     * La classe RankManager est une classe qui étend la classe Outils et sert à gérer les données de la table "rank" en base de données.
     * Elle permet de récupérer le nom du rang d'un utilisateur.
     */
    class RankManager extends Outils {
        private $_db;

        /**
         * Constructeur de la classe RankManager qui récupère la connexion à la base de données.
         * 
         * @param PDO $db Connexion à la base de données.
         */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**
         * Définit la connexion à la base de données.
         * 
         * @param PDO $db Connexion à la base de données.
         */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**
         * Récupère le nom du rang d'un utilisateur en fonction de son identifiant de rang.
         * 
         * @param Rank $rank Objet de type Rank qui contient l'identifiant de rang de l'utilisateur.
         * 
         * @return string Nom du rang de l'utilisateur.
         */
        public function recupRank(Rank $rank) {
            $idRank = $rank->getIdRank();

            $q = $this->_db->prepare('SELECT nameRank FROM rank WHERE idRank = :idRank');
            $q->execute([
                ":idRank" => $idRank
            ]);
            return (string) $q->fetch(PDO::FETCH_ASSOC)['nameRank'];
        }
    }