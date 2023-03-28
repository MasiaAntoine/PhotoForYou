<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';

    $outil = new Outils;
    $isConnected = $outil->isConnected();
    
    //récupère l'id dans l'url
    $id = $_GET['id'];

    //Permet de récupérer les données de la photo
    $photo = new Photo(
        [
        'IdPhoto' => (int) $id
        ]
      );
    $photoManager = new PhotoManager($db);
    $dataPhoto = $photoManager->recupDate($photo);


    // Classe utilisateur
    $user = new User(
        [
        'IdUser' => $_SESSION['idUser']
        ]
        );
    $userManager = new UserManager($db);
    $dataUser = $userManager->recupData($user);


    // ---- PARTIE VERIFICATION ---- \\

    //Vérifier si l'utilisateur est connecté
    if(!$isConnected) {
        $outil->redirectUrl('/profil.php');
    }

    //Vérifier si la photo existe
    if(!$dataPhoto) {
        $outil->redirectUrl("/profil.php");
    }

    //Vérifier si la photo n'est pas acheté
    if(!is_null($dataPhoto['isBuyPhoto'])) {
        $outil->redirectUrl("/profil.php");
    }

    //Pass droit admin (car l'admin peut supprimer les photos de tout les photographes)
    if($dataUser['rankUser'] != 3) {
        //Vérifier si la photo appartient bien au photographe avant de la supprimer
        if($dataPhoto['idUserPhotographer'] != $_SESSION['idUser']) {
            $outil->redirectUrl("/profil.php");
        }
    }


    // ---- PARTIE SUPPRESSION ---- \\

    //Permet de supprimer les photos
    $image = scandir("assets/images/photos");
    for($i=0;$i<count($image);$i++) {
        if($image[$i] != "." and $image[$i] != "..") {
            $image[$i] = explode("_", $image[$i]);
            if($image[$i][0] == $id) {
                $linkImage = $_SERVER['DOCUMENT_ROOT'].'/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                unlink( $linkImage );
            }

        }
    }

    //Permet de supprimer en base de donnée
    $photo = new Photo(
        [
        'IdPhoto' => (int) $id
        ]
      );
    $photoManager = new PhotoManager($db);
    $photoManager->deletePhotoForPhotographer($photo);

    // redirection une fois finie
    if($dataUser['rankUser'] != 3) {
        $outil->redirectUrl("/profil.php");
    } else {  
        $outil->redirectUrl("/admin/pages/photo/photo.php");
    }
?>