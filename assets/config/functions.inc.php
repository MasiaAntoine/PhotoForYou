<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/essentialFunctions.inc.php';

    //Permet de charger les classes
    function changeClass($class) {
        include_once $_SERVER['DOCUMENT_ROOT']."/assets/class/".$class.".class.php";
    }
    spl_autoload_register("changeClass");