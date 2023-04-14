<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';

    // Requête SQL pour récupérer les informations de la table log
    $sql = "SELECT date, table_name, type, detail FROM log ORDER BY date DESC";

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

?>
    