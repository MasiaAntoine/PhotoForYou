<?php

    class Outils {
        //Vérifier si l'utilisateur est connecté
        public function isConnected() {
            if(isset($_SESSION['isConnected'])) {
                return true;
            }
            return false;
        }

        //Redirection d'url
        public function redirectUrl($url) {
            header('Location: ' . $url);
            exit;
        }

        //Permet de générer le code d'un formulaire HTML
        //Avec un tableau exemple : 
        // [
        //     ['nameBouton','placeholderBouton'],
        //     ['type','name','placeholder'],
        //     ['type','name','placeholder']
        // ]
        public function generateForm($data) {

            $title = $data[0][1];

            $code = "<form method='post'>";
            $code .= "<img class='mb-4' src='/assets/images/logo/logo.png' width='72' height='72'>";
            $code .= "<h1 class='h3 mb-3 fw-normal'>$title</h1>";
            
            for($i=1;$i<count($data);$i++) {
                $type = $data[$i][0];
                $name = $data[$i][1];
                $placeholder = $data[$i][2];

                if($type != "checkbox") {
                    $code .= "<div class='form-floating'>";
                    $code .= "<input type='$type' class='form-control' name='$name' placeholder='$placeholder' autocomplete='off'>";
                    $code .= "<label for='$name'>$placeholder</label>";
                    $code .= "</div>";
                } else {
                    $code .= "<div class='checkbox mb-3'>";
                    $code .= "<label>";
                    $code .= "<input type='$type' name='$name'> $placeholder";
                    $code .= "</label>";
                    $code .= "</div>";
                }
            }
            
            $boutonName = $data[0][0];
            $boutonPlaceholder = $data[0][1];
            $code .= "<button class='w-100 btn btn-lg btn-primary' name='$boutonName' type='submit'>$boutonPlaceholder</button>";
            $code .= "</form>";
    
            return $code;
        }

        //permet de générer le code HTML d'une image d'affichage.
        public function generateImageBlock($data) {
            $title = ucfirst($data[0]);
            $description = ucfirst($data[1]);
            $linkImage = "";
            $date = $this->formatDate($data[3]);
            $price = $data[4];
            $id = (int) $data[6];
            $photographe = ucwords($data[7]);
            $idPhotographe = $data[8];
            $reductionPhotographe = (int) $data[9];
            $ifPhotographer = (int) $data[10];

            $code = "<div class='col'>";
            $code .= "<div class='card shadow-sm'>";

            $image = scandir("assets/images/photos");
            for($i=0;$i<count($image);$i++) {
                if($image[$i] != "." and $image[$i] != "..") {
                    $image[$i] = explode("_", $image[$i]);
                    if($image[$i][0] == $id) {
                        if($image[$i][1] == 'demo') {
                            $linkImage = '/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                        }
                        if($image[$i][1] == 'real') {
                            $tailleImage = getimagesize($_SERVER['DOCUMENT_ROOT'].'/assets/images/photos/'.$image[$i][0].'_real_'.$image[$i][2]);
                        }
                    }

                }
            }

            $code .= "<a href='$linkImage' target='_blank'><img src='$linkImage' width='100%' height='225' role='img'></a>";
            $code .= "<div class='card-body'>";
            $code .= "<div class='d-flex justify-content-end align-items-center mt-2 mb-4'>";

            //Permet de vérifier si l'image est dans le panier
            if(isset($_COOKIE['cardShopping'])) {
                $cookieCardShopping = explode("|", $_COOKIE['cardShopping']);
                if(in_array($id, $cookieCardShopping)) {
                   $class = "fa-badge-check text-success";
                } else {
                    $class = "fa-cart-plus";
                }
            } else {
                $class = "fa-cart-plus";
            }

            if($ifPhotographer) {
                $code .= "<i id='article-$id' class='fa-duotone $class fa-xl' onclick='addCardShopping($id)'></i>";
            }

            $code .= "</div>";
            $code .= "<h5>$title</h5>";
            $code .= "<p class='card-text'>$description <br>";

            $hauteurImage = $tailleImage[1];
            $longueurImage = $tailleImage[0];
            $code .= "$longueurImage x $hauteurImage</p>";

            $code .= "<p class='card-text'>Par <a href='/profil.php?id=$idPhotographe'>$photographe</a></p>";
            $code .= "<div class='d-flex justify-content-between align-items-center'>";

            $code .= "<small class='text-muted'>$date</small>";

            $code .= "<div class='btn-group'>";
            if($reductionPhotographe > 0) {
                $code .= "<span class='badge text-bg-secondary'><del>$price Crédits</del></span>";
                $code .= "<span class='m-1'></span>";

                $price = round($price-($price*$reductionPhotographe/100),2);
                $code .= "<span class='badge text-bg-warning'>$price Crédits</span>";
            } else {
                $code .= "<span class='badge text-bg-primary'>$price Crédits</span>";
            }
            $code .= "</div>";
            $code .= "</div>";

            $code .= "<div class='mt-2'>";

            $tags = explode(",", $data[5]);

            for($i=0;$i<count($tags);$i++) {
                $tag = $tags[$i];
                $code .= "<span class='badge rounded-pill bg-secondary mr-5'  onclick='location.href = \"/?page=1&tag=$tag\";'>#$tag</span> ";
            }

            $code .= "</div>";

            $code .= "</div>";
            $code .= "</div>";
            $code .= "</div>";

            return $code;
        }

        public function generateTitleBlok($title,$description) {
            $code = "<section class='py-3 text-center container'>";
            $code .= "<div class='row'>";
            $code .= "<div class='col-lg-8 col-md-8 mx-auto'>";
            $code .= "<h1 class='fw-light'>$title</h1>";
            $code .= "<p class='lead text-muted'>$description</p>";
            $code .= "</div>";
            $code .= "</div>";
            $code .= "</section>";

            return $code;
        }

        public function generateArticle($credits, $price, $id, $title, $description) {
            $code = "";
            $code .= "<div class='col'>";
            $code .= "<div class='card mb-4 rounded-3 shadow-sm border-warning'>";
            $code .= "<div class='card-header py-3 text-bg-warning border-warning'>";
            $code .= "<h4 class='my-0 fw-normal'>" . ucfirst($title) . "</h4>";
            $code .= "</div>";
            $code .= "<div class='card-body'>";
            $code .= "<h1 class='card-title pricing-card-title'>" . str_replace(".", ",", $price) . " €<small class='text-muted fw-light'></small></h1>";
            $code .= "<ul class='list-unstyled mt-3 mb-4'>";
            $code .= "<li><b>" . number_format($credits, 0, '', ' ') . " Crédits</b></li>";
            $code .= "<li>" . ucfirst($description) . "</li>";
            $code .= "</ul>";
            $code .= "<a href='/article.php?id=$id'>";
            $code .= "<img class='bi me-2 mb-5' src='/assets/images/boutique/" . $id . ".png' height='120'>";
            $code .= "</a>";
            $code .= "<button type='button' class='w-100 btn btn-lg btn-warning' onclick='location.href = \"/article.php?id=$id\";'>Acheter</button>";
            $code .= "</div>";
            $code .= "</div>";
            $code .= "</div>";
            return $code;
        }
        
        public function recupLink() {
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
            $url = "https"; 
          else
            $url = "http"; 
            
          // Ajoutez // à l'URL.
          $url .= "://"; 
            
          // Ajoutez l'hôte (nom de domaine, ip) à l'URL.
          $url .= $_SERVER['HTTP_HOST']; 
            
          // Ajouter l'emplacement de la ressource demandée à l'URL
          $url .= $_SERVER['REQUEST_URI']; 
              
          // Afficher l'URL
          return $url; 
        }

        public function countCardShopping() {
            if(isset($_COOKIE['cardShopping'])) {
                $cookieCardShopping = explode("|", $_COOKIE['cardShopping']);
                $cookieCardShopping = count($cookieCardShopping);
                return $cookieCardShopping-1;
            }
            return 0;
        }


        //permet de générer le code HTML d'une image acheter par l'utilisateur pour pouvoir la télécharger.
        public function generateImageBlockInUser($data) {
            $title = $data[0];
            $description = $data[1];
            $linkImage = "";
            $date = $this->formatDate($data[3]);
            $id = (int) $data[5];
            $rankUser = (int) $data[6];

            $code = "<div class='col'>";
            $code .= "<div class='card shadow-sm'>";

            $image = scandir("assets/images/photos");
            for($i=0;$i<count($image);$i++) {
                if($image[$i] != "." and $image[$i] != "..") {
                    $image[$i] = explode("_", $image[$i]);
                    if($image[$i][0] == $id) {
                        if($image[$i][1] == 'real') {
                            $linkImage = '/assets/images/photos/'.$image[$i][0].'_'.$image[$i][1].'_'.$image[$i][2];
                        }
                    }

                }
            }

            $code .= "<a href='$linkImage' target='_blank'><img src='$linkImage' width='100%' height='225' role='img'></a>";
            $code .= "<div class='card-body'>";
            $code .= "<h5>$title</h5>";
            $code .= "<p class='card-text'>$description <br>";

            $tailleImage = getimagesize($_SERVER['DOCUMENT_ROOT'].$linkImage);
            $hauteurImage = $tailleImage[1];
            $longueurImage = $tailleImage[0];
            $code .= "$longueurImage x $hauteurImage</p>";

            $code .= "<div class='d-flex justify-content-between align-items-center'>";

            $code .= "<small class='text-muted'>$date</small>";
            $code .= "<div class='btn-group mb-2'>";

            if($rankUser != 2) {
                $code .= "<button type='button' class='btn btn-success' onclick='downloadImage(\"$linkImage\",\"$title\")'>Télécharger</button>";
            } else {
                $code .= "<button type='button' class='btn btn-outline-danger' onclick=\"location.href = '/deletePhoto.php?id=$id';\")'>Supprimer</button>";
                $code .= "<button type='button' class='btn btn-outline-primary' onclick=\"location.href = '/editPhoto.php?id=$id';\")'>Modifier</button>";
            }
            
            $code .= "</div>";
            $code .= "</div>";

            $code .= "<div class='mt-2'>";

            $tags = explode(",", $data[4]);

            for($i=0;$i<count($tags);$i++) {
                $tag = $tags[$i];
                $code .= "<span class='badge rounded-pill bg-secondary mr-5' onclick='location.href = \"/?tag=$tag\";'>#$tag</span> ";
            }

            $code .= "</div>";

            $code .= "</div>";
            $code .= "</div>";
            $code .= "</div>";

            return $code;
        }

        //Permet d'avoir un format de date re structuré
        public function formatDate($date) {
            $month_name = date("F", strtotime($date));
            switch ($month_name) {
              case "January":
                  $month_name = "janvier";
                  break;
              case "February":
                  $month_name = "février";
                  break;
              case "March":
                  $month_name = "mars";
                  break;
              case "April":
                  $month_name = "avril";
                  break;
              case "May":
                  $month_name = "mai";
                  break;
              case "June":
                  $month_name = "juin";
                  break;
              case "July":
                  $month_name = "juillet";
                  break;
              case "August":
                  $month_name = "août";
                  break;
              case "September":
                  $month_name = "septembre";
                  break;
              case "October":
                  $month_name = "octobre";
                  break;
              case "November":
                  $month_name = "novembre";
                  break;
              case "December":
                  $month_name = "décembre";
                  break;
              }
              if(strlen($month_name)>3){
                  $month_name = substr($month_name,0,4);
              }
              return date("d ", strtotime($date)).$month_name.date(" Y", strtotime($date));
          }
          
        //Permet de supprimer du panier un article
        public function removePhotoInCardShopping($idCard) {
            $cookieCard = $_COOKIE['cardShopping'];
            $listeId = explode("|", $cookieCard);
            unset($listeId[array_search($idCard, $listeId)]);
        
            $forCookie = "";
            foreach($listeId as $id) {
                if($id > 0) {
                    $forCookie .= "|$id";
                }
            }
        
            setcookie('cardShopping', $forCookie, time()+3600*24*30, '/', '', true, false);
        }

        //Permet de créer l'image de démo
        public function createImageDemo($chemin,$cheminFinal) {
            // Redimensionnement de l'image
            $image = imagecreatefromjpeg($chemin);
            $newImage = imagecreatetruecolor(1280, 853);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, 1280, 853, imagesx($image), imagesy($image));
            // Ajout du filigrane
            $filigrane = imagecreatefrompng($_SERVER['DOCUMENT_ROOT']."/assets/images/filagramme.png");
            imagecopy($newImage, $filigrane, imagesx($newImage) - imagesx($filigrane), imagesy($newImage) - imagesy($filigrane), 0, 0, imagesx($filigrane), imagesy($filigrane));
            // Enregistrement de l'image redimensionnée et avec le filigrane
            imagejpeg($newImage, $cheminFinal);
            imagedestroy($image);
            imagedestroy($newImage);
            imagedestroy($filigrane);
        }
    }