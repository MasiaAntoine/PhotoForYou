<?php
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
   
    $outil = new Outils;

    //Si l'utilisateur n'est pas connecter envoyer à la page de connexion
    $isConnected = $outil->isConnected();

    if(!$isConnected) {
        $outil->redirectUrl("/");
    }

    //Récupère les données Paypal
    $json = json_decode($_POST['form'], true);

    //Si la transaction n'a pas eu lieu rediriger
    if(!isset($json)) {
        $outil->redirectUrl("/");
    }

    //Récupère les données importante
    $credit = (int) filter_var($json['purchase_units'][0]['items'][0]['description'], FILTER_SANITIZE_NUMBER_INT);
    $idUser = $_SESSION['idUser'];

    //Créer l'objet user pour récupurer les données
    $user = new User(
        [
        'IdUser' => $idUser
        ]
    );
    $managerUser = new UserManager($db);
    $creditActuel = $managerUser->recupData($user)['creditUser'];

    //Ajoute les credits au compte de l'utilisateur
    $user = new User(
            [
            'IdUser' => $idUser,
            'CreditUser' => $credit+$creditActuel
            ]
        );
    $managerUser = new UserManager($db);
    $managerUser->editCredit($user);