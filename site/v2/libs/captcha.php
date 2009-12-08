<?php
session_start();

function nombre($n)
{
    return str_pad(mt_rand(0,pow(10,$n)-1),$n,'0',STR_PAD_LEFT);
}

function image($mot)
{
    $size = 32;
    $marge = 15;
    $font = dirname(__FILE__).'/vrinda.ttf';
    
    $matrix_blur = array(
        array(1,1,1),
        array(1,1,1),
        array(1,1,1));
        
    $box = imagettfbbox($size, 0, $font, $mot);
    $largeur = $box[2] - $box[0];
    $hauteur = $box[1] - $box[7];
    $largeur_lettre = round($largeur/strlen($mot));
    
    $img = imagecreate($largeur+$marge, $hauteur+$marge);
    $blanc = imagecolorallocate($img, 255, 255, 255); 
    $noir = imagecolorallocate($img, 0, 0, 0);
    
    $couleur = array(
        imagecolorallocate($img, 0x99, 0x00, 0x66),
        imagecolorallocate($img, 0xCC, 0x00, 0x00),
        imagecolorallocate($img, 0x00, 0x00, 0xCC),
        imagecolorallocate($img, 0x00, 0x00, 0xCC),
        imagecolorallocate($img, 0xBB, 0x88, 0x77));

    for($i = 0; $i < strlen($mot);++$i)
    {
        $l = $mot[$i];
        $angle = mt_rand(-40,40);
        imagettftext($img,mt_rand($size-7,$size),$angle,($i*$largeur_lettre)+$marge, $hauteur+mt_rand(0,$marge/2),$couleur[array_rand($couleur)], $font, $l);    
    }
    
    
    imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $noir);
    imageline($img, 2,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $noir);
    
    /* Pour PHP avec la librairie GD complète
    imageconvolution($img, $matrix_blur,9,0);
    imageconvolution($img, $matrix_blur,9,0);
    */
    imagepng($img);
    imagedestroy($img);
}


function captcha()
{
    $mot = nombre(5);
    $_SESSION['captcha'] = $mot;
    image($mot);
}

header("Content-type: image/png");
captcha();
?>