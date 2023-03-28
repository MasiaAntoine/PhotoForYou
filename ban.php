<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    session_start();

    //Classe Outils
    $outil = new Outils;
    $isConnected = $outil->isConnected();
?>

<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
    <head>
        <title>Ban</title>
    </head>
    <body>
        Vous êtes ban de notre site !
        <br>
        Nous contacter : contact@photoforyou.fr
        <br>
        <a href="/deco.php">Vous déconnectez de votre compte.</a>
    </body>
</html>