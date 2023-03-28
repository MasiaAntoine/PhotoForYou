<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if($_GET['id'] <= 0) {
        $outil->redirectUrl("/");
    }

    $outil->removePhotoInCardShopping($_GET['id']);
 
    $outil->redirectUrl("/cardShopping.php");
?>