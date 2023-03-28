<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/assets/extensions/functions.php';
    session_start();

    //Si l'utilisateur n'est pas connecter envoyer à la page de connexion
    isConnect();
    
    //vérifier si l'utilisateur est admin
    isAdmin();

    //Vérifier si l'utilisateur est ban du site
    isBan();
    
    //Récupère les data du l'utilisateur
    $dataUser = dataUser($_SESSION['idUser']);

    //Vérifie si il y a une fiche à traiter
    if(selectAllFiche(false) <= 0) {
        redirectUrl("/admin/");
    }

    //Donnée sur la fiche
    $fiche = selectLastFicheSend();

    //Récupère les donnée d'un utilisateur
    $idUser = (int) $fiche['IdUser'];
    $user = dataUser($idUser);

    //Mise à jour de la fiche
    if (isset($_POST['Bouton'])) {

        if($_POST['Bouton'] == "Refuser") {
            //Envoi du mail
            mailRefuserFiche($user['Mail'],$_POST['Raison'],writeCorrespondance($fiche['Correspondance']));

            //Les/le fichier(s) PDF à supprimer
            $pdf = scandir($_SERVER['DOCUMENT_ROOT']."/assets/fichesPDF");
            for($i=0;$i<count($pdf);$i++) {
                if(ucfirst(explode("_", $pdf[$i])[0]) == $fiche["IdFiche"]) {
                    $chemin = strstr($pdf[$i], $fiche["IdFiche"]."_");
                    unlink($_SERVER['DOCUMENT_ROOT']."/assets/fichesPDF/$chemin");
                }
            }

            //Suppression de la fiche de la base de donnée
            deleteFiche($fiche["IdFiche"]);
        }

        if($_POST['Bouton'] == "Confirmer") {
            //Affiche la fiche sur le site
            updateFiche($fiche["IdFiche"],$_POST['Qualité']);

            //ajoute les cerise à l'utilisateur
            if((int) $_POST['Qualité'] == 0) {
                $ceriseAdd = 100;
            }
            elseif((int) $_POST['Qualité'] == 1) {
                $ceriseAdd = 150;
            }
            elseif((int) $_POST['Qualité'] == 2) {
                $ceriseAdd = 300;
            }

            addCerise($ceriseAdd,$idUser);
            mailAccepterFiche($user['Mail'],$ceriseAdd,writeCorrespondance($fiche['Correspondance']));
        }

        redirectUrl("/admin/pages/fiche/ficheAttente.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/main.css" />
    <link rel="stylesheet" href="/assets/css/admin.css" />
    <title>Fiche en attente - Dashboard - Download Cerise Pro</title>
</head>
<body>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/admin.js"></script>

    <!-- Popup -->
    <?php include_once($_SERVER['DOCUMENT_ROOT']."/assets/extensions/all/popup.html"); ?>

    <section>
        <article>
            <p><a href="/admin/" class="button primary small">Retour</a></p>
            <div class="contenerBadge">
                <span class="badge mini"><?= MyDate($fiche['Date']); ?></span>
                <span class="badge mini"><?= htmlspecialchars(openssl_decrypt($user['Nom'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey'])); ?> <?= htmlspecialchars(openssl_decrypt($user['Prenom'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey'])); ?></span>
                <span class="badge mini filiere"><?= $fiche['Filiere']; ?></span>
                <span class="badge mini premium"><?= writeCorrespondance($fiche['Correspondance']); ?></span>
            </div>

            <h2 class="major"><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Titre'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></h2>

            <h3 class="major">1 | Le contexte de réalisation de la situation professionnelle Description du cadre (l'organisation, le service) ; Description des tâches demandées, les résultats attendus</h3>
            <p><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Text1'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></p>
            
            <h3 class="major">2 | Les conditions de réalisation de la situation professionnelle Moyens à disposition, outils à disposition, délais, personnes ressources ; La réalisation : Démarche, choix, décisions, essais ; Traitement : Les éléments complexes, les aléas, incidents, imprévus</h3>
            <p><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Text2'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></p>
            
            <h3 class="major">3 | Les productions résultant de la situation professionnelle Résultats et productions obtenus</h3>
            <p><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Text3'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></p>
            
            <h3 class="major">4 | Analyse de l'action menée Réussites, difficultés</h3>
            <p><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Text4'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></p>
            
            <h3 class="major">5 | Décrivez votre compétence Ce dont vous êtes maintenant capable après avoir fait cette activité</h3>
            <p><?= nl2br(htmlspecialchars(openssl_decrypt($fiche['Text5'], $GLOBALS['codeEncryptionKey'], $GLOBALS['encryptionKey']))); ?></p>

            <h3 class="major">Pièce(s) Jointe(s)</h3>
            <div class="contenerPDF">
            <?php
                $pdf = scandir($_SERVER['DOCUMENT_ROOT']."/assets/fichesPDF");
                for($i=0;$i<count($pdf);$i++) {
                    $chemin = strstr($pdf[$i], $fiche["IdFiche"]."_");
                    if(ucfirst(explode("_", $pdf[$i])[0]) == $fiche["IdFiche"]) {
                        if($chemin) {
                            $namePDF = ucfirst(explode("_", $pdf[$i])[2]);
                            $namePDF = htmlspecialchars($namePDF);
                            echo("<div>");

                            echo("<img class='pdf' src='/images/pdf.png'>");

                            echo("<div>");

                            echo("<a target='_blank' href='/assets/fichesPDF/$chemin'>$namePDF</a>");

                            echo("</div>");

                            echo("</div>");
                        }
                    }
                }
            ?>
            </div>
        </article>

        <div class="sousMenu">
            <div onclick="validFiche(1)" class="radium">
                <img src="/images/refuse.png">
            </div>
            <div onclick="validFiche(0)" class="radium">
                <img src="/images/valid.png">
            </div>
        </div>
    </section>
</body>
</html>