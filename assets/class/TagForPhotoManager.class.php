<?php
    /**
     * Classe gérant les opérations en base de données pour les tags associés à une photo.
     * 
     * Cette classe étend la classe Outils pour hériter de ses méthodes.
     */
    class TagForPhotoManager extends Outils {
        private $_db;

        /**
         * Constructeur permettant de définir la connexion à la base de données.
         * 
         * @param PDO $db Connexion à la base de données.
         */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**
         * Méthode permettant d'ajouter un tag à une photo en base de données.
         * 
         * @param TagForPhoto $tagForPhoto Instance de la classe TagForPhoto représentant le tag à ajouter à une photo.
         * 
         * @return void
         */
        public function addTag(TagForPhoto $tagForPhoto) {
            $idPhoto = (int) $tagForPhoto->getIdPhoto();
            $idTag = (int) $tagForPhoto->getIdTag();

            $q = $this->_db->prepare("INSERT INTO tagforphoto(idTag, idPhoto) VALUES(:idTag, :idPhoto)");
            $q->execute([
                ':idPhoto' => $idPhoto,
                ':idTag' => $idTag
            ]);
        }
    }