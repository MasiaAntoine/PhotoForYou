<?php
    /**

    Class GlobalsManager

    Cette classe est destinée à gérer tout ce qui concerne les données globales de l'application dans la base de données.

    Elle hérite de la classe Outils.

    @property PDO $_db Connexion à la base de données
    */
    class GlobalsManager extends Outils {
        private $_db;

        /**

        Récupère la connexion à la base de données passée en paramètre
        @param PDO $db Connexion à la base de données
        */
        public function __construct($db)
        {
            $this->setDB($db);
        }

        /**

        Définit la connexion à la base de données
        @param PDO $db Connexion à la base de données
        */
        private function setDB(PDO $db) {
            $this->_db = $db;
        }

        /**

        Récupère la valeur de la remise enregistrée dans la base de données
        @return float Valeur de la remise
        */
        public function recupRemise() {
            $q = $this->_db->prepare('SELECT remise FROM globals');
            $q->execute();
            return (float) $q->fetch(PDO::FETCH_ASSOC)['remise'];
        }

        /**

        Récupère la valeur des frais enregistrée dans la base de données
        @return float Valeur des frais
        */
        public function recupCosts() {
            $q = $this->_db->prepare('SELECT costs FROM globals');
            $q->execute();
            return (float) $q->fetch(PDO::FETCH_ASSOC)['costs'];
        }

        /**

        Récupère la valeur de la TVA enregistrée dans la base de données
        @return float Valeur de la TVA
        */
        public function recupTva() {
            $q = $this->_db->prepare('SELECT tva FROM globals');
            $q->execute();
            return (float) $q->fetch(PDO::FETCH_ASSOC)['tva'];
        }

        /**

        Met à jour les données globales enregistrées dans la base de données

        @param Globals $globals Données à mettre à jour
        */
        public function updateData(Globals $globals) {
            $remise = (float) $globals->getRemise();
            $costs = (float) $globals->getCosts();
            $tva = (float) $globals->getTva();

            $q = $this->_db->prepare('TRUNCATE TABLE globals;');
            $q->execute();

            $q = $this->_db->prepare('INSERT INTO globals(remise, costs, tva) VALUES(:remise, :costs, :tva);');
            $q->execute([
                ":remise" => $remise,
                ":costs" => $costs,
                ":tva" => $tva
            ]);
        }
    }