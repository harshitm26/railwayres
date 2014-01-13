<?php
  header('Content-Type: image/png');
  session_start();
  $dictionary = 'y3wvu6srpnmk5ji8fed4a7@#$%=?';
  $captcha='dkjrkd';
  for($i=0; $i<6; $i++)
    $captcha{$i}=$dictionary{mt_rand(0, strlen($dictionary)-1)};
  $_SESSION['jetmango'] = $captcha;
  $img = imagecreate(360,50);
  
  $background = imagecolorallocate($img,237, 239, 244);
  $color=array();
  $color[0] = imagecolorallocate($img,mt_rand(0,127), mt_rand(0,127), mt_rand(0,127));
  $color[1] = imagecolorallocate($img,mt_rand(0,127), mt_rand(0,127), mt_rand(0,127));
  $color[2] = imagecolorallocate($img,mt_rand(0,127), mt_rand(0,127), mt_rand(0,127));
  imagefilledrectangle($img,0,0,359,49, $background);
  $font='0'.mt_rand(0,6);
  $font = "./fonts/f".$font.".ttf";
  $minSize=30; $maxSize=35;
  $minAngle=-30; $maxAngle=30;
  $minY=35; $maxY=40;
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),10+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[0]);
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),70+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[1]);
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),130+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[2]);
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),190+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[3]);
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),250+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[4]);
  imagettftext($img,mt_rand($minSize, $maxSize),mt_rand($minAngle, $maxAngle),310+mt_rand(-5,5),mt_rand($minY, $maxY),$color[mt_rand(0,2)],$font,$captcha[5]);
  imagepng($img);
  imagedestroy($img);
  
?>
