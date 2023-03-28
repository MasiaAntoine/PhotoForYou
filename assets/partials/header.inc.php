<?php
    $isConnected = $outil->isConnected();
    $totalCardShopping = $outil->countCardShopping();
    if(!isset($menu)) {
        $menu = "";
    }

    //Démarre le cookie shopping
    if(!isset($_COOKIE['cardShopping'])) {
        setcookie('cardShopping', '[]', time()+3600*24*30, '/', '', true, false);
    }

    //récupère la remise
    $globals = new Globals([]);
    $managerGlobals = new GlobalsManager($db);
    $remiseHeader = $managerGlobals->recupRemise();

    //récupère les données utilisateur
    if(isset($_SESSION['idUser'])) {
        $userHeader = new User(
          [
          'IdUser' => $_SESSION['idUser']
          ]
        );
        $managerUserHeader = new UserManager($db);
        $dataUserHeader = $managerUserHeader->recupData($userHeader);
    }

    //Afficher le panier
    $ifPhotographer = true;
    if(isset($dataUserHeader['rankUser'])) {
        if($dataUserHeader['rankUser'] == 2) {
            $ifPhotographer = false;
        }
    }
?>

<?php if($remiseHeader > 0): ?>
<div class="p-2 text-bg-warning">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-center">
            Remise -<?= $remiseHeader ?> % sur toute les photos de la boutique ! Offert par PhotoForYou.
        </div>
    </div>
</div>
<?php endif; ?>

<header class="p-3 mb-3 text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <img class='bi me-2' src='/assets/images/logo/logo.png' width='35' height='35'>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="/" class="nav-link px-2 text-<?php if($menu == "accueil") {echo "white";} else {echo "secondary";} ?>">Accueil</a></li>
                <?php if($ifPhotographer): ?>
                <li><a href="/shop.php" class="nav-link px-2 text-<?php if($menu == "boutique") {echo "white";} else {echo "secondary";} ?>">Boutique</a></li>
                <?php endif; ?>
            </ul>

            <div class="text-end">
                <?php if($ifPhotographer): ?>
                <span class="position-relative me-4" onclick="location.href = '/cardShopping.php';">
                    <i class="fa-duotone fa-cart-shopping-fast fa-xl"></i>

                    <span id='notifCardShopping' class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                        <?= $totalCardShopping ?>
                    </span>

                </span>
                <?php endif; ?>

                <?php if(!$isConnected): ?>
                    <button type="button" class="btn btn-outline-light me-2" onclick="location.href = '/login.php';">Connexion</button>
                    <button type="button" class="btn btn-warning" onclick="location.href = '/register.php';">Inscription</button>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-warning me-2" onclick="location.href = '/profil.php';">Profil</button>
                    <button type="button" class="btn btn-danger me-2" onclick="location.href = '/deco.php';">Déconnexion</button>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>