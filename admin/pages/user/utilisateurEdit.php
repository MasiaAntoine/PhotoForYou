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

    // Classe Utilisateur pour moi l'admin pour afficher les bonne donnée
    $user = new User(
      [
      'IdUser' => $_SESSION['idUser']
      ]
    );
    $userManager = new UserManager($db);
    $dataUser = $userManager->recupData($user);
    
    // Classe pour l'utilisateur qu'on edite
    $userEdit = new User(
      [
      'IdUser' => (int) $_GET['id']
      ]
    );
    $userEditManager = new UserManager($db);
    $dataUserEdit = $userEditManager->recupData($userEdit);

    //Vérifier si l'utilisateur est Admin
    if($dataUser['rankUser'] != 3) {
      $outil->redirectUrl("/");
    }


    $message = "";


    //------ Permet de bannir ou débannir un membre -----\\
    if(isset($_POST['validBan'])) {
      $ban = new User(
        [
        'IdUser' => (int) $_GET['id']
        ]
      );
      $banManager = new UserManager($db);
      $message = $banManager->ban($ban);
    }



    //------ Permet de mettre à jour les données utilisateur -----\\
    if(isset($_POST['validUpdate'])) {
      //Vérifier si l'adresse email à été modifier
      if($dataUserEdit['emailUser'] != $_POST['emailUser']) {
        $email = $_POST['emailUser'];
      } else {
        $email = "noedit";
      }

      $update = new User(
        [
        'IdUser' => (int) $_GET['id'],
        'NameUser' => $_POST['nameUser'],
        'SurnameUser' => $_POST['surnameUser'],
        'EmailUser' => $email
        ]
      );
      $updateManager = new UserManager($db);
      $message = $updateManager->updateDataUser($update);

      //Relance la recherche de donnée pour la mettre à jour
      $dataUserEdit = $userEditManager->recupData($userEdit);
    }



    //------ Permet de supprimer le compte de l'utilisateur  -----\\
    if(isset($_POST['validDelete'])) {
      $delete = new User(
        [
        'IdUser' => (int) $_GET['id']
        ]
      );
      $deleteManager = new UserManager($db);
      $deleteManager->deleteAccount($delete);

      $outil->redirectUrl('/admin/pages/user/utilisateur.php');
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
                    <h4 class="card-title">Editeur de l'utilisateur</h4>
                    <p class="card-description">Cette page permet aux administrateurs de modifier les informations du profil d'un utilisateur, comme leur nom, leur adresse e-mail, etc ...</p>
                    <?php 
                      if($dataUserEdit['rankUser'] == 1) {
                        $colorRank = "primary";
                      } elseif($dataUserEdit['rankUser'] == 2) {
                        $colorRank = "info";
                      } elseif($dataUserEdit['rankUser'] == 3) {
                        $colorRank = "danger";
                      }
                      $rank = ucfirst($dataUserEdit['rank']);
                    ?>
                    <?= "<label class='badge badge-$colorRank mr-2'>$rank</label>" ?> 
                      
                    <form class="forms-sample mt-3" method="post">
                        <div class="form-group">
                            <label for="surnameUser">Nom</label>
                            <input type="text" class="form-control" id="surnameUser" name="surnameUser" value="<?= $dataUserEdit['surnameUser'] ?>">
                        </div>

                        <div class="form-group">
                            <label for="nameUser">Prénom</label>
                            <input type="text" class="form-control" id="nameUser" name="nameUser" value="<?= $dataUserEdit['nameUser'] ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="emailUser">Email</label>
                            <input type="text" class="form-control" id="emailUser" name="emailUser" value="<?= $dataUserEdit['emailUser'] ?>">
                        </div>
                        
                        <button type="submit" name="validUpdate" class="btn btn-primary mr-2">Mettre à jour</button>

                        <?php 
                          if($dataUserEdit['isBanUser']) {
                            $colorRank = "primary";
                            $nameButton = "Débannir";
                          } elseif(!$dataUserEdit['isBanUser']) {
                            $colorRank = "danger";
                            $nameButton = "Bannir";
                          }

                          $rank = ucfirst($dataUserEdit['rank']);
                        ?>
                        <button type="submit" name="validBan" class="btn btn-<?= $colorRank ?> mr-2"><?= $nameButton ?></button>

                        <button type="submit" name="validDelete" class="btn btn-danger mr-2">Supprimer le compte</button>
                    </form>
                    <p class="mt-2 text-success"><?= $message ?></p>
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
    <!-- End custom js for this page -->
  </body>
</html>