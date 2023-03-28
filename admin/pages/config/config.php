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

    // Classe Global
    $globals = new Globals([]);
    $globalsManager = new GlobalsManager($db);

    $costs = $globalsManager->recupCosts();
    $tva = $globalsManager->recupTva();
    $remise = $globalsManager->recupRemise();

    // Partie validation du formulaire
    $messageConfig = "";
    if(isset($_POST['validConfig'])) {
        $messageConfig = 'Mise à jour des données réussi !';
        $remise = (float) $_POST['remise'];
        $costs = (float) $_POST['costs'];
        $tva = (float) $_POST['tva'];

        //Mettre à jour les données
        if($remise <= 0) {
            $remise = 0.0;
        }
        if($remise >= 60) {
            $remise = 60.0;
        }
        
        if($costs <= 0) {
            $costs = 0;
        }
        if($costs >= 50) {
            $costs = 50.0;
        }

        if($tva <= 0) {
            $tva = 0.0;
        }
        if($tva >= 100) {
            $tva = 100.0;
        }

        //Mettre à jour
        $globals = new Globals(
            [
            'Remise' => $remise,
            'Costs' => $costs,
            'Tva' => $tva
            ]
        );
        $globalsManager = new GlobalsManager($db);
        $globalsManager->updateData($globals);
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
            <div class="card" id="config">
                <div class="card-body">
                    <h4 class="card-title">Configuration générale</h4>
                    <p class="card-description"> Cette page vous permet de configurer les paramètres liés aux taxes TVA et aux remises sur votre site. Ces paramètres incluent les taux de TVA applicables à vos produits ou services, ainsi que les modalités de remise que vous offrez à vos clients. Ces informations sont cruciales pour garantir que les prix affichés sur votre site sont exacts et conformes aux normes fiscales en vigueur. Il est important de maintenir ces paramètres à jour pour éviter toute erreur ou confusion lors de la facturation ou de la facturation de vos clients.</p>

                    <form class="forms-sample" method="post" action="#config">
                        <div class="form-group">
                            <label for="remise">Remise</label>
                            <input type="number" step="0.01" class="form-control" id="remise" name="remise" value="<?= $remise ?>" placeholder="20.00">
                        </div>

                        <div class="form-group">
                            <label for="costs">Frais</label>
                            <input type="number" step="0.01" class="form-control" id="costs" name="costs" value="<?= $costs ?>" placeholder="50.00">
                        </div>

                        <div class="form-group">
                            <label for="tva">T.V.A</label>
                            <input type="number" step="0.01" class="form-control" id="tva" name="tva" value="<?= $tva ?>" placeholder="5.5">
                        </div>
                        
                        <button type="submit" name="validConfig" class="btn btn-primary mr-2">Mettre à jour</button>
                    </form>
                    <p class="mt-2 text-success"><?= $messageConfig ?></p>
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