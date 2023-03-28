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

  // Classe Photo
  $photo = new Photo(
    []
  );
  $photoManager = new PhotoManager($db);
  $photos = $photoManager->recupArticlesNoBuy(null, -1, 0, false, null);
  
  //Vérifier si l'utilisateur est Admin
  if($dataUser['rankUser'] != 3) {
    $outil->redirectUrl("/");
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
    <style>
        .photo {
            width: 150px !important;
            height: 100px !important;
            border-radius: 5px !important;
        }
    </style>
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
          <div class="row">
              
              <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Photo</h4>
                    <p class="card-description"> Affiche des informations sur les membres.</p>

                        <button class="btn btn-primary mb-5" onclick="location.href = '/addPhoto.php';">Ajouter une photo</button>

                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th> </th>
                            <th> Photo </th>
                            <th> Photographe </th>
                            <th> Prix crédit(s) </th>
                            <th> Identifiant </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($photos as $photo): ?>
                          <tr>
                            <td>
                              <a class="text-primary" href="/editPhoto.php?id=<?= $photo['idPhoto']; ?>">
                                <i class="mdi mdi-grease-pencil icon-md"></i>
                              </a>

                              <a class="text-danger" href="/deletePhoto.php?id=<?= $photo['idPhoto']; ?>">
                                <i class="mdi mdi-delete icon-md"></i>
                              </a>
                            </td>
                            <td class="text-capitalize">
                                <?php 
                                    $image = scandir($_SERVER['DOCUMENT_ROOT']."/assets/images/photos");
                                    for($i=0;$i<count($image);$i++) {
                                        if($image[$i] != "." and $image[$i] != "..") {
                                            $image[$i] = explode("_", $image[$i]);
                                            if($image[$i][0] == $photo['idPhoto']) {
                                                if($image[$i][1] == 'demo') {
                                                    $linkImage = '/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                                                }
                                            }

                                        }
                                    }
                                ?> 
                              <a href='#'>
                                <img src="<?= $linkImage; ?>" class="mr-2 photo">
                              <?= $photo['titlePhoto']; ?>
                              </a>
                            </td>

                            <td><?= $photo['surnameUser']; ?> <?= $photo['nameUser']; ?></td>

                            <td><?= $photo['creditPricePhoto']; ?></td>
                            <td>#<?= $photo['idPhoto']; ?></td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
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