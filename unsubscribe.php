<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';

    $outil = new Outils;
    $isConnected = $outil->isConnected();

    // ---- PARTIE VERIFICATION ---- \\

    //Récupère les données de l'utilisateur
    $user = new User(
        [
        'IdUser' => $_SESSION['idUser']
        ]
    );
    $userManager = new UserManager($db);
    $dataUser = $userManager->recupData($user);

    //Vérifier si l'utilisateur est connecté
    if(!$isConnected) {
        $outil->redirectUrl('/profil.php');
    }

    //Vérifier si l'utilisateur est photographe pour personnalisé le message de désinscrition
    if($dataUser['rankUser'] != 2) {
        $messageDesinscription = "Attention, veuillez noter que si vous vous désinscrivez, vous perdrez toutes les photos achetées et vos crédits.";
    } else {
        $messageDesinscription = "Attention, veuillez noter que si vous vous désinscrivez, vous perdrez toutes les photos de votre catalogue et vos crédits.";
    }

    //Vérifier et récupère les articles de l'utilisateur si il est client ou photographe
    $photo = new Photo([
        "IsBuyPhoto" => (int) $_SESSION['idUser'],
        "IdUserPhotographer" => (int) $_SESSION['idUser']
    ]);
    $photoManager = new PhotoManager($db);



    // ---- PARTIE SUPPRESSION ---- \\

    //Génère la liste pour un photographe
    if(isset($_POST['valid'])) {
        if($dataUser['rankUser'] == 2) { 
            $listPhoto = $photoManager->recupAllArticlesPhotographerNoBuy($photo);
        } else {
            //Génère la liste pour un client
            $listPhoto = $photoManager->recupArticlesBuyUser($photo);
        }
    
        //Fabrique la liste des id des photos a supprimer
        $listIdPhoto = []; 
        foreach($listPhoto as $photo) {
            array_push($listIdPhoto, (int) $photo['idPhoto']);
        }
    
        //Permet de supprimer les photos
        $image = scandir("assets/images/photos");
        for($i=0;$i<count($image);$i++) {
            if($image[$i] != "." and $image[$i] != "..") {
                $image[$i] = explode("_", $image[$i]);
    
                for($c=0;$c<count($listIdPhoto);$c++) {
                    if($image[$i][0] == $listIdPhoto[$c]) {
                        $linkImage = $_SERVER['DOCUMENT_ROOT'].'/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                        unlink($linkImage);
                    }
                }
    
            }
        }
    
        //supprime en base de données les photos concerné
        for($c=0;$c<count($listIdPhoto);$c++) {
            $photo = new Photo(
                [
                'IdPhoto' => (int) $listIdPhoto[$c]
                ]
              );
            $photoManager = new PhotoManager($db);

            // Supprimer les photos du photographe
            if($dataUser['rankUser'] == 2) { 
                $photoManager->deletePhotoForPhotographer($photo);
            } else {
                // Supprimer les photos des clients
                $photoManager->deletePhotoForClient($photo);
            }
        }
    
        //Supprime le compte de l'utilisateur
        $userManager->deleteAccount($user);
    
        // redirection une fois finie
        session_destroy();
        $outil->redirectUrl("/");
    }
?>


<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
    <head>
        <?php include_once($headerMeta); ?>
        <?php include_once($headerLink); ?>

        <title>Profil</title>
    </head>
    <body>
        <?php include_once($headerPath); ?>

        <main>
            <?= $outil->generateTitleBlok("Désinscription",$messageDesinscription); ?>

            <div class="px-5 text-center">
                <form method="post" enctype="multipart/form-data" class="needs-validation px-5">
                    <div class="mt-3">
                        <button type="submit" name="valid" class="btn btn-primary">Valider</button>
                        <a href="/profil.php" class="btn btn-danger my-2">Annuler</a>
                    </div>
                </form>

            </div>
        </main>

        <?php include_once($footerPath); ?>
        <?php include_once($script); ?>
    </body>
</html>