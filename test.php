<?php

// ---------- REDIMENTIONNER L'IMAGE ----------
// Le fichier
$filename = $_SERVER['DOCUMENT_ROOT']."/assets/images/photos/test.jpg";

// Définition de la largeur et de la hauteur maximale
$width = 2400;
$height = 1600;

// Content type
header('Content-Type: image/jpeg');

// Cacul des nouvelles dimensions
list($width_orig, $height_orig) = getimagesize($filename);

$ratio_orig = $width_orig/$height_orig;

if ($width/$height > $ratio_orig) {
   $width = $height*$ratio_orig;
} else {
   $height = $width/$ratio_orig;
}

// Redimensionnement
$image_p = imagecreatetruecolor($width, $height);
$image = imagecreatefromjpeg($filename);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

// Affichage
imagejpeg($image_p, $_SERVER['DOCUMENT_ROOT']."/assets/images/photos/test_"."resize.jpg", 200);
?>