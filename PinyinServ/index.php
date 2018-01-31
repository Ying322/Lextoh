<?php
  // TODO Actuellement, on convertit les chaînes en minuscule avec strtolower, ça ne marche pas pour les caractères accentués
  header("Content-Type: text/html; charset=utf-8"); 
  // BD connex




define('REPERTOIRE_COURANT',getcwd()); 
if(!$api) {
include ('../menu.php');
}
  // Lire les paramètres
  $text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
if($text){
$mainCommand = "java -cp \"/var/www/Ci-Hai/lingOutils/HanLP.jar:/var/www/Ci-Hai/lingOutils/hanlp-1-2/hanlp-1.2.8.jar\" TestHanToPinyin ".$text;
echo $mainCommand;
print shell_exec($mainCommand);


}
?>
