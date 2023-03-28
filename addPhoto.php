<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    //Vérifier si l'utilisateur est connecté
    if(!$isConnected) {
        $outil->redirectUrl("/login.php");
    }

    //récupère les données utilisateur
    $user = new User(
      [
      'IdUser' => $_SESSION['idUser']
      ]
    );
    $managerUser = new UserManager($db);
    $dataUser = $managerUser->recupData($user);

    //récupère la liste des tags
    $tag = new Tag([]);
    $managerTag = new TagManager($db);
    $tags = $managerTag->recupAllTagName();

    //récupère les données globals
    $globals = new Globals([]);
    $globalsManager = new GlobalsManager($db);
    $costs = (int) $globalsManager->recupCosts();

    //Vérifier si l'utilisateur est photographe
    //Pass droit admin (car l'admin peut supprimer les photos de tout les photographes)
    if($dataUser['rankUser'] != 3) {
        if($dataUser['rankUser'] != 2) {
        $outil->redirectUrl("/profil.php");
        }
    }

    //partie formulaire envoi de la photo
    if(isset($_POST['valid'])) {
        var_dump($_POST);
        $dataImg = getimagesize($_FILES['photo']['tmp_name']);

        $error = false;

        //vérifier la taille du titre
        if(!$error) {
            if(strlen($_POST['titlePhoto']) < 5 || strlen($_POST['titlePhoto']) > 40) {
                $error = true;
                $messageError = "Votre titre ne convient pas.";
            }
        }

        //vérifier la description
        if(!$error) {
            if(strlen($_POST['descriptionPhoto']) < 5 || strlen($_POST['descriptionPhoto']) > 200) {
                $error = true;
                $messageError = "Votre description ne convient pas.";
            }
        }

        //vérifier les crédits
        if(!$error) {
            if($_POST['creditPricePhoto'] < 2 || $_POST['creditPricePhoto'] > 100) {
                $error = true;
                $messageError = "Les crédits ne suivent pas la norme.";
            }
        }

        //vérifier la taille de la photo
        if(!$error) {
            if($dataImg[0] < 2400 && $dataImg[1] < 1600) {
                $error = true;
                $messageError = "Votre photo ne respecte pas la taille demander.";
            }
        }

        //Vérifier si l'image ne contient pas d'erreur
        if(!$error) {
            if($_FILES['photo']['error'] != 0) {
                $error = true;
                $messageError = "Une erreur sur la photo est survenu.";
            }
        }

        //Vérifier si l'image possède le bon format
        if(!$error) {
            if($_FILES['photo']['type'] != "image/jpg" && $_FILES['photo']['type'] != "image/jpeg") {
                $error = true;
                $messageError = "Votre photo ne respecte pas le format demander.";
            }
        }

        //Vérifier si l'image ne dépasse pas 30 Mo (31457280 octets)
        if(!$error) {
            if($_FILES['photo']['size'] > 32000000) {
                $error = true;
                $messageError = "Votre photo est trop lourde.";
            }
        }

       // ---- VALIDATION APRES TOUTE LES CONDITIONS ----- \\
        if(!$error) {
            //Partie base de donnée pour la photo
            $photo = new Photo([
                'TitlePhoto' => $_POST["titlePhoto"],
                'CreditPricePhoto' => (float) $_POST["creditPricePhoto"],
                'DescriptionPhoto' => $_POST["descriptionPhoto"]
            ]);
            $photoManager = new PhotoManager($db);
            $id = $photoManager->addPhoto($photo);

            //Partie tag
            if(isset($_POST['tag1'])) {
                $tagForPhoto = new TagForPhoto([
                    'IdPhoto' => (int) $id,
                    'IdTag' => (int) $_POST["tag1"]
                ]);
                $tagForPhotoManager = new TagForPhotoManager($db);
                $tagForPhotoManager->addTag($tagForPhoto);
            }
            if(isset($_POST['tag2'])) {
                $tagForPhoto = new TagForPhoto([
                    'IdPhoto' => (int) $id,
                    'IdTag' => (int) $_POST["tag2"]
                ]);
                $tagForPhotoManager = new TagForPhotoManager($db);
                $tagForPhotoManager->addTag($tagForPhoto);
            }
            if(isset($_POST['tag3'])) {
                $tagForPhoto = new TagForPhoto([
                    'IdPhoto' => (int) $id,
                    'IdTag' => (int) $_POST["tag3"]
                ]);
                $tagForPhotoManager = new TagForPhotoManager($db);
                $tagForPhotoManager->addTag($tagForPhoto);
            }

            //Partie photo
            $codeReal = time();
            $codeDemo = time()+random_int(1000000000, 9999999999);
            $chemin = $_SERVER['DOCUMENT_ROOT']."/assets/images/photos/".$id."_"."real_"."$codeReal.jpg";
            $cheminFinal = $_SERVER['DOCUMENT_ROOT']."/assets/images/photos/".$id."_"."demo_"."$codeDemo.jpg";
    
            //---------- SAVE L'IMAGE ----------
            move_uploaded_file($_FILES['photo']['tmp_name'], $chemin);

            // ---------- AJOUT DU FILAGRAMME ----------
            $outil->createImageDemo($chemin,$cheminFinal);

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
        <?= $outil->generateTitleBlok("Ajouter une photo","Vous pouvez ajouter votre photo depuis ce formulaire. <br>- 2400 x 1600 minimum <br>- 30 Mo maximum  <br>- format JPEG"); ?>

        <div class="px-5">
            <form method="post" enctype="multipart/form-data" class="needs-validation px-5" novalidate>
                <div class="row g-3">
                    <div class="col">
                        <label for="titlePhoto" class="form-label">Titre</label>
                        <input type="text" minlength="5" maxlength="70" class="form-control" id="titlePhoto" name="titlePhoto" placeholder="Paysage de montagne" autocomplete="off" onkeypress="setTimeout('titleForFormAddPhoto()', 100);" onkeydown="setTimeout('titleForFormAddPhoto()', 100);" onchange="setTimeout('titleForFormAddPhoto()', 100);" required>
                        <div class="fs-6 fw-lighter" id="titleForFormAddPhoto"></div>
                        <div class="invalid-feedback">Le libellé est obligatoire.</div>
                        
                        <label for="creditPricePhoto" class="form-label mt-4">Prix (en Credit)</label>
                        <input onkeypress="setTimeout('creditEgalEuroForFormAddPhoto(<?= $costs ?>)', 100);" onkeydown="setTimeout('creditEgalEuroForFormAddPhoto(<?= $costs ?>)', 100);" onchange="setTimeout('creditEgalEuroForFormAddPhoto(<?= $costs ?>)', 100);" type="number" step="0.01" min="2" max="100" class="form-control" id="creditPricePhoto" name="creditPricePhoto" placeholder="2.34" autocomplete="off" required>
                        <div class="fs-6 fw-lighter" id="creditEgalEuroForFormAddPhoto"></div>
                        <div class="invalid-feedback">Le libellé est obligatoire.</div>
                    </div>

                    <div class="col">
                        <label for="descriptionPhoto" class="form-label">Description</label>
                        <textarea class="form-control" rows="5" minlength="5" maxlength="200" id="descriptionPhoto" name="descriptionPhoto" placeholder="Vue panoramique sur les Alpes" onkeypress="setTimeout('descriptionForFormAddPhoto()', 100);" onkeydown="setTimeout('descriptionForFormAddPhoto()', 100);" onchange="setTimeout('descriptionForFormAddPhoto()', 100);" required></textarea>
                        <div class="fs-6 fw-lighter" id="descriptionForFormAddPhoto"></div>
                        <div class="invalid-feedback">
                        Please enter a message in the textarea.
                        </div>
                    </div>
                </div>

                
                <div class="row g-3 mt-3">
                    <div class="col">
                        <label for="photo" class="form-label">Votre photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/jpg, image/jpeg" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col">
                        <label for="tag1" class="form-label">Tag N° 1</label>
                        <select class="form-select" id="tag1" name="tag1" required>
                            <option selected disabled>...</option>
                            <?php foreach($tags as $tag): ?>
                            <option value="<?= $tag['idTag'] ?>"><?= $tag['nameTag'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col">
                        <label for="tag2" class="form-label">Tag N° 2</label>
                        <select class="form-select" id="tag2" name="tag2" required>
                            <option selected disabled>...</option>
                            <?php foreach($tags as $tag): ?>
                            <option value="<?= $tag['idTag'] ?>"><?= $tag['nameTag'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col">
                        <label for="tag3" class="form-label">Tag N° 3</label>
                        <select class="form-select" id="tag3" name="tag3" required>
                            <option selected disabled>...</option>
                            <?php foreach($tags as $tag): ?>
                            <option value="<?= $tag['idTag'] ?>"><?= $tag['nameTag'] ?></option>
                            <?php endforeach; ?>
                        </select>
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
