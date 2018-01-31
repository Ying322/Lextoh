<?php
//header("Content-Type: text/html; charset=utf-8");
//include 'traduction.php';
require('traduction.c.php');

$compt=0;

$seg=$strPage;

//$newSeg=preg_split('@([\W]+)@', $seg, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
for($k=0; $k<$nbrWord; $k++)
{
echo $wordArray[$k];
echo ' : ';
echo "<br/>";
echo '<span style="color:red">lemmatisation -> </span>';
$lemms = explode("|", $lemmBackupArray[$k]);
for ($i=0;$i<count($lemms);$i++){
if($i>0){
echo "<br/>-----------<br/>";
}
echo $lemms[$i];
}
echo "<br/>";
echo ' <span style="color:red">POS -> </span>';
if ($posArray[$k]==''){
echo 'POS indisponible dans ce dictionnaire <br/>';
}else{
echo $posArray[$k].'</br>';
}
echo ' <span style="color:red">traduction -> </span>';
if ($lemmArray[$k]==''){
echo 'traduction indisponible dans ce dictionnaire';
}else{
echo $lemmArray[$k];
}
echo "<br/>";
echo "<br/>";
//$seg=preg_replace('/(^|\s|\'|\-)('.$wordArray[$k].')(\W|\s|$)/i','$1'.'<span class="mot_ori" id="tata_'.$compt.'">$2</span><span class="mot_aide" id="toto_'.$compt.'">'.$lemmArray[$k].'</span>'.'$3',$seg);
//$compt++;
}
//$strPage=str_replace($strPage,$seg,$strPage);

//$strPage=str_replace('</head>','<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script><script type="text/javascript" src="js/habillage.js"></script></head>',$strPage);
//unlink('lemmatisation.txt');
//echo $strPage;


?>

