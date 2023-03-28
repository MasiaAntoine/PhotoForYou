<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    session_start();

    //Classe Outils
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if(!$isConnected) {
        $outil->redirectUrl("/");
    }

    //Vérifier si c'est notre propre profil
    if(isset($_GET['id'])) {
        if($_SESSION['idUser'] == $_GET['id']) {
            $outil->redirectUrl("/profil.php");
        }
    }

    //Vérifier le profil de la personne
    if(isset($_GET['id'])) {
        $id = (int) $_GET['id'];
    } else {
        $id = (int) $_SESSION['idUser'];
    }

    $user = new User(
        [
        'IdUser' => $id
        ]
    );
        
    $manager = new UserManager($db);
    $dataUser = $manager->recupData($user);

    //Vérifier si le profil regardé est bien photographe et si se n'est pas celui de la session en cours.
    if(isset($_GET['id'])) { 
        if($dataUser['rankUser'] != 2) {
            $outil->redirectUrl('/profil.php');
        }
    }

    //Donnée du rank
    $rank = new Rank(
        [
        'IdRank' => (int) $dataUser['rankUser']
        ]
    );

    $rankManager = new RankManager($db);
    $dataRank = $rankManager->recupRank($rank);

    
    //Récupère les articles de l'utilisateur
    $photo = new Photo(
        [
        'IdUserPhotographer' => $id
        ]
    );
        
    $managerPhoto = new PhotoManager($db);

    if(!isset($_GET['id'])) {
        if($dataUser['rankUser'] == 1) {
            $dataPhoto = $managerPhoto->recupArticlesBuyUser($photo);
        } elseif($dataUser['rankUser'] == 2) {
            $dataPhoto = $managerPhoto->recupAllArticlesPhotographerNoBuy($photo);
        }
    } else {
        $dataPhoto = $managerPhoto->recupAllArticlesPhotographerNoBuy($photo);
    }

    
    //Si le photographe clique sur le bouton pour mettre à jour la remise
    if(isset($_POST['clickRemise'])) {
        $user = new User(
            [
            'IdUser' => $id,
            'ReductionCreditUser' => (int) $_POST["remise"]
            ]
        );
            
        $manager = new UserManager($db);
        $dataUser = $manager->updateRemise($user);
        $outil->redirectUrl('profil.php');
    }

    //Afficher le panier
    $ifPhotographer = true;
    if(isset($dataUser['rankUser'])) {
        if($dataUser['rankUser'] == 2) {
            $ifPhotographer = false;
        }
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

        <?php if(!isset($_GET['id'])): ?>

            <section class="py-3 text-center container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 mx-auto">
                        <h1 class="fw-light"><?= ucfirst($dataUser['nameUser']) ?> <?= ucfirst($dataUser['surnameUser']) ?></h1>
                        <h5><span <?php if($dataUser['rankUser'] == 3) { echo('onclick="location.href = \'/admin/\';"'); }  ?> class="badge bg-warning mt-2 mb-2"><?= ucfirst($dataRank) ?></span></h5>
                        <button type="button" class="btn btn-secondary"><?= number_format($dataUser['creditUser'], 2, ',', ' ') ?> Crédit<?php if($dataUser['creditUser'] > 1) {echo 's';} ?></button>

                        <?php if($dataUser['rankUser'] == 2): ?>
                        <button type="button" class="btn btn-primary" onclick="location.href = '/addPhoto.php';">Ajouter une photo</button>
                        <?php endif; ?>

                        <button type="button" class="btn btn-danger" onclick="location.href = '/unsubscribe.php';">Désinscription</button>

                    </div>
                </div>
            </section>

            <section class="py-3 text-center container">
                <hr>
            </section>

            <!-- View client -->
            <?php if($dataUser['rankUser'] == 1): ?>
            <?= $outil->generateTitleBlok("Galerie Photo","Bienvenue dans votre galerie photo ! Vous pouvez télécharger les images que vous avez débloqué directement depuis votre compte."); ?>
            
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php foreach($dataPhoto as $photo): ?>
                        <?= $outil->
                        generateImageBlockInUser(
                            [
                                $photo['titlePhoto'],
                                $photo['descriptionPhoto'],
                                "/assets/images/photos/".$photo['idPhoto'],
                                $photo['datePublicPhoto'],
                                $photo['nameTag'],
                                $photo['idPhoto'],
                                $dataUser['rankUser']
                            ]
                        ); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php elseif($dataUser['rankUser'] == 2): ?>


            
                <?= $outil->generateTitleBlok("Galerie Photo","Vous pouvez voir ici tout les photos que vous avez publié."); ?>
            
            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php foreach($dataPhoto as $photo): ?>
                        <?= $outil->
                        generateImageBlockInUser(
                            [
                                $photo['titlePhoto'],
                                $photo['descriptionPhoto'],
                                "/assets/images/photos/".$photo['idPhoto'],
                                $photo['datePublicPhoto'],
                                $photo['nameTag'],
                                $photo['idPhoto'],
                                $photo['rankUser']
                            ]
                        ); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <section class="py-3 container">
                <hr>
            </section>

            <?php if($dataUser['creditUser'] > 10): ?>    
                <?= $outil->generateTitleBlok("Retrait", "Vous pouvez choisir une option pour échanger vos crédits en euros."); ?>
                <section class="py-1 px-5 text-center container d-flex justify-content-center gap-3">
                    <div>
                        <img class='bi me-2' src='/assets/images/icons/paypal.png' height='60'>
                        <div>Paypal</div>
                    </div>
                    <div>
                        <img class='bi me-2' src='/assets/images/icons/cheque.png' height='60'>
                        <div>Cheque</div>
                    </div>
                </section>

                <section class="py-5 container">
                    <hr>
                </section>
            <?php else: ?>
                <?= $outil->generateTitleBlok("Retrait", "Vous pouvez demander un retrait en euros quand votre montant de crédit sera suppérieur à 10."); ?>
                <section class="py-2 container">
                    <hr>
                </section>
            <?php endif; ?>

            <section class=" px-5 text-center container">
                <form class="px-5" method="post">
                    <div class="input-group px-5">

                        <?= $outil->generateTitleBlok("Remise", "Cette option vous permet de réduire vos prix sur l'ensemble de vos photos."); ?>
                        
                        <select class="form-select" aria-label="Default select example" name="remise">
                
                            <?php if(isset($dataUser['reductionCreditUser'])): ?>
                            <option value="<?= $dataUser['reductionCreditUser'] ?>" selected><?= $dataUser['reductionCreditUser'] ?> % de réduction</option>
                            <?php else: ?>
                            <option selected>---</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 0): ?>
                            <option value="0">0 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 5): ?>
                            <option value="5">5 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 10): ?>
                            <option value="10">10 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 15): ?>
                            <option value="15">15 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 20): ?>
                            <option value="20">20 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 25): ?>
                            <option value="25">25 % de réduction</option>
                            <?php endif; ?>

                            <?php if($dataUser['reductionCreditUser'] != 30): ?>
                            <option value="30">30 % de réduction</option>
                            <?php endif; ?>
                        </select>

                        <button class="btn btn-outline-primary" name='clickRemise' type='submit'>Mettre à jour</button>
                    </div>
                </form>
            </section>

            <?php endif; ?>
        <?php else: ?>
            
            <!-- ----- PARTIE EXTERIEUR ---- -->
            <section class="py-3 text-center container">
                <div class="row">
                    <div class="col-lg-8 col-md-8 mx-auto">
                        <h1 class="fw-light"><?= ucfirst($dataUser['nameUser']) ?> <?= ucfirst($dataUser['surnameUser']) ?></h1>
                        <h5><span class="badge bg-warning mt-2 mb-2"><?= ucfirst($dataRank) ?></span></h5>
                    </div>
                </div>
            </section>

            <section class="py-3 text-center container">
                <hr>
            </section>
 
            <?= $outil->generateTitleBlok("Galerie Photo","Vous pouvez voir les images du photographe ici."); ?>

            <div class="album py-5 bg-light">
                <div class="container">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                        <?php foreach($dataPhoto as $photo): ?>
                        <?= $outil->
                            generateImageBlock(
                                [
                                    $photo['titlePhoto'],
                                    $photo['descriptionPhoto'],
                                    "/assets/images/photos/".$photo['idPhoto'],
                                    $photo['datePublicPhoto'],
                                    $photo['creditPricePhoto'],
                                    $photo['nameTag'],
                                    $photo['idPhoto'],
                                    $photo['nameUser']." ".$photo['surnameUser'],
                                    $photo['idUser'],
                                    $photo['reductionCreditUser'],
                                    $ifPhotographer
                                ]
                            ); 
                        ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php include_once($footerPath); ?>
        <?php include_once($script); ?>
    </body>
</html>