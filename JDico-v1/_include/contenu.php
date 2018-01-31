
<?php
/* Début de la récupération contenu de la page */
//L'URL de la page à récupérer

$langueSource =$_GET['ls'];
$dico =$_GET['dico'];
$strPage =$_GET['segment'];
$serveur=$_GET['serveur'];

$timeout = 10;

/*switch($langueSource){
case "fra" : $ls="fr"; break;
case "zho" : $ls="zh";	break;
case "eng" : $ls="en";	break;
}*/


/* Fin de larécupération contenu de la page */
/* Début traitement du contenu de la page */
$strPage = str_replace("\r\n", chr(0), $strPage); // permet de sauver les retour à la ligne
$strPage = strip_tags($strPage);
$strPage=strtolower($strPage);
$strPage = preg_replace('/\p{P}(?<!\')/',' ', $strPage);
$strPage = preg_replace('@[0-9]@',chr(0), $strPage);

$newtext = wordwrap($strPage,255, "|");
$segments = explode("|",$newtext);
$nbElements = count($segments);

for($i=0; $i< $nbElements ; $i++ )
{

//$segments[$i] = file_get_contents("http://www.aiakide.net/ttg/index.php?text=".urlencode($segments[$i])."&lang=$ls"); //lemmatisation
$segments[$i] = file_get_contents("http://129.88.64.160/Ci-Hai/lemmatix2/?lang=fra&window=5&output=txt&lemma=xip&text=".urlencode($segments[$i]));
//$texte =str_replace('<br/>','|', $segments[$i]);

//$fichier = fopen ("lemmatisation.txt", "w+");
//fwrite($fichier,$texte);
//fclose ($fichier);
}

?>
