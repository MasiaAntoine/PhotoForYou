<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    $menu = "boutique";
    
    $outil = new Outils;
    
    $boutiqueManager = new BoutiqueManager($db);
    $articles = $boutiqueManager->recupArticles();
    
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
      $outil->redirectUrl("/login.php");
    }
?>

<!DOCTYPE html>
<html lang="<?= $defaultLanguage; ?>">
<head>
    <?php include_once($headerMeta); ?>
    <?php include_once($headerLink); ?>
    <title>Boutique</title>
</head>
<body>
    <?php include_once($headerPath); ?>

    <main>
        <?= $outil->generateTitleBlok("Boutique","Notre boutique en ligne vous permet d'acquérir des crédits pour découvrir et acheter les superbes images de notre collection. Des paysages grandioses, des portraits saisissants et des instantanés de la vie quotidienne, vous pourrez trouver des photos adaptées à vos besoins, grâce à notre système de crédits. En achetant des crédits, vous pourrez facilement sélectionner les images qui vous plaisent et les utiliser pour vos projets personnels ou professionnels. Faites vos achats en toute tranquilité et découvrez notre sélection élégante et diverse de photographies."); ?>
        
        <div class="container py-3">
            <div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
                <?php foreach($articles as $article) { echo $outil->generateArticle($article['creditGiveArticle'], $article['priceArticle'], $article['idArticle'], $article['titleArticle'], $article['descriptionArticle']); } ?>
            </div>
        </div>
    </main>

    <?php include_once($footerPath); ?>
    <?php include_once($script); ?>
</body>
</html>
