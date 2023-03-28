<?php 
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/config/functions.inc.php';
    session_start();

    //Classe Outils
    $outil = new Outils;
    $isConnected = $outil->isConnected();

    //Vérifier si l'utilisateur est connecté
    if(!$isConnected) {
        $outil->redirectUrl("/");
    }

    // Classe Utilisateur
    $user = new User(
      [
      'IdUser' => $_SESSION['idUser']
      ]
    );
    $userManager = new UserManager($db);
    $dataUser = $userManager->recupData($user);
    
    //Vérifier si l'utilisateur est Admin
    if($dataUser['rankUser'] != 3) {
      $outil->redirectUrl("/");
    }

    // Classe Tag
    $tag = new Tag([]);
    $tagManager = new TagManager($db);
    $tags = $tagManager->recupAllTagName();

    
    // Partie Suppression du tag
    $messageDeleteTag = "";
    if(isset($_POST['validDeleteTag'])) {
        if($_POST['tagDelete'] > 0) {
            $messageDeleteTag = "Suppression du tag réussi !";
            
            $tag = new Tag([
                'idTag' => (int) $_POST['tagDelete']
            ]);
            $tagManager = new TagManager($db);
            $tagManager->deleteTag($tag);

            //Recharger les tags
            $tags = $tagManager->recupAllTagName();
        }
    }
    
    // Partie Ajout du tag
    $messageAddTag = "";
    if(isset($_POST['validAddTag'])) {
        if(strlen($_POST['tagAdd']) >= 3 && strlen($_POST['tagAdd']) <= 60) {
            $messageAddTag = "Ajout du tag '".$_POST['tagAdd']."' réussi !";
            
            $tag = new Tag([
                'NameTag' => (string) strtolower($_POST['tagAdd'])
            ]);
            $tagManager = new TagManager($db);
            $tagManager->addTag($tag);

            //Recharger les tags
            $tags = $tagManager->recupAllTagName();
        }
    }

    
    // Partie Modification du tag
    $messageEditTag = "";
    if(isset($_POST['validEditTag'])) {
        if(strlen($_POST['tagEditAfter']) >= 3 && strlen($_POST['tagEditAfter']) <= 60) {
            if($_POST['tagEditBefore'] > 0) {
                $messageEditTag = "Modification du tag réussi !";
                
                $tag = new Tag([
                    'IdTag' => (int) $_POST['tagEditBefore'],
                    'NameTag' => (string) strtolower($_POST['tagEditAfter'])
                ]);
                $tagManager = new TagManager($db);
                $tagManager->updateTag($tag);

                //Recharger les tags
                $tags = $tagManager->recupAllTagName();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Corona Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="/admin/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/admin/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="/admin/assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="/admin/assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="/admin/assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="/admin/assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    
    <link rel="stylesheet" href="/admin/assets/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/admin/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="/admin/assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="/admin/assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <?php include($_SERVER['DOCUMENT_ROOT']."/admin/partials/_sidebar.php"); ?>

      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        <?php include($_SERVER['DOCUMENT_ROOT']."/admin/partials/_navbar.php"); ?>

        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ajouter un tag</h4>
                    <p class="card-description">Vous devez écrire un tag et ensuite cliquer sur ajouter.</p>

                    <form class="forms-sample" method="post" action="#tagAdd">
                        <div class="form-group">
                            <label for="tagAdd">Tag</label>
                            <input type="text" class="form-control" id="tagAdd" name="tagAdd" placeholder="zen">
                        </div>
                        
                        <button type="submit" name="validAddTag" class="btn btn-primary mr-2">Ajouter</button>
                    </form>
                    <p class="mt-2 text-success"><?= $messageAddTag ?></p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="card-title">Modifier un tag</h4>
                    <p class="card-description">Vous devez choisir un tag et ensuite remplir le nouveau nom puis cliquer sur modifier.</p>

                    <form class="forms-sample" method="post" action="#tagAdd">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <select class="form-control form-control" id="tagEditBefore" name="tagEditBefore">
                                            <option selected disabled>...</option>
                                            <?php foreach($tags as $tag): ?>
                                            <option value="<?= $tag['idTag'] ?>"><?= $tag['nameTag'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <input type="text" name="tagEditAfter" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="validEditTag" class="btn btn-primary mr-2">Modifier</button>
                    </form>

                    <p class="mt-2 text-success"><?= $messageEditTag ?></p>
                </div>
            </div>

            <div class="card mt-3" id="deleteTag">
                <div class="card-body">
                    <h4 class="card-title">Supprimer un tag</h4>
                    <p class="card-description">Vous devez choisir un tag et ensuite cliquer sur supprimer pour le retirer du site.</p>

                    <form class="forms-sample" method="post" action="#deleteTag">
                        <div class="form-group">
                            <label for="tagDelete">Tag</label>
                            <select class="form-control form-control" id="tagDelete" name="tagDelete">
                                <option selected disabled>...</option>
                                <?php foreach($tags as $tag): ?>
                                <option value="<?= $tag['idTag'] ?>"><?= $tag['nameTag'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" name="validDeleteTag" class="btn btn-danger mr-2">Supprimer</button>
                    </form>
                    <p class="mt-2 text-success"><?= $messageDeleteTag ?></p>
                </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
            <?php include($_SERVER['DOCUMENT_ROOT']."/admin/partials/_footer.php"); ?>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="/admin/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="/admin/assets/vendors/chart.js/Chart.min.js"></script>
    <script src="/admin/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="/admin/assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="/admin/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="/admin/assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    
    <script src="/admin/assets/vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="/admin/assets/js/off-canvas.js"></script>
    <script src="/admin/assets/js/hoverable-collapse.js"></script>
    <script src="/admin/assets/js/misc.js"></script>
    <script src="/admin/assets/js/settings.js"></script>
    <script src="/admin/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="/admin/assets/js/dashboard.js"></script>
    
    <script src="/admin/assets/js/typeahead.js"></script>
    <script src="/admin/assets/js/select2.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>