<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    //Si l'utilisateur est connecté
    if(!$isConnected) {
        $_SESSION['redirectPage'] = $outil->recupLink();
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

    //Vérifier si l'utilisateur n'est pas photographe
    if($dataUser['rankUser'] == 2) {
      $outil->redirectUrl("/profil.php");
    }

    //Taux pourcentage de frais pour les photographes
    $globals = new Globals([]);
    $managerGlobals = new GlobalsManager($db);
    $tauxFrais = $managerGlobals->recupCosts();
    $remise = $managerGlobals->recupRemise();

    //objet photo
    $photo = new Photo([]);
        
    $managerPhoto = new PhotoManager($db);

    //Récupère les articles dans le panier
    if(isset($_COOKIE['cardShopping'])) {
      $articles = explode("|", $_COOKIE['cardShopping']);
    }

    //Récupère les données de l'article
    $dataPhoto = $managerPhoto->getPhotosByIdsInCardShopping($articles);

    if(count($dataPhoto) < 1){
      $outil->redirectUrl("/");
    }

    //total de crédit à payer
    $priceCredits = 0;
    foreach($dataPhoto as $price) {
      if($price['reductionCreditUser'] > 0) {
        $priceCredits += (float) round($price["creditPricePhoto"]-($price["creditPricePhoto"]*$price["reductionCreditUser"]/100),2);
      } else {
        $priceCredits += (float) $price['creditPricePhoto'];
      }
    }

    //Si il y a une remise par photoforyou
    if($remise > 0) {
      $priceNoRemiseAffiche = $priceCredits;
      $priceCredits = $priceCredits-($priceCredits*$remise/100);
      $priceCredits = (float) $priceCredits;
    }

    //Si la personne clique sur le bouton pour déboquer les photos
    if(isset($_POST['unlock'])) {
      //Récupère les crédits du l'utilisateur
      $creditUser = (float) $dataUser['creditUser'];

      //Débloque les images
      if($creditUser >= $priceCredits) {

        //Supprime les photos de demo pour faire de la place
        $image = scandir("assets/images/photos");
        for($i=0;$i<count($image);$i++) {
            if($image[$i] != "." and $image[$i] != "..") {
                $image[$i] = explode("_", $image[$i]);
                
                for($c=0;$c<count($articles);$c++) {
                  if($image[$i][0] == $articles[$c]) {
                      if($image[$i][1] == 'demo') {
                        $linkImage = $_SERVER['DOCUMENT_ROOT'].'/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                        unlink( $linkImage );
                      }
                  }
                }
    
            }
        }

        //Débloque les photos pour l'utilisateur
        $managerPhoto->unclockPhotosForUser($articles,$_SESSION['idUser']);

        //Pour mettre à jour les crédits
        $user = new User(
            [
            'IdUser' => $_SESSION['idUser'],
            'CreditUser' => $creditUser-$priceCredits
            ]
        );

        $managerUser = new UserManager($db);
        $managerUser->editCredit($user);

        //Payer les photographes
        $listPhotographer = [];
        $listCreditForPhotographier = [];

        //Permet de générer les deux tabeaux qui permette d'ajouter les crédits aux photagraphe
        foreach($dataPhoto as $photo) {
          $creditPricePhoto = (float) $photo["creditPricePhoto"];

          //Si le photographe à activié une remise
          if($photo['reductionCreditUser'] > 0) {
            $finalPriceGiveForPhotographer = (float) round($photo["creditPricePhoto"]-($photo["creditPricePhoto"]*$photo["reductionCreditUser"]/100),2);
          } else {
            $finalPriceGiveForPhotographer = (float) round($photo["creditPricePhoto"],2);
          }

          //Appliquer les frais sur les photographes
          if($tauxFrais > 0) {
            $finalPriceGiveForPhotographer = (float) round($finalPriceGiveForPhotographer-($finalPriceGiveForPhotographer*$tauxFrais/100),2);
          }

          array_push($listPhotographer, (int) $photo['idUserPhotographer']);
          array_push($listCreditForPhotographier, $finalPriceGiveForPhotographer);
        }

        //Permet de payer tout les photographes
        $managerPhoto->updateCreditsOfSeveralPhotographers($listPhotographer,$listCreditForPhotographier);

        $outil->redirectUrl('/profil.php');
      } else {
        $outil->redirectUrl('/shop.php');
      }
    }
?>

<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
<head>
    <?php include_once($headerMeta); ?>
    <?php include_once($headerLink); ?>
    <title>Boutique</title>  
    <!-- FAKE PAYPAL ACCOUNT
    email sb-wl47zx1457633@personal.example.com
    password btU&oK2! -->
    <!-- Faux Paypal pour faire des tests avec l'API. -->
    <script src="https://www.paypal.com/sdk/js?client-id=AZdSSdqKHQU6acjcamT9569TUZIkE9Xb1FJ5P8orKqsqC2okW_IZACJBmx7Pvn1pNoPlRaDdeFdaq4aA&currency=EUR&buyer-country=FR"></script>
</head>
<body>
    <?php include_once($headerPath); ?>
    <main>
      <?= $outil->generateTitleBlok("Panier","Vous êtes à l'étape finale de votre achat de photos, vous pouvez toujours modifier votre panier."); ?>
        
      <section class="h-100 gradient-custom">
        <div class="container py-5">
          <div class="row d-flex justify-content-center my-4">
            <div class="col-md-8">
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h5 class="mb-0">Panier - <?= count($dataPhoto) ?> produit<?php if(count($dataPhoto) > 1){echo "s";} ?></h5>
                </div>
                <div class="card-body">
                  <!-- Single item -->
                  <div class="row">

                    <?php foreach($dataPhoto as $photo): ?>
                    <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                      <!-- Image -->
                      <div class="bg-image hover-overlay hover-zoom ripple rounded" data-mdb-ripple-color="light">

                        <?php
                          $image = scandir("assets/images/photos");
                          for($i=0;$i<count($image);$i++) {
                              if($image[$i] != "." and $image[$i] != "..") {
                                  $image[$i] = explode("_", $image[$i]);
                                  if($image[$i][0] == $photo["idPhoto"]) {
                                      if($image[$i][1] == 'demo') {
                                          $linkImage = '/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                                      }
                                      if($image[$i][1] == 'real') {
                                        $tailleImage = getimagesize($_SERVER['DOCUMENT_ROOT'].'/assets/images/photos/'.$image[$i][0].'_real_'.$image[$i][2]);
                                      }
                                  }

                              }
                          }
                        ?>

                        <img src='<?= $linkImage ?>'
                          class="w-100" height='120'/>
                        <a href="#!">
                          <div class="mask" style="background-color: rgba(251, 251, 251, 0.2)"></div>
                        </a>
                      </div>
                      <!-- Image -->
                    </div>

                    <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                      <!-- Data -->
                      <p><strong><?= ucfirst($photo["titlePhoto"]) ?></strong></p>
                      <?php
                        $hauteurImage = $tailleImage[1];
                        $longueurImage = $tailleImage[0];
                      ?>
                      <p>
                        Description : <?= ucfirst($photo["descriptionPhoto"]) ?>
                        <br>
                        <?= "$longueurImage x $hauteurImage" ?>
                      </p>
                      <p>
                        <div>
                        <a href='/profil.php?id=<?= ucfirst($photo["idUser"]) ?>'><?= ucfirst($photo["nameUser"]) ?> <?= ucfirst($photo["surnameUser"]) ?></a>
                        </div>

                        <?php if($photo["reductionCreditUser"] > 0): ?>
                        <div>
                        -<?= $photo["reductionCreditUser"]; ?> % offert par le photographe
                        </div>
                        <?php endif; ?>

                      </p>
                      <p>
                      <?php
                        $tags = explode(",", $photo["nameTag"]);
                        for($i=0;$i<count($tags);$i++) {
                            $tag = $tags[$i];
                            echo "<span class='badge rounded-pill bg-secondary mr-5'>#$tag</span> ";
                        }
                      ?>
                      </p>
                      <!-- Data -->
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">

                      <!-- Price -->
                      <p class="text-start text-md-center">

                        <?php if($photo["reductionCreditUser"] > 0): ?>
                        <del><?= $photo["creditPricePhoto"] ?> Crédits</del> <strong><?= round($photo["creditPricePhoto"]-($photo["creditPricePhoto"]*$photo["reductionCreditUser"]/100),2) ?> Crédits</strong>
                        <?php else: ?>
                        <strong><?= number_format($photo["creditPricePhoto"], 2, ',', ' ') ?> Crédits</strong>
                        <?php endif; ?>

                      </p>
                      <p class="text-start text-danger text-md-center">
                        <i class="fa-solid fa-cart-circle-xmark fa-xl" onclick="location.href = '/deletePhotoCardShopping.php?id=<?= $photo['idPhoto'] ?>';"></i>
                      </p>
                      <!-- Price -->
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <!-- Single item -->
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h5 class="mb-0">Résumé</h5>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush">
                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                      <?= count($dataPhoto); ?> Photo<?php if(count($dataPhoto) > 1){echo "s";} ?>
                    </li>

                    <?php if($remise > 0): ?>
                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                      <div>
                        Remise PhotoForYou
                      </div>
                      -<?= $remise ?> %
                    </li>
                    <?php endif; ?>

                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                      <div>
                        <strong>Total</strong>
                      </div>

                      <?php if($remise > 0): ?>
                      <span><del><?= number_format($priceNoRemiseAffiche, 2, ',', ' '); ?> Crédits</del> <strong><?= number_format($priceCredits, 2, ',', ' '); ?> Crédits</strong></span>
                      <?php else: ?>
                      <span><strong><?= number_format($priceCredits, 2, ',', ' '); ?> Crédits</strong></span>
                      <?php endif; ?>

                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                      <form method="post">
                        <button type="post" name="unlock" class="btn btn-primary">Débloquer</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php include_once($footerPath); ?>
    <?php include_once($script); ?>
</body>
</html>
