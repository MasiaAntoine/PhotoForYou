<?php
    // include_once $_SERVER['DOCUMENT_ROOT'].'/photoforyou/assets/config/functions.inc.php';
    include_once $_SERVER['DOCUMENT_ROOT'].'/photoforyou/assets/config/tokenAdminAPI.inc.php';

    // Ip/Adresse
    $servernameDB = "localhost";
    // identifiant
    $usernameDB = "root";
    // Mot de passe
    $passwordDB = "";
    // Nom de la base de données
    $nameDB = "sab_photoforyou";

    //Connexion en base de donnée
    try {
        $db = new PDO("mysql:host=$servernameDB:3306;dbname=$nameDB","$usernameDB","$passwordDB");
        $db->exec('SET NAMES utf8');
    }
    catch(PDOException $e) {
        echo "Erreur : ".$e->getMessage();
        die();
    }

    function noToken() {
        // Création de l'objet JSON d'erreur
        $error = array(
            'error' => array(
            'code' => 401,
            'message' => 'Unauthorized',
            'details' => 'Invalid API token provided'
            )
        );
        
        // Conversion de l'objet JSON en chaîne JSON
        $json_error = json_encode($error);
        
        // Envoi de la réponse HTTP avec le code d'erreur et le contenu JSON
        http_response_code(401);
        header('Content-Type: application/json');
        return $json_error;
    }

    if(isset($_GET['token'])) {
        if($tokenAdminAPI == $_GET['token']) {

            // Requête SQL pour récupérer les informations de la table log
            if(isset($_GET['table'])) {
                $table = $_GET['table'];
                $sql = "SELECT date, table_name, type, detail FROM log WHERE table_name = '$table' ORDER BY date DESC";
                if($_GET['table'] == "all") {
                    $sql = "SELECT date, table_name, type, detail FROM log ORDER BY date DESC";
                }
            }
        
            // Exécution de la requête SQL
            $stmt = $db->query($sql);
        
            // Récupération des résultats sous forme d'un tableau associatif
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Traitement des caractères d'échappement
            $results = array_map(function($row) {
                $row['detail'] = json_decode($row['detail']);
                if (is_string($row['detail'])) {
                    $row['detail'] = str_replace('\\', '', $row['detail']);
                    $row['detail'] = json_encode($row['detail'], JSON_UNESCAPED_UNICODE);
                }
                return $row;
            }, $results);
        
            // Affichage des résultats au format JSON
            header('Content-Type: application/json');
            echo json_encode($results);
        } else {
            echo(noToken());
        }
    } else {
        echo(noToken());
    }
?>
    