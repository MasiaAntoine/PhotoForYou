<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';

    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if($isConnected) {
        session_destroy();
    }
    
    $outil->redirectUrl("/");
?>