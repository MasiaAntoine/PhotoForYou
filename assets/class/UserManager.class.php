<?php
    /**

    Classe UserManager permettant de gérer les données en base de données

    Hérite de la classe Outils
    */
    class UserManager extends Outils {
        private $_db;

        /**

        Constructeur qui permet de récupérer les valeurs passées
        @param PDO $db Instance de PDO pour la connexion à la base de données
        */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**

        Setter de $_db
        @param PDO $db Instance de PDO pour la connexion à la base de données
        */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**

        Vérifie si l'adresse email existe en base de données
        @param User $user Instance de la classe User
        @return bool True si l'adresse email existe, sinon False
        */
        private function getExistEmail(User $user) {
            $email = $user->getEmailUser();
            $q = $this->_db->prepare('SELECT * FROM user WHERE emailUser = :email');
            $q->execute([
                ":email" => $email
            ]);
            return (bool) $q->rowCount();
        }

        /**

        Récupère le hash du mot de passe correspondant à l'adresse email

        @param User $user Instance de la classe User

        @return string|null Retourne le hash du mot de passe si l'adresse email existe en base de données, sinon Null
        */
        private function recupPassword(User $user) {
            $email = $user->getEmailUser();
            $emailIsPresent = $this->getExistEmail($user);

            //Si l'adresse mail existe
            if($emailIsPresent) {
                $q = $this->_db->prepare('SELECT passwordUser FROM user WHERE emailUser = :email');
                $q->execute([
                    ":email" => $email
                ]);
                return (string) $q->fetch(PDO::FETCH_ASSOC)['passwordUser'];
            }
            return null;
        }

        /**

        Vérifie le hash du mot de passe en base de données et celui du formulaire

        @param User $user Instance de la classe User

        @return bool True si les mots de passe correspondent, sinon False
        */
        private function comparedHashPassword(User $user) {
            $passwordInBdd = $this->recupPassword($user);
            $passwordInForm = $user->getPasswordUser();

            //Vérifier si le mot de passe est récupéré
            if(!is_null($passwordInBdd)) {
                //Vérifier si le mot de passe correspond
                if($passwordInBdd == $passwordInForm) {
                    return true;
                }
            }
            return false;
        }

        /**

        Cette méthode permet de se connecter et d'ouvrir une session.
        Elle vérifie si le email existe en appelant la méthode getExistEmail et si le mot de passe est correct en appelant la méthode comparedHashPassword.
        Si les deux conditions sont remplies, une session est ouverte avec les variables de session 'isConnected' et 'idUser'.
        Si la variable de session 'redirectPage' est définie, la méthode redirectUrl est appelée avec cette valeur en paramètre. Sinon, la méthode est appelée avec "/" en paramètre.
        @param User $user Un objet utilisateur qui contient les informations de connexion
        */
        public function login(User $user) {
            $getExistEmail = $this->getExistEmail($user);
            $verifPassword = $this->comparedHashPassword($user);

            if($getExistEmail && $verifPassword) {
                $_SESSION['isConnected'] = true;
                $_SESSION['idUser'] = $this->recupId($user);
                if(isset($_SESSION['redirectPage'])) {
                    $this->redirectUrl($_SESSION['redirectPage']);
                } else {
                    $this->redirectUrl("/");
                }
            }
        }

        /**

        Cette méthode permet de récupérer l'identifiant d'un utilisateur en utilisant son adresse email.
        Elle prépare et exécute une requête SQL pour récupérer l'id de l'utilisateur correspondant à l'email fourni dans l'objet utilisateur.
        @param User $user Un objet utilisateur qui contient l'email à utiliser pour la récupération de l'identifiant
        @return int L'identifiant de l'utilisateur trouvé
        */
        private function recupId(User $user) {
            $email = $user->getEmailUser();

            $q = $this->_db->prepare('SELECT idUser FROM user WHERE emailUser = :email');
            $q->execute([
                ":email" => $email
            ]);
            return (int) $q->fetch(PDO::FETCH_ASSOC)['idUser'];
        }

        /**

        Cette méthode permet d'inscrire un utilisateur dans la base de données.
        Elle vérifie les différents champs de l'objet utilisateur pour détecter les erreurs, comme des valeurs nulles pour les noms, prénoms, adresses emails, mots de passe et rangs.
        Elle utilise également la méthode getExistEmail pour vérifier si l'adresse email fournie est déjà présente dans la base de données.
        Si aucune erreur n'est détectée, une requête SQL est préparée et exécutée pour ajouter les informations de l'utilisateur dans la base de données.
        Enfin, elle appelle la méthode redirectUrl pour rediriger l'utilisateur vers la page de connexion.
        @param User $user Un objet utilisateur qui contient les informations à enregistrer dans la base de données
        @return string|void Une chaîne d'erreur en cas d'erreur détectée, sinon aucune valeur n'est retournée
        */
        public function register(User $user) {
            $surname = $user->getSurnameUser();
            $name = $user->getNameUser();
            $email = $user->getEmailUser();
            $password = $user->getPasswordUser();
            $rank = $user->getRankUser();
            
            $emailIsPresent = $this->getExistEmail($user);

            //Vérification des erreurs
            if($surname == null) {
                return "Erreur au niveau du nom.";
            }
            if($name == null) {
                return "Erreur au niveau du prénom.";
            }
            if($email == null) {
                return "Erreur au niveau du mail.";
            }
            if($password == null) {
                return "Erreur au niveau du mot de passe.";
            }
            if($emailIsPresent) { 
                return "L'adresse email existe déjà.";
            }
            if($rank == null) {
                return "Erreur au niveau du rank.";
            }

            //Si il y a aucune erreur ajouter en base de donnée.
            $q = $this->_db->prepare('INSERT INTO user(surnameUser, nameUser, emailUser, passwordUser, rankUser) VALUES(:surname, :name, :email, :password, :rank)');
            $q->execute([
                ":surname" => $surname,
                ":name" => $name,
                ":email" => $email,
                ":password" => $password,
                ":rank" => $rank
            ]);
            $this->redirectUrl("/login.php");
        }

        /**

        Cette méthode vérifie si l'utilisateur est banni du site.

        Si c'est le cas, l'utilisateur est redirigé vers la page de bannissement.

        @param User $user L'utilisateur qui doit être vérifié pour savoir s'il est banni ou non.
        */
        private function isBan(User $user) {
            $idUser = $user->getIdUser();
            
            if($idUser == $_SESSION['idUser']) {
                $q = $this->_db->prepare('SELECT * FROM user WHERE idUser = :idUser');
                $q->execute([
                    ':idUser' => $idUser
                ]);
                $valeur = $q->fetch(PDO::FETCH_ASSOC);
    
                if((bool) $valeur['isBanUser']) {
                    $this->redirectUrl('/ban.php');
                }
            }
        }

        /**

        Cette méthode permet de récupérer les données de l'utilisateur à partir de son identifiant.
        Elle vérifie d'abord si l'utilisateur n'est pas banni du site.
        Si l'utilisateur est banni, il est redirigé vers une page d'erreur.
        Sinon, les données de l'utilisateur sont récupérées à partir de la base de données.
        @param User $user L'objet utilisateur à partir duquel les données seront récupérées.
        @return array Les données de l'utilisateur sous forme de tableau associatif.
        */
        public function recupData(User $user) {
            //Vérifier si l'utilisateur est ban
            $this->isBan($user);

            $id = $user->getIdUser();

            $q = $this->_db->prepare('SELECT user.*, rank.nameRank as rank FROM user LEFT JOIN rank ON user.rankUser = rank.idRank WHERE idUser = :id');
            $q->execute([
                ":id" => $id
            ]);
            return $q->fetch(PDO::FETCH_ASSOC);
        }

        /**
         * Ajout de crédits à un utilisateur
         *
         * @param User $user L'utilisateur à qui on veut ajouter des crédits
         * 
         * @return void
         */
        public function editCredit(User $user) {
            $idUser = $user->getIdUser();
            $credit = (float) $user->getCreditUser();

            $q = $this->_db->prepare('UPDATE user SET creditUser = :credit WHERE idUser = :idUser');
            $q->execute([
                ':idUser' => $idUser,
                ':credit' => $credit
            ]);
        }

        /**
         * Mise à jour de la remise d'un photographe
         *
         * @param User $user Le photographe pour lequel on veut mettre à jour la remise
         * 
         * @return void
         */
        public function updateRemise(User $user) {
            $idUser = $user->getIdUser();
            $remise = (int) $user->getReductionCreditUser();

            $q = $this->_db->prepare('UPDATE user SET reductionCreditUser = :remise WHERE idUser = :idUser');
            $q->execute([
                ':idUser' => $idUser,
                ':remise' => $remise
            ]);
        }

        /**
         * Suppression d'un compte utilisateur
         *
         * @param User $user L'utilisateur dont on veut supprimer le compte
         * 
         * @return void
         */
        public function deleteAccount(User $user) {
            $idUser = $user->getIdUser();

            $q = $this->_db->prepare('DELETE FROM user WHERE idUser = :idUser;');
            $q->execute([
                ':idUser' => $idUser
            ]);

        }

        /**
         * Récupération de tous les utilisateurs du site
         *
         * @return array Tableau associatif contenant les informations de tous les utilisateurs du site
         */
        public function recupAllUser() {
            $q = $this->_db->prepare('SELECT user.*, rank.nameRank as rank FROM user LEFT JOIN rank ON user.rankUser = rank.idRank');
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Bannissement ou débannissement d'un membre
         *
         * @param User $user Le membre à bannir ou débannir
         * 
         * @return string Message de confirmation de l'opération effectuée (bannissement ou débannissement)
         */
        public function ban(User $user) {
            $idUser = $user->getIdUser();

            $isBan = (bool) $this->recupData($user)['isBanUser'];

            if($isBan) {
                $isBanUser = 0;
                $message = "Vous venez de débannir le membre du site.";
            } else {
                $isBanUser = 1;
                $message = "Vous venez de bannir le membre du site.";
            }

            $q = $this->_db->prepare('UPDATE user SET isBanUser = :isBanUser WHERE idUser = :idUser');
            $q->execute([
                ':idUser' => $idUser,
                ':isBanUser' => $isBanUser
            ]);

            return $message;
        }

        /**

        Cette méthode permet de mettre à jour les données de l'utilisateur dans la base de données.
        Elle prend en paramètre un objet de type User.
        Elle vérifie d'abord si l'adresse email de l'utilisateur a été modifiée en comparant avec la valeur "noedit".
        Si l'adresse email a été modifiée, la méthode vérifie si elle n'existe pas déjà dans la base de données en utilisant la méthode getExistEmail().
        Si l'adresse email n'existe pas, les données de l'utilisateur sont mises à jour dans la base de données.
        Sinon, un message d'erreur est retourné.
        Si l'adresse email n'a pas été modifiée, les données de l'utilisateur sont mises à jour dans la base de données à l'exception de l'adresse email.
        Un message de confirmation de la mise à jour est retourné.
        @param User $user L'objet User qui contient les nouvelles données de l'utilisateur.
        @return string Un message de confirmation ou d'erreur selon le résultat de la mise à jour des données de l'utilisateur.
        */
        public function updateDataUser(User $user) {
            $nameUser = $user->getNameUser();
            $surnameUser = $user->getSurnameUser();
            $emailUser = $user->getEmailUser();
            $idUser = $user->getIdUser();

            $emailIsPresent = $this->getExistEmail($user);
            var_dump($emailUser);
            //Si l'adresse mail à était modifier faire une vérification
            if($emailUser != "noedit") {
                if(!$emailIsPresent) {
                    $q = $this->_db->prepare('UPDATE user SET nameUser = :nameUser, surnameUser = :surnameUser, emailUser = :emailUser WHERE idUser = :idUser');
                    $q->execute([
                        ':nameUser' => $nameUser,
                        ':surnameUser' => $surnameUser,
                        ':idUser' => $idUser,
                        ':emailUser' => $emailUser
                    ]);
                } else {
                    return "Cettre Adresse email existe déjà vous ne pouvez pas l'utiliser.";
                }
                //Si l'utilisateur n'as pas modifier l'adresse email mettre tout à jour quand même sauf l'adresse mail
            } else {
                $q = $this->_db->prepare('UPDATE user SET nameUser = :nameUser, surnameUser = :surnameUser WHERE idUser = :idUser');
                $q->execute([
                    ':nameUser' => $nameUser,
                    ':surnameUser' => $surnameUser,
                    ':idUser' => $idUser
                ]);
                return "Modification de l'utilisateur mis à jour.";
            }
        }
    }