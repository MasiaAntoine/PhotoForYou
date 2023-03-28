<?php
    /**
     * Class PhotoManager
     * 
     * La classe PhotoManager est utilisée pour gérer tout ce qui concerne la base de données.
     * 
     * @extends Outils
     */
    class PhotoManager extends Outils {
        private $_db;

        /**

        Constructeur de la classe
        @param PDO $db Connexion à la base de données
        Cette méthode permet d'initialiser la connexion à la base de données en utilisant la méthode setDB().
        */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**

        Constructeur qui initialise la connexion à la base de données
        @param PDO $db Objet représentant la connexion à la base de données
        */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**

        Méthode recupArticlesNoBuy
        Cette méthode permet de récupérer les articles de la boutique en fonction des paramètres entrés en argument.
        Elle réalise une requête SQL en utilisant les paramètres pour filtrer les résultats et retourne les résultats sous forme d'un tableau associatif.
        @param string $tag Le tag utilisé pour filtrer les articles.
        @param int $fichesLimiteParPage Le nombre d'articles à afficher par page.
        @param int $ficheDeDepart La première article à afficher sur la page.
        @param bool $count Indique si la requête doit retourner le nombre total d'articles (true) ou les articles eux-mêmes (false).
        @param string $search La chaîne de caractères utilisée pour la recherche dans les titres d'articles.
        @return array Un tableau associatif contenant les résultats de la requête.
        */
        public function recupArticlesNoBuy($tag, $fichesLimiteParPage, $ficheDeDepart, $count=false, $search = null) {
            if(!is_null($search)) {
                $search = htmlspecialchars($search);
            }
            
            if($count) {
                $afficheCount = "COUNT(*)";
            } else {
                $afficheCount = "photo.*";
            }
    
            $requete = "SELECT $afficheCount, IFNULL(GROUP_CONCAT(tag.nameTag), 'autre') as nameTag, user.nameUser, user.surnameUser, user.idUser, user.reductionCreditUser, user.rankUser";
            $requete .= " FROM photo";
            $requete .= " LEFT JOIN tagforphoto ON photo.idPhoto = tagforphoto.idPhoto";
            $requete .= " LEFT JOIN tag ON tagforphoto.idTag = tag.idTag";
            $requete .= " LEFT JOIN user ON photo.idUserPhotographer = user.idUser";
            $requete .= " WHERE photo.isBuyPhoto IS NULL";
    
            if(!is_null($tag)) {
                $requete .= " AND (tag.nameTag = :tag OR :tag = 'autre' AND tagforphoto.idTag IS NULL)";
            }
            if(!is_null($search)) {
                $requete .= " AND photo.titlePhoto LIKE :search";
            }
    
            $requete .= " GROUP BY photo.idPhoto";
            $requete .= " ORDER BY photo.datePublicPhoto DESC";
    
            if(!$count) {
                if($fichesLimiteParPage >= 1) {
                    $requete .= " LIMIT $ficheDeDepart,$fichesLimiteParPage";
                }
            }
    
            $q = $this->_db->prepare($requete);
    
            if(!is_null($tag)) {
                $q->execute([
                    ':tag' => $tag,
                    ':search' => '%'.$search.'%'
                ]);
            } else {
                $q->execute([
                    ':search' => '%'.$search.'%'
                ]);
            }
    
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }

        /**

        Méthode qui permet de récupérer les articles débloqué par un utilisateur
        @param Photo $photo instance de la classe Photo contenant les informations sur l'article
        @return array|PDOStatement un tableau associatif contenant les informations sur les articles débloqués par l'utilisateur
        */
        public function recupArticlesBuyUser(Photo $photo) {
            $idUser = $photo->getIdUserPhotographer();

            $q = $this->_db->prepare("SELECT photo.*, IFNULL(GROUP_CONCAT(tag.nameTag), 'autre') as nameTag 
            FROM photo
            LEFT JOIN tagforphoto ON photo.idPhoto = tagforphoto.idPhoto
            LEFT JOIN tag ON tagforphoto.idTag = tag.idTag
            WHERE photo.isBuyPhoto = :idUser
            GROUP BY photo.idPhoto
            ORDER BY photo.datePublicPhoto DESC;");
            $q->execute([
                ":idUser" => $idUser
            ]);
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }

        /**

        Cette méthode permet de retourner les données des articles dans le panier d'achat.
        Elle filtre les ID qui ne sont pas des nombres entiers et construit une requête SQL pour récupérer les informations de photos correspondantes.
        Elle retourne un tableau associatif contenant les données des photos ou un tableau vide si aucun ID valide n'a été trouvé.
        @param array $ids Les IDs des photos à récupérer
        @return array Les données des photos correspondantes
        */
        public function getPhotosByIdsInCardShopping($ids) {
            $filtered_ids = array();
            foreach ($ids as $id) {
                if(filter_var($id, FILTER_VALIDATE_INT)){
                    $filtered_ids[] = $id;
                }
            }
            if(!empty($filtered_ids)){

                // var_dump($filtered_ids);
                $query = "SELECT photo.*, IFNULL(GROUP_CONCAT(tag.nameTag), 'autre') as nameTag, user.nameUser, user.surnameUser, user.idUser, user.reductionCreditUser
                FROM photo 
                LEFT JOIN tagforphoto ON photo.idPhoto = tagforphoto.idPhoto
                LEFT JOIN tag ON tagforphoto.idTag = tag.idTag
                LEFT JOIN user ON photo.idUserPhotographer = user.idUser
                WHERE photo.isBuyPhoto IS NULL AND photo.idPhoto IN (".sprintf(implode(',' ,$filtered_ids)).")
                GROUP BY photo.idPhoto";

                //.sprintf permet de transformer dans cet exemple le tableau en chaine de caractère sans format (brut)

                $q = $this->_db->prepare($query);
                $q->execute();
                return $q->fetchAll(PDO::FETCH_ASSOC);
            } else{
                return array();
            }
        }

        /**

        Cette méthode permet de débloquer les photos achetées par un utilisateur.
        @param int[] $ids Les identifiants des photos à débloquer
        @param int $idUser L'identifiant de l'utilisateur qui a acheté les photos
        */
        public function unclockPhotosForUser($ids,$idUser) {
            $filtered_ids = array();
            foreach ($ids as $id) {
                if(filter_var($id, FILTER_VALIDATE_INT)){
                    $filtered_ids[] = $id;
                }
            }
            if(!empty($filtered_ids)){
                $query = "UPDATE photo
                SET isBuyPhoto = :idUser
                WHERE idPhoto IN (".sprintf(implode(',' ,$filtered_ids)).");";

                $q = $this->_db->prepare($query);
                $q->execute([
                    "idUser" => $idUser
                ]);

                //vide la panier
                setcookie('cardShopping', '[]', time()+3600*24*30, '/', '', true, false);
            }

        }

        /**

        Cette méthode permet de payer en crédit les photographes après l'achat d'images par un utilisateur.
        @param array $idUser Le tableau contenant les IDs des utilisateurs photographes.
        @param array $idCredit Le tableau contenant les crédits correspondants à ajouter à chaque photographe en fonction de leur ID.
        */
        public function updateCreditsOfSeveralPhotographers($idUser, $idCredit) {
            // Initialisation d'un tableau vide pour stocker les id valides
            $filtered_ids = array();
            // Boucle pour vérifier si les id dans les tableaux d'entrée sont des entiers valides
            for ($i = 0; $i < count($idUser); $i++) {
                if(filter_var($idUser[$i], FILTER_VALIDATE_INT) && filter_var($idCredit[$i], FILTER_VALIDATE_FLOAT)){
                    $filtered_ids[] = $idUser[$i];
                }
            }
            // Vérifie si le tableau filtré n'est pas vide
            if(!empty($filtered_ids)){
                // Création de la requête SQL en utilisant le "CASE" et la clause "IN()" pour ajouter les crédits de l'utilisateur en fonction de leur ID
                $query = "UPDATE user SET creditUser = creditUser + (CASE idUser ";
                for($i = 0; $i < count($idUser); $i++){
                    if(filter_var($idUser[$i], FILTER_VALIDATE_INT) && filter_var($idCredit[$i], FILTER_VALIDATE_FLOAT)){
                        $query .= "WHEN {$idUser[$i]} THEN {$idCredit[$i]} ";
                    }
                }
                $query .= "END) WHERE idUser IN (".sprintf(implode(',' ,$filtered_ids)).")";
                // Préparation et exécution de la requête
                $q = $this->_db->prepare($query);
                $q->execute();
            }
        }

        
        /**

        Cette méthode permet de récupérer tous les articles d'un photographe qui n'ont pas encore été achetés.
        Elle utilise la classe Photo pour récupérer l'ID du photographe.
        La requête SQL effectuée joint plusieurs tables (photo, tagforphoto, tag et user) pour récupérer les informations nécessaires
        sur les photos, les tags associés, les informations sur le photographe et la date de publication.
        La requête retourne les données sous forme de tableau associatif et trie les photos par date de publication décroissante.
        @param Photo $photo l'objet de la classe Photo
        @return array un tableau associatif contenant les informations sur les photos du photographe
        */
        public function recupAllArticlesPhotographerNoBuy(Photo $photo) {
            $idPhotographer = $photo->getIdUserPhotographer();

            $q = $this->_db->prepare("SELECT photo.*, IFNULL(GROUP_CONCAT(tag.nameTag), 'autre') as nameTag, user.nameUser, user.surnameUser, user.idUser, user.reductionCreditUser, user.rankUser
            FROM photo
            LEFT JOIN tagforphoto ON photo.idPhoto = tagforphoto.idPhoto
            LEFT JOIN tag ON tagforphoto.idTag = tag.idTag
            LEFT JOIN user ON photo.idUserPhotographer = user.idUser
            WHERE photo.isBuyPhoto IS NULL
            AND photo.idUserPhotographer = :idPhotographer
            GROUP BY photo.idPhoto
            ORDER BY photo.datePublicPhoto DESC;");
            $q->execute([
                ':idPhotographer' => $idPhotographer
            ]);
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }


        /**

        Cette méthode permet d'ajouter une photo en base de données.
        Elle prend en entrée un objet Photo et enregistre son titre, son prix en crédit, sa description et l'ID de son photographe en base de données.
        La méthode utilise la méthode prepare() de PDO pour préparer la requête SQL d'insertion et execute() pour l'exécuter.
        La méthode retourne l'ID de la photo ajoutée en base de données grâce à la méthode lastInsertId() de PDO.
        */
        public function addPhoto(Photo $photo) {
            $titlePhoto = $photo->getTitlePhoto();
            $creditPricePhoto = (float) $photo->getCreditPricePhoto();
            $descriptionPhoto = $photo->getDescriptionPhoto();
            $idUserPhotographer = $_SESSION['idUser'];

            $q = $this->_db->prepare("INSERT INTO photo(titlePhoto, creditPricePhoto, descriptionPhoto, idUserPhotographer) VALUES(:titlePhoto, :creditPricePhoto, :descriptionPhoto, :idUserPhotographer)");
            $q->execute([
                ':titlePhoto' => $titlePhoto,
                ':creditPricePhoto' => $creditPricePhoto,
                ':descriptionPhoto' => $descriptionPhoto,
                ':idUserPhotographer' => $idUserPhotographer
            ]);

            //permet de récupérer l'id
            return $this->_db->lastInsertId();
        }

        /**
         * Méthode permettant de supprimer une photo avec les tags associés pour un photographe.
         * 
         * @param Photo $photo Instance de la classe Photo représentant la photo à supprimer.
         * 
         * @return void
         */
        public function deletePhotoForPhotographer(Photo $photo) {
            $id = $photo->getIdPhoto();

            $q = $this->_db->prepare("DELETE FROM photo WHERE idPhoto = :id AND isBuyPhoto IS NULL;");
            $q->execute([
                ':id' => $id
            ]);

            $q = $this->_db->prepare("DELETE FROM tagforphoto WHERE idPhoto = :id AND NOT EXISTS (SELECT 1 FROM photo WHERE photo.idPhoto = tagforphoto.idPhoto AND photo.isBuyPhoto IS NOT NULL);");
            $q->execute([
                ':id' => $id
            ]);
        }

        /**
         * Méthode permettant de supprimer une photo avec les tags associés pour un client.
         * 
         * @param Photo $photo Instance de la classe Photo représentant la photo à supprimer.
         * 
         * @return void
         */
        public function deletePhotoForClient(Photo $photo) {
            $id = $photo->getIdPhoto();

            $q = $this->_db->prepare("DELETE FROM photo WHERE idPhoto = :id AND isBuyPhoto IS NOT NULL;");
            $q->execute([
                ':id' => $id
            ]);

            $q = $this->_db->prepare("DELETE FROM tagforphoto WHERE idPhoto = :id AND NOT EXISTS (SELECT 1 FROM photo WHERE photo.idPhoto = tagforphoto.idPhoto AND photo.isBuyPhoto IS NOT NULL);");
            $q->execute([
                ':id' => $id
            ]);
        }

        /**
         * Méthode permettant de récupérer les données d'une photo.
         * 
         * @param Photo $photo Instance de la classe Photo pour laquelle les données doivent être récupérées.
         * 
         * @return array Tableau associatif contenant les données de la photo.
         */
        public function recupDate(Photo $photo) {
            $id = $photo->getIdPhoto();
            
            $q = $this->_db->prepare("SELECT * FROM photo WHERE idPhoto = :id;");
            $q->execute([
                ':id' => $id
            ]);
            return $q->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * Méthode permettant de mettre à jour le prix d'une photo.
         * 
         * @param Photo $photo Instance de la classe Photo pour laquelle le prix doit être mis à jour.
         * 
         * @return void
         */
        public function updatePricePhoto(Photo $photo) {
            $id = (int) $photo->getIdPhoto();
            $price = (float) $photo->getCreditPricePhoto();

            $q = $this->_db->prepare("UPDATE photo SET creditPricePhoto = :price WHERE idPhoto = :id;");
            $q->execute([
                ':id' => $id,
                ':price' => $price
            ]);
        }
    }