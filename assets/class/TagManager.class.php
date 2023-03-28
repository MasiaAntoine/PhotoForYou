<?php
    /**
     * Class manager serves to manage everything that passes in the database
     */
    class TagManager extends Outils {
        private $_db;

        /**

        Constructeur de la classe
        @param PDO $db La connexion à la base de données
        */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**

        Définit la connexion à la base de données
        @param PDO $db La connexion à la base de données
        */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**

        Récupère tous les noms de tags
        @return array Tous les tags avec leurs noms
        */
        public function recupAllTagName() {
            $q = $this->_db->prepare('SELECT * FROM tag ORDER BY nameTag');
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }

        /**

        Supprime un tag
        @param Tag $tag Le tag à supprimer
        */
        public function deleteTag(Tag $tag) {
            $id = $tag->getIdTag();

            $q = $this->_db->prepare("DELETE FROM tag WHERE idTag = :id;");
            $q->execute([
                ':id' => $id
            ]);
        }

        /**

        Ajoute un tag
        @param Tag $tag Le tag à ajouter
        */
        public function addTag(Tag $tag) {
            $nameTag = $tag->getNameTag();

            $q = $this->_db->prepare("INSERT INTO tag(nameTag) VALUES(:nameTag)");
            $q->execute([
                ':nameTag' => $nameTag
            ]);
        }

        /**

        Modifie un tag
        @param Tag $tag Le tag à modifier
        */
        public function updateTag(Tag $tag) {
            $nameTag = $tag->getNameTag();
            $idTag = $tag->getIdTag();

            $q = $this->_db->prepare("UPDATE tag SET nameTag = :nameTag WHERE idTag = :idTag;");
            $q->execute([
                ':nameTag' => $nameTag,
                ':idTag' => $idTag
            ]);
        }
    }