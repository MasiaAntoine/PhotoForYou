<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    session_start();

    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if($isConnected) {
        $outil->redirectUrl("/");
    }

    if(isset($_POST['connexion'])) {
        $connexion = new User(
            [
            'EmailUser' => $_POST['email'],
            'PasswordUser' => $_POST['password']
            ]
        );
        
        $manager = new UserManager($db);
        $manager->login($connexion);
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

    <title>Connexion</title>
</head>
<body class="text-center">
    
<main class='form-signin w-100 m-auto'>
    <?= $outil->generateForm([
        ['connexion', 'Connexion'],
        ['email', 'email', 'Adresse email'],
        ['password', 'password', 'Mot de passe']
    ]); ?>
    <a href="#">Mot de passe oublié ?</a>
    <br>
    <a href="/register.php">Vous n'avez pas encore de compte ? Inscrivez vous !</a>
</main>
    
    
        
      </body>
</html>