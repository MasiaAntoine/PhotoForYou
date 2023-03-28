<?php 
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    $menu = "accueil";

    $outil = new Outils;

    // ----- SYSTEME DE RECHERCHE ----- \\

    //Donnée des photos
    $photo = new photo(
        []
    );
    $photoManager = new PhotoManager($db);
	//Système de pagination
    if(!isset($_GET['page'])) {
        $pagination['actual'] = 1;
    } else {
        $pagination['actual'] = (int) $_GET['page'];
    }

	$pagination['limit'] = 6;
	$pagination['ficheStart'] = ($pagination['actual']-1)*$pagination['limit'];

    if(!isset($_GET['tag'])) {
	    $pagination['total'] = count($photoManager->recupArticlesNoBuy(null,null,null,true));

        if(isset($_GET['search'])) {
            $pagination['total'] = count($photoManager->recupArticlesNoBuy(null,null,null,true,$_GET['search']));
        }

    } else {
	    $pagination['total'] = count($photoManager->recupArticlesNoBuy($_GET['tag'],null,null,true));

        if(isset($_GET['search'])) {
            $pagination['total'] = count($photoManager->recupArticlesNoBuy($_GET['tag'],null,null,true,$_GET['search']));
        }

    }

	$pagination['page'] = (int) ceil($pagination['total']/$pagination['limit']);
	
    // Permet de récupérer les photos avec ou sans le filtre tag
    if(!isset($_GET['tag'])) {
        $dataPhoto = $photoManager->recupArticlesNoBuy(null,$pagination['limit'],$pagination['ficheStart']);

        if(isset($_GET['search'])) {
            $dataPhoto = $photoManager->recupArticlesNoBuy(null,$pagination['limit'],$pagination['ficheStart'],false,$_GET['search']);
        }

    } else {
        $dataPhoto = $photoManager->recupArticlesNoBuy($_GET['tag'],$pagination['limit'],$pagination['ficheStart']);

        if(isset($_GET['search'])) {
            $dataPhoto = $photoManager->recupArticlesNoBuy($_GET['tag'],$pagination['limit'],$pagination['ficheStart'],false,$_GET['search']);
        }


        //récupère la liste des tags
        $tag = new Tag([]);
        $managerTag = new TagManager($db);
        $tags = $managerTag->recupAllTagName();
        $tagList = [];
        foreach($tags as $tag) {
            array_push($tagList, $tag['nameTag']);
        }
        
        // Vérifier si le tag exite bien
        if(!in_array($_GET['tag'], $tagList) && $_GET['tag'] != "autre") {
            $outil->redirectUrl('/');
        }
    }


    // ----- PARTIE RECHERCHE ------ \\
    if(isset($_POST['valid'])) {
        $_POST['search'] = htmlspecialchars($_POST['search']);

        if(!isset($_GET['tag'])) {
            $outil->redirectUrl('/?search='.$_POST['search']);
        } else {
            $outil->redirectUrl('/?tag='.$_GET['tag'].'&search='.$_POST['search']);
        }
    }




    // ----- DONNEE UTILISATEUR ----- \\

    //récupère les données utilisateur
    if(isset($_SESSION['idUser'])) {
        $user = new User(
          [
          'IdUser' => $_SESSION['idUser']
          ]
        );
        $managerUser = new UserManager($db);
        $dataUser = $managerUser->recupData($user);
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
    <title>Accueil</title>
</head>
<body>
    <?php include_once($headerPath); ?>

    <main>
        <?= $outil->generateTitleBlok("PhotoForYou","Photoforyou propose une sélection de magnifiques photographies, capturant la beauté et l'émotion de chaque instant. Que vous cherchiez des paysages époustouflants, des portraits inspirants ou des scènes de la vie quotidienne, vous trouverez certainement ce que vous cherchez parmi les images proposées par photoforyou."); ?>

    <div class="container">
        <div class="album py-5 bg-light">

            <div class="container">
                <!-- RECHERCHE  -->
                <form method="POST">
                <div class="input-group mb-5 px-5">
                    <input type="text" name="search" class="form-control" aria-label="Sizing example input" placeholder="Essayer d'écrire le mot femme" value="<?php if(isset($_POST['search'])) {echo(htmlspecialchars($_POST['search']));} ?>" aria-describedby="inputGroup-sizing-default">
                    <button class="btn btn-primary" name="valid">Recherche</button>
                </div>
                </form>

                <!-- ARTICLES -->
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

            <!-- PAGINATION -->
            <div class="d-flex justify-content-center mt-5 gap-2">
                <?php if($pagination['actual']-1 <= 0): ?>
                    <button class="btn btn-primary btn-sm disabled">Prev</button>
                <?php else: ?>
                    <button onclick="location.href = '/?page=<?= $pagination['actual']-1 ?>';" class="btn btn-primary btn-sm">Prev</button>
                <?php endif; ?>


                <?php for($i=-5;$i<6;$i++): ?>
                    <?php if($i+$pagination['actual']>0 AND $i+$pagination['actual']<=$pagination['page']): ?>
                    <button onclick="location.href = '/?page=<?= $i+$pagination['actual'] ?><?php if(isset($_GET['tag'])) {echo('&tag='.$_GET['tag']);} ?><?php if(isset($_GET['search'])) {echo('&search='.$_GET['search']);} ?>';" type="button" class="btn btn-sm btn-secondary <?php if($i+$pagination['actual']==$pagination['actual']) {echo("disabled");}?>"><?= $i+$pagination['actual']; ?></button>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if($pagination['actual'] >= $pagination['page']): ?>
                    <button class="btn btn-primary btn-sm disabled">Next</button>
                <?php else: ?>
                    <button onclick="location.href = '/?page=<?= $pagination['actual']+1 ?>';" class="btn btn-primary btn-sm">Next</button>
                <?php endif; ?>
            </div>
            
        </div>
    </main>

    <?php include_once($footerPath); ?>
    <?php include_once($script); ?>
</body>
</html>
