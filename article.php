<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    $menu = "boutique";
    
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    if(!$isConnected) {
        $_SESSION['redirectPage'] = $outil->recupLink();
        $outil->redirectUrl("/login.php");
    }
    
    $boutiqueManager = new BoutiqueManager($db);

    //Vérifier si l'article existe en base de donnée.
    $boutiqueManager->ifArticleExistInBdd($_GET['id']);

    //Récupère les données de l'article
    $article = $boutiqueManager->recupArticle($_GET['id']);

    //Taux TVA
    $globals = new Globals([]);
    $managerGlobals = new GlobalsManager($db);
    $tauxTVA = $managerGlobals->recupTva();
    
    //Donnée de l'article de la boutique crédit
    $name = htmlspecialchars($article['titleArticle']);
    $idArticle = htmlspecialchars($article['idArticle']);
    $credit = htmlspecialchars($article['creditGiveArticle']);
    $description = htmlspecialchars($article['descriptionArticle']);
    
    $price = $article['priceArticle'];
    $priceAffichage = str_replace(".", ",", $price);
    
    
    $priceHT = round($price-($price*$tauxTVA/100), 2);
    $TVA = round($tauxTVA/100*$price, 2);
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
        <?= $outil->generateTitleBlok("Boutique","Vous êtes à l'étape finale de votre achat de crédits pour accéder à notre collection de photographies. Profitez de votre collection de photographies dès maintenant !"); ?>
        
    <section class="h-100 gradient-custom">
  <div class="container py-5">
    <div class="row d-flex justify-content-center my-4">
      <div class="col-md-8">
        <div class="card mb-4">
          <div class="card-header py-3">
            <h5 class="mb-0">Panier - 1 produit</h5>
          </div>
          <div class="card-body">
            <!-- Single item -->
            <div class="row">
              <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                <!-- Image -->
                <div class="bg-image hover-overlay hover-zoom ripple rounded" data-mdb-ripple-color="light">
                  <img src='/assets/images/boutique/<?= $idArticle ?>.png'
                    class="w-100 p-3" />
                  <a href="#!">
                    <div class="mask" style="background-color: rgba(251, 251, 251, 0.2)"></div>
                  </a>
                </div>
                <!-- Image -->
              </div>

              <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                <!-- Data -->
                <p><strong><?= ucfirst($name) ?></strong></p>
                <p>Crédit: <?= number_format($credit, 0, ',', ' ') ?></p>
                <p>Description: <?= ucfirst($description) ?></p>
                <!-- Data -->
              </div>

              <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">

                <!-- Price -->
                <p class="text-start text-md-center">
                  <strong><?= $priceAffichage ?> €</strong>
                </p>
                <!-- Price -->
              </div>
            </div>
            <!-- Single item -->
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <p><strong>Choisi un mode de paiement</strong></p>
            <p class="mb-0">                                    
                <div id="paypal-button-container"></div>
                <div id="return"></div></p>
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
                Produit
                <span><?= $priceHT ?> €</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                TVA (<?= $tauxTVA ?> %)
                <span><?= $TVA ?> €</span>
              </li>
              <li
                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                <div>
                  <strong>Total</strong>
                </div>
                <span><strong><?= $priceAffichage ?> €</strong></span>
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
    <script>
            paypal.Buttons({
                // Sets up the transaction when a payment button is clicked
                createOrder: (data, actions) => {
                return actions.order.create({
                    "purchase_units": [{
                        "custom_id": "<?= $idArticle; ?>|<?= $tauxTVA; ?>",
                        "amount": {
                        "currency_code": "EUR",
                        "value": "<?= $price; ?>",
                        "breakdown": {
                            "item_total": {  /* Required when including the items array */
                                "currency_code": "EUR",
                                "value": "<?= $price; ?>"
                            }
                        },
                        },
                        "items": [
                        {
                            "name": "<?= $name; ?>", /* Shows within upper-right dropdown during payment approval */
                            "description": "<?= substr(number_format($credit,2,',', ' '), 0, -3).' crédits'; ?>",
                            "unit_amount": {
                                "currency_code": "EUR",
                                "value": "<?= $price; ?>"
                            },
                            "quantity": "1"
                        },
                        ],
                    }],
                    
                    // "application_context": {
                    //     "shipping_preference": "NO_SHIPPING"
                    // },
                });
                },
                // Finalize the transaction after payer approval
                onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    var data = new FormData();

                    data.append( "form", JSON.stringify(orderData, null, 2) );
                    fetch('addCredit.inc.php', {
                        method: 'POST',
                        body: data
                    }).then(function(response) {
                        if (response.status >= 200 && response.status < 300) {
                            return response.text()
                        }
                        throw new Error(response.statusText)
                    }).then(function(response) {
                        document.getElementById('return').innerHTML = response;
                    });
                    
                    window.location.replace("/profil.php");
                });
                }
                
            }).render('#paypal-button-container');
            </script>
</body>
</html>
