<?php
// TODO Actuellement, on convertit les chaînes en minuscule avec strtolower, ça ne marche pas pour les caractères accentués
header("Content-Type: text/html; charset=utf-8");

// Fonctions commons
require('_include/fonction.php');

// Plugins 
require ('_plugin/traitementJibiki.php');
require ('_plugin/traitementIATE.php');
require ('_plugin/traitementGlosbe.php');

// Package Simple html dom
require ('../simplehtmldom_1_5/simple_html_dom.php');

// Lire les paramètres
$lang = stripslashes($_POST['lang']?$_POST['lang']:$_GET['lang']);
$lt = stripslashes($_POST['lt']?$_POST['lt']:$_GET['lt']);
$text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
$dico = stripslashes($_POST['dico']?$_POST['dico']:$_GET['dico']);
$serv = stripslashes($_POST['serv']?$_POST['serv']:$_GET['serv']);
$debug = stripslashes($_POST['debug']?$_POST['debug']:$_GET['debug']);
$output = stripslashes($_POST['output']?$_POST['output']:$_GET['output']);
$lemma = stripslashes($_POST['lemma']?$_POST['lemma']:$_GET['lemma']);
$detail = stripslashes($_POST['detail']?$_POST['detail']:$_GET['detail']);
$formule = stripslashes($_POST['formule']?$_POST['formule']:$_GET['formule']);

define('REPERTOIRE_COURANT',getcwd());
define('PAGE_BOOLEAN',!$lang || !$text ||!$output || !$dico || !$serv ||!$lemma||$formule);
define('PRE_FILL_BOOLEAN',$lang || $text || $output || $dico || $lemma || $serv || $formule);

$l_serv = csv_to_array ('_config/l_serv.csv', "\t");
$l_lang = parserConf ('_config/l_lang.csv', "\t");
$l_lemma = parserConf ('_config/l_lemma.conf', "\t");
?>
<script type="text/javascript">
var jArray= <?php echo json_encode($l_lemma); ?>;
var langArray=<?php echo json_encode($l_lang); ?>;
</script>
<?php

/**
 * ====================================================================================
 * Pre-remplire la page
 * ------------------------------------------------------------------------------------
 **/
//PAGE_BOOLEAN : true -> affichier la page avec formulaire, false -> affichage que le resultat (API REST)
if (PAGE_BOOLEAN) {
include('../menu.php');
	echo '<head><title>CREATDICO</title><script src="_design/js/action.js"></script><script src="_design/js/XMLDispaly.js"></script><LINK href="_design/XMLDisplay.css" type="text/css" rel="stylesheet">';
	echo '</head>';
//PRE_FILL_BOOLEAN : true -> la page (les parametres) est deja remplie
//checkOptions : affichage les boutons, les zones texts avec les valeurs pre-remplis.
if (PRE_FILL_BOOLEAN){ 
echo "<body onload=\"checkOptions('$lang', '$lemma', '$dico', '$serv', '$output', '$formule', jArray, langArray)\"  bgcolor=\"#F4f4f4\" text=\"#333333\" link=\"#CC0000\" vlink=\"#CC0000\" alink=\"#CC0000\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">";
}else{
echo '<body bgcolor="#F4f4f4" text="#333333" link="#CC0000" vlink="#CC0000" alink="#CC0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">';
}
}

/**
 * ====================================================================================
 * Affichage des précautions d'emploi (Explications)
 * ------------------------------------------------------------------------------------
 **/
if (PAGE_BOOLEAN) {
echo "<br/>";
	echo "\n".'<style>.attention{background-color:#ffffdd;border:solid 1px #ffff88;}.attention legend{background-color:#ffffbb;border:solid 1px #ffff88;} .attention a, .attention legend {line-height:1;} .attention li {line-height:1.4;}</style>';
	echo "\n".' <fieldset class="attention">
  <legend>Explications</legend>
  <ul>
  	<li>CREATDICO : un serveur paramétrable d\'appel de services des dictionnaires multilingues - fonctionne avec POST ou GET</li> la méthode GET ne marche que lorsque le type de l\'entrée n\'est que du texte, et ne contient pas "trop" de balises html</li>
  	<li>Arguments:
 			<ul>
				<li><i>serv</i> : Serveur de dictionnaire par example PIVAX, IATE, etc.</li>
				<li><i>dico</i> : Dictionnaire (pour le cas de multi-dictionnaire dans un serveur, s\'il n\'y a qu\'un dictioinnaire dans un serveur, la valeur de dictionnaire est égale la valeur de serveur.)</li>
 	  			<li><i>lang</i> : 3 caractères latins en iso 639-2</li>
      			<li><i>lemma</i> : Système de lemmatiseur (Delaf, XeLDA, XIP)</li>
      			<li><i>output</i> : Type de sortie : txt, sectra (minidico de SECTra), dico-Q (minidico d\'OMNIA sur un graphe-Q)</li>
     		 	<li><i>text</i></li>
				<li><i>detail</i> : 1 ou 0 (si 0 affichage des mot-vedette, 1 affichage de tous les détaillés)</li>
      			<li><i>debug</i> : 1 ou 0</li>
      			<li><i>formule</i> : 1 ou 0 (si 1 affichage de cette formulaire, si 0 affichage que des resultat (API REST))</li>
      		</ul>
    </li>
    <li>Plus détail : <a href="./CREATDICO.htm">CREATDICO documentations</a></li>
	</ul>
     </fieldset><!--  class="attention" -->'."<br />\n";
}

/**
 * ====================================================================================
 * traces pour pour debug
 * ------------------------------------------------------------------------------------
 **/
if (PAGE_BOOLEAN || $debug) {
	echo "\n".'<style>.trace{background-color:#eeffee;border:solid 1px #ccffcc}.trace>legend{background-color:#ddffdd;border:solid 1px #ccffcc}</style>';
	echo "\n".'<fieldset class="trace"><legend>Trace :</legend>';
	echo "\n REPERTOIRE_COURANT =".REPERTOIRE_COURANT;
	echo "\n <br/>PARAMS : serv=$serv, dico=$dico, lang=$lang, text=$text, lemma=$lemma, output=$output";
	echo "\n".'</fieldset>'."<br />\n";
}

/**
 * ====================================================================================
 * formulaire
 * ------------------------------------------------------------------------------------
 **/
if (PAGE_BOOLEAN) {
	echo "\n".'<style>fieldset{background-color:#ddffff;border:solid 1px #88ffff;font-size:12px;line-height:1.9} legend{background-color:#bbffff;border:solid 1px #44ffff}</style>';
	echo "\n".'<fieldset class="fieldset"><legend>Désigner le script :</legend>';
	$str = '<form action="index.php" method="POST">';
	/* ------ serveur -----*/
	$str .= "\n".'Serveur : <select name="serv" id="serv"  onchange="updateDicoList(this.value, langArray)">';
	$str .= "\n".'<option value="">Choisir un serveur</option>';
	foreach($l_serv as $k=>$v){
		if(!startsWith($k, 'dico_')&&!startsWith($k, 'trans_')) {
	    	$str .= "\n"."<option value=\"$k\">$k</option>";
		}
	}
	$str .=  '</select>';
	/* ------ dictionnaire -----*/
	$str .= "\n".'Dictionnaire : <select name="dico" id="dico" onchange="updateLangList(this.value)">';
	$str .= "\n".'<option value="">Choisir un serveur d\'abord</option>';
	$str .= "\n".'</select>';
	/* ------ langue -----*/
	$str .= "\n".'Langue source : <select name="lang" id="lang" onchange="updateLemmaList(this.value, jArray)">';
	$str .= "\n".'<option value="">Choisir un dictionnaire d\'abord</option>';
	$str .= "\n".'</select>';
	/* ------ langue Cible-----*/
	$str .= "\n".'Langue cible : <select name="lt" id="lt" >';
	$str .= "\n".'<option value="">Choisir un dictionnaire d\'abord</option>';
	$str .= "\n".'</select>';
	/* ------ lemmatiseur -----*/
	$str .= "\n".'Lemmatiseur : <select name="lemma" id="lemma">';
	$str .= "\n".'<option value="">Choisir une langue d\'abord</option>';
	$str .=  '</select>';
	/* ------ type sortie -----*/
    $str .= "\n".'<span>';
    $loutput = array('txt','sectra','dico-q');
    $str .= "\n".'Type de sortie : <select name="output" id="output" >'; 
    foreach($loutput as $opt) {
      $str .= "\n".'<option value="'.$opt.'">'.$opt.'</option>';
    }
    $str .= "\n".'</select></span><br/>';
    /* ------ detail -----*/
    $str .= "\n".'<span>affichier les détaillés : ';
    $str .= "\n".'<input type="checkbox" id="detail" name="detail" value="detail" '.((@$_REQUEST['detail']=='detail')?'checked':'').' /> </span>';
	/* ------ text -----*/
    $str .= "\n".'<br /> text : <textarea name="text" id="text" style="left:100px;vertical-align:top" cols="80" rows="10">'.(isset($text)?$text:'').'</textarea>';
    $str .= "\n".'<br /> <input type="submit" name="lancer" value="lancer" /> ';
    /* ------ reaffichage de formulaire -----*/
    $str .= "\n".'<span>réafficher le formulaire : ';
  	$str .= "\n".'<input type="checkbox" id="formule" name="formule" value="formule" '.((@$_REQUEST['formule']=='formule')?'checked':'').' /> </span>'; 
	echo $str;
	echo "\n".'</fieldset>'."<br />\n";
}

/**
 * ====================================================================================
 * traitement
 * ------------------------------------------------------------------------------------
 **/
$lemmaList = getLemmaList($lang,$text,$lemma);

if($lemmaList){
	if($serv=='wiktionary'){
		$dico_Json = getTranslationListGlosbe($lemmaList, $dico, $serv, $lang, $lt);
	} else if($serv=='iate'){
		$translationLemmaList = getTranslationListIATE($lemmaList, $dico, $serv, $lang, $l_serv);
		//print_r($translationLemmaList);
		if($detail){
			$tragetListArray = getTragetInfoListIATE($translationLemmaList, $serv, $dico, $l_serv);
		}
		
	}else{
		$translationLemmaList = getTranslationListJibiki($lemmaList, $dico, $serv, $lang, $l_serv);
		if($detail){
			$tragetListArray = getTragetInfoListJibiki($translationLemmaList, $serv, $dico, $l_serv);
		}
	}
}

/**
 * ====================================================================================
 * affichage simple
 * ------------------------------------------------------------------------------------
 **/
if(PAGE_BOOLEAN){
	echo "\n".'<style>.result{background-color:#FBEFF8;border:solid 1px #F6CEEC;font-size:12px;line-height:1.9} .result>legend{background-color:#F8E0F1;border:solid 1px #F5A9E1}</style>';
	echo "\n".'<form id="resultsimple">';
	echo "\n".'<fieldset class="result"><legend>Résultat :</legend>';
}

if($serv=='wiktionary'){
	foreach($dico_Json as $dico){
		$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($dico),RecursiveIteratorIterator::SELF_FIRST);
		echo "<span style=\"color:blue\">lemme : ".$dico['lemme']."<br/></span>";
		foreach ($iterator as $key => $val) {
    		if(is_array($val)) {
        		if(strcasecmp($key,'phrase')===0){
        			echo "<span style=\"color:red\">traduction : </span><br/>";
        		}else if(strcasecmp($key,'meanings')===0){
        			echo "<span style=\"color:red\">sens : </span><br/>";
        		}else if(strcasecmp($key,'authors')===0){
        			echo "<span style=\"color:red\">origine : </span>";
        		}
    		} else {
    			if(strcasecmp($key,'0')==0){
    				echo "$val<br/>";
    			}else{
    				if(strcasecmp($key,'meaningId')==0||strcasecmp($key,'lemme')==0){
    					//echo "<span style=\"color:red\">$key : </span>$val<br/>";
    				}else{
       		 			echo "$key => $val<br/>";
       		 		}
       		 	}
    		}
    	}
	}
} else {
if(!$detail){
	foreach($translationLemmaList as $arr){
		$lemma = $arr[0];
		$pos = $arr[1];
		$translationList = $arr[2];
		echo "lemma = <span style=\"color:blue\"> $lemma </span><br/>";
		echo "translation = <br/> <span style=\"color:red\"> ";
		foreach($translationList as $trans){
			echo $trans.'<br/>';
		}
		echo '</span>';
		echo '<br/>';
	}		
}else {
	for($i=0; $i<count($tragetListArray); $i++){
		//$transArr=array("lemme"=>$lemma, "pos"=>$pos, "traduction"=>$term, "langue"=>$lang, "xml"=>$newtrad);
		$arr = $tragetListArray[$i];
		$arr_lemma = $arr['lemme'];
		$arr_pos = $arr['pos'];
		$arr_term = $arr['traduction'];
		$arr_lang = $arr['langue'];
		$arr_xml = $arr['xml'];
		$arr_html = $arr['html'];
		if($arr_xml){
			if(strpos($arr_xml,'<?xml')!==false){
				echo "<span style=\"color:#380000;\"><b>lemma</b> = $arr_lemma, <b>part-of-speech de lemma</b> = $arr_pos, <b>traduction</b> = $arr_term, <b>langue de traduction</b> = $arr_lang, <b>Voici les détaillés de traduction</b> : </span>";
				//regourpe xml
				$arr_xml = trim($arr_xml);
				$arr_xml = preg_replace('/s(?=s)/', '', $arr_xml); // remove any doubled-up whitespace
				$arr_xml = str_replace(PHP_EOL, '', $arr_xml); // remove any return	
				if($formule){
					echo "\n<div id=\"transltionDetail_$i\">";
					?>
					<script type="text/javascript">
					LoadXMLString('transltionDetail_<?php echo $i ?>', '<?php echo $arr_xml ?>');
					</script>
					<?php 
					echo "\n".'</div>';
				}else{
					echo '<br/>';
					echo $arr_xml;
					echo '<br/>';
				}
			}else{
				echo "<span style=\"color:#380000;\"><b>lemma</b> = $arr_lemma, <b>part-of-speech de lemma</b> = $arr_pos, <b>traduction</b> = $arr_term </span>";
			}
		}
		if($arr_html){
			echo "<span style=\"color:#380000;\"><b>lemma</b> = $arr_lemma, <b>part-of-speech de lemma</b> = $arr_pos, <b>traduction</b> = $arr_term, <b>langue de traduction</b> = $arr_lang, <b>Voici les détaillés de traduction</b> : </span>";
			echo $arr_html;
		}
		echo '<br/>';
	}
//	print_r($tragetInfoArray);
}
}
if(PAGE_BOOLEAN){
	echo "\n".'</fieldset>'."<br />\n".'</form>';
}

/**
 * ====================================================================================
 * exemples
 * ------------------------------------------------------------------------------------
 **/
if(PAGE_BOOLEAN){
	echo "\n".'<style>.exemple{background-color:#EFEFFB;border:solid 1px #CED8F6;font-size:12px;line-height:1.9} .exemple>legend{background-color:#E0E6F8;border:solid 1px #A9BCF5}</style>';
	echo "\n".'<fieldset class="exemple"><legend>Exemples :</legend>';
	$str = '<dl>';
	$str .='<dt> Exemple 1, du texte, dans un formulaire, français comme langue source, xip comme lemmatiseur, avec dictionnaire CommonUNLDict de pivax, txt comme sortie  : </dt><dd><a href="?text=bonjour, tu vas bien?&lemma=xip&lang=fra&serv=pivax&dico=CommonUNLDict&formule=1&output=txt">     http://46.105.41.94/Ci-Hai/CREATDICO/index.php?text=bonjour, tu vas bien?&lemma=xip&lang=fra&serv=pivax&dico=CommonUNLDict&formule=1&output=txt</a></dd>';
	$str .= '<dt> Exemple 2, du texte, dans un formulaire, chinois comme langue source, jieba comme segmenteur, avec dictionnaire Cedict de papillon, txt comme format de sortie, avec window comme 5 : </dt><dd><a href="?text=今天的天气很好&lemma=jieba&serv=papillon&lang=zho&dico=Cedict&output=txt&formule=1">     http://46.105.41.94/Ci-Hai/CREATDICO/index.php?text=今天的天气很好&lemma=jieba&serv=papillon&lang=zho&dico=Cedict&output=txt&formule=1</a></dd>';
	$str .= '<dt> Exemple 3, du texte, dans un formulaire, français comme langue source, anglais comme langue cible, xelda comme lemmatiseur, avec dictionnaire wiktionary, txt comme format de sortie: </dt><dd><a href="?text=C\'est la couleur rouge.&lemma=xelda&serv=wiktionary&lang=fra&lt=eng&dico=wiktionary&output=txt&formule=1">     http://46.105.41.94/Ci-Hai/CREATDICO/index.php?text=C\'est la couleur rouge.&lemma=xelda&serv=wiktionary&lang=zho&lt=eng&dico=wiktionary&output=txt&formule=1</a></dd>';
	$str .= '</dl>';
	echo $str;
	echo "\n".'</fieldset>'."<br />\n";
}

/**
 * ====================================================================================
 * fin de la page
 * ------------------------------------------------------------------------------------
 **/
if(PAGE_BOOLEAN){
	echo "</body>";
}

?>