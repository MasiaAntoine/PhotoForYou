<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    session_start();

    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if($isConnected) {
        $outil->redirectUrl("/");
    }

    if(isset($_POST['inscription'])) {
        if($_POST['password'] == $_POST['confirmPassword']) {

            //Vérifier la condition pour être ou non photographe
            if (isset($_POST['isPhotographer'])) {
                $rank = 2;
            } else {
                $rank = 1;
            }

            $inscription = new User(
                [
                'SurnameUser' => $_POST['nom'],
                'NameUser' => $_POST['prenom'],
                'EmailUser' => $_POST['email'],
                'PasswordUser' => $_POST['password'],
                'RankUser' => $rank
                ]
            );
    
            $manager = new UserManager($db);
            $returnRegister = $manager->register($inscription);
        }
    }
?>

<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
<head>
    <?php include_once($headerMeta); ?>
    <?php include_once($headerLink); ?>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">

    <!-- Custom styles for this template -->
    <link href="assets/css/sign-in.css" rel="stylesheet">

    <title>Inscription</title>
</head>
<body class="text-center">
    
<main class='form-signin w-100 m-auto'>
    <?= $outil->generateForm([
        ['inscription', 'Inscription'],
        ['text', 'nom', 'Nom'],
        ['text', 'prenom', 'Prenom'],
        ['email', 'email', 'Adresse email'],
        ['password', 'password', 'Mot de passe'],
        ['password', 'confirmPassword', 'Confirmation mot de passe'],
        ['checkbox', 'isPhotographer', 'Coche pour être photographe']
    ]); ?>
    <a href="/login.php">Déjà inscript ? Connectez vous !</a>
    <?php 
        if(isset($returnRegister)) {
            echo $returnRegister;
        } 
    ?>
</main>
    
    
        
      </body>
</html>