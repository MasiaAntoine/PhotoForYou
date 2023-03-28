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

    //Permet de récupérer le chemin de la photo
    $image = scandir("assets/images/photos");
    for($i=0;$i<count($image);$i++) {
        if($image[$i] != "." and $image[$i] != "..") {
            $image[$i] = explode("_", $image[$i]);
            if($image[$i][0] == $id) {
                if($image[$i][1] == 'real') {
                    $linkImage = '/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                }
            }

        }
    }

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
        //Vérifier si la photo appartient bien au photographe pour pouvoir la modifier
        if($dataPhoto['idUserPhotographer'] != $_SESSION['idUser']) {
            $outil->redirectUrl("/profil.php");
        }
    }

    // ---- PARTIE FORMULAIRE ---- \\
    if(isset($_POST['valid'])) {
        $error = false;

        //vérifier les crédits
        if(!$error) {
            if($_POST['creditPricePhoto'] < 2 || $_POST['creditPricePhoto'] > 100) {
                $error = true;
                $messageError = "Les crédits ne suivent pas la norme.";
            }
        }

       // ---- VALIDATION APRES TOUTE LES CONDITIONS ----- \\
        if(!$error) {
            //Partie base de donnée pour la photo
            $photo = new Photo([
                'IdPhoto' => (int) $id,
                'CreditPricePhoto' => (float) $_POST["creditPricePhoto"]
            ]);
            $photoManager = new PhotoManager($db);
            $id = $photoManager->updatePricePhoto($photo);

            //redirection
            if($dataUser['rankUser'] != 3) {
                $outil->redirectUrl("/profil.php");
            } else {
                $outil->redirectUrl("/admin/pages/photo/photo.php");
            }
        } 
        
    }
?>


<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
<head>
    <?php include_once($headerMeta); ?>
    <?php include_once($headerLink); ?>
    <title>Accueil</title>
</head>
<body>
    <?php include_once($headerPath); ?>

    <style>
        textarea {
            resize: none;
        }
    </style>
    <main>
        <?php if(isset($messageError)) {echo $messageError;} ?>
        <?= $outil->generateTitleBlok("Modifier le prix","Vous pouvez modifier le prix de votre photo."); ?>

        <div class="px-5">
            
            <div class="mb-5">
                <img class="rounded mx-auto d-block" src="<?= $linkImage ?>" width="40%" height="300" role="img">
            </div>

            <form method="post" enctype="multipart/form-data" class="needs-validation px-5" novalidate>
                <div class="row g-3">
                    <div class="col">
                        <label class="form-label">Titre</label>
                        <input type="text" class="form-control" placeholder="<?= $dataPhoto['titlePhoto'] ?>" disabled>
                        
                        <label for="creditPricePhoto" class="form-label mt-4">Prix (en Credit)</label>
                        <input type="number" onkeypress="setTimeout('creditEgalEuroForFormAddPhoto(60)', 100);" onkeydown="setTimeout('creditEgalEuroForFormAddPhoto(60)', 100);" onchange="setTimeout('creditEgalEuroForFormAddPhoto(60)', 100);" step="0.01" min="2" max="100" class="form-control" id="creditPricePhoto" name="creditPricePhoto" placeholder="2.34" autocomplete="off" value="<?= $dataPhoto['creditPricePhoto'] ?>" required>
                        <div class="fs-6 fw-lighter" id="creditEgalEuroForFormAddPhoto"></div>
                        <div class="invalid-feedback">Le libellé est obligatoire.</div>
                    </div>

                    <div class="col">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" placeholder="<?= $dataPhoto['descriptionPhoto'] ?>" rows="5" disabled></textarea>
                        <div class="fs-6 fw-lighter" id="descriptionForFormAddPhoto"></div>
                        <div class="invalid-feedback">
                        Please enter a message in the textarea.
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" name="valid" class="btn btn-primary">Valider</button>
                    <a href="index.php" class="btn btn-danger my-2">Annuler</a>
                </div>
            </form>

        </div>
    </main>

    <?php include_once($footerPath); ?>
    <?php include_once($script); ?>
</body>
</html>
