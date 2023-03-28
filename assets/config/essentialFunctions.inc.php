<?php
    include $_SERVER['DOCUMENT_ROOT'].'/assets/config/loginDB.inc.php';
    include $_SERVER['DOCUMENT_ROOT'].'/assets/config/global.inc.php';
    
    //Connexion en base de donnée
    try {
        $db = new PDO("mysql:host=$servernameDB:3306;dbname=$nameDB","$usernameDB","$passwordDB");
        $db->exec('SET NAMES utf8');
    }
    catch(PDOException $e) {
        echo "Erreur : ".$e->getMessage();
        die();
    }