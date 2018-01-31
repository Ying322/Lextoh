<?php
  // TODO Actuellement, on convertit les chaînes en minuscule avec strtolower, ça ne marche pas pour les caractères accentués
  header("Content-Type: text/html; charset=utf-8"); 
  // BD connex
  include('../connex.php'); 
  
  // Fonctions commons
  include('../functions.php'); 
  
  //lemmatiseurs
  include('_include/syst_lemma.php');
  
  //creation de forme lemmatix
  include('_include/lemmatix.php');
  
  //plugins
  include('_plugin/xelda.php');
  include('_plugin/xip.php');
  include('_plugin/delaf.php');
  include('_plugin/ariane.php');
  include('_plugin/jieba.php');
  include('_plugin/stanford.php');
  



define('REPERTOIRE_COURANT',getcwd()); 

  // Lire les paramètres
  $lang = stripslashes($_POST['lang']?$_POST['lang']:$_GET['lang']);
  $text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
  $window = stripslashes($_POST['window']?$_POST['window']:$_GET['window']);
  $debug = stripslashes($_POST['debug']?$_POST['debug']:$_GET['debug']);
  $output = stripslashes($_POST['output']?$_POST['output']:$_GET['output']);
  $lemma = stripslashes($_POST['lemma']?$_POST['lemma']:$_GET['lemma']);
  $formule = stripslashes($_POST['formule']?$_POST['formule']:$_GET['formule']);
  $formalism = stripslashes($_POST['formalism']?$_POST['formalism']:$_GET['formalism']);
  $advmode = stripslashes($_POST['advmode']?$_POST['advmode']:$_GET['advmode']);

define('PAGE_BOOLEAN',!$lang || !$text ||!$output ||!$formalism||!$lemma||$formule); 
define('PRE_FILL_BOOLEAN',$lang && $text && $output && $formalism && $lemma && $formule); 
$l_lemma = parserConf ('_config/l_lemma.conf', "\t");
$l_pos = parserConf ('_config/l_pos.conf', "\t");
$connex = connex();
if(strcasecmp($formalism, 'grapheq') == 0 ||strcasecmp($formalism, 'graphe-q') == 0 ){
	$formalism='graphe-Q';
}

  
 /**
 * ====================================================================================
 * Pre-remplire la page
 * ------------------------------------------------------------------------------------
**/
//PAGE_BOOLEAN : true -> affichier la page avec formulaire, false -> affichage que le resultat (API REST)
if (PAGE_BOOLEAN) { 
include('../menu.php');
echo '<head><title>LEXTOH</title><script src="_include/js/action.js"></script><script src="_include/js/XMLDispaly.js"></script><LINK href="XMLDisplay.css" type="text/css" rel="stylesheet"><link rel="stylesheet" href="core.css" type="text/css">';
?>
<script type="text/javascript">
var jArray= <?php echo json_encode($l_lemma); ?>;
</script>
<?php
echo '</head>';
//PRE_FILL_BOOLEAN : true -> la page (les parametres) est deja remplie
//checkOptions : affichage les boutons, les zones texts avec les valeurs pre-remplis.
if (PRE_FILL_BOOLEAN){ 
echo "<body onload=\"checkOptions('$lang', '$lemma', '$output', '$formule', jArray, '$formalism')\" bgcolor=\"#F4f4f4\" text=\"#333333\" link=\"#CC0000\" vlink=\"#CC0000\" alink=\"#CC0000\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\" >";
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
  	<li>LEXTOH : un serveur paramétrable d\'appel de services des lemmatiseurs multilingues - fonctionne avec POST ou GET</li> la méthode GET ne marche que lorsque le type de l\'entrée n\'est que du texte, et ne contient pas "trop" de balises html</li>
  	<li>Arguments:
 			<ul>
 	  			<li><i>lang</i> : 3 caractères latins en iso 639-2</li>
      			<li><i>lemma</i> : Système de lemmatiseur (<em>Delaf</em>, <em>XeLDA</em>, <em>XIP</em>, <em>Jieba</em>, <em>ariane-heloise</em>, <em>stanford-corenlp</em>, <em>stanford-segmenter</em>)</li>
      			<li><i>output</i> : Format de sortie (<tt>txt</tt>, <tt>JSON</tt>, <tt>xml</tt>)</li>
      			<li><i>formalism</i> : Grammaire formelle (<tt>original</tt>,<tt>graphe-Q</tt>)
     		 	<li><i>text</i></li>
      			<li><i>debug</i> : 1 ou 0</li>
      			<li><i>window</i>(delaf) : Offset des résultats de tokenisation (ex. window=3, text=pomme de terre, le résultat de tokenisation est "pomme", "de", "terre",  donc le système va vérifier les lemmatisations pour "pomme", "pomme de", "pomme de terre", "de", "de terre", "terre".)</li>
      			<li><i>formule</i> : 1 ou 0 (si 1 affichage de cette formulaire, si 0 affichage que des resultat (API REST))</li>
      			<!--li><i>advmode</i> : 1 ou 0 (si 1 mode avancé (affichage des résultats de native à final), si 0 mode simple (affichage de résultat final))</li-->
      		</ul>
    </li>
    <li>Langues supportées : <ul>
    	<li>XeLDA : fra, eng, cse, dan, nld, fin, deu, ell, hun, ita, nob, pol, por, ron, rus, spa, tur</li>
    	<li>Xip : fra, eng</li>
    	<li>Delaf : fra, eng</li> 
    	<li>Jieba : zho (segmenteur chinois)</li>
    	<li>ariane-heloise : fra, eng</li>
    	<li>stanford-corenlp : eng</li>
    	<li>stanford-segmenter : zho</li>
    	</ul>
    </li>
    <li>Plus détail : <a href="./Lextoh.htm">Lextoh documentations</a></li>
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
	echo "\n <br/>PARAMS : lang=$lang, text=$text, window=$window, lemma=$lemma, output=$output, formalism=$formalism";
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
	/* ------ lemmatiseur -----*/
	$str .= "\n".'lemmatiseur : <select name="lemma" id="lemma"  onchange="selectLemma(this.value, jArray)">';
    $str .= "\n".'<option value="">Choisir un lemmatiseur</option>';
    foreach($l_lemma as $lemmas){
    	$str .= "\n"."<option value=\"$lemmas[lemma]\">$lemmas[lemma]</option>";
    }
    $str .=  '</select>';  
    /* ------ langue -----*/
    $str .= "\n".'  langue source : <select name="lang" id="lang" onchange="showAlert(this.value)">';
    $str .= "\n".'<option value="">Choisir un lemmatiseur d\'abord</option>';
    $str .= "\n".'</select>';
    /* ------ formalisme de la sortie -----*/
    $str .= "\n".'<span>';
    $l_formalism = array('original','graphe-Q');
    $str .= "\n".'  formalisme de sortie : <select name="formalism" id="formalism" >'; 
    foreach($l_formalism as $opt) {
      $str .= "\n".'<option value="'.$opt.'">'.$opt.'</option>';
    }
    $str .= "\n".'</select></span>';
    /* ------ format sortie -----*/
    $str .= "\n".'<span>';
    $loutput = array('xml','txt','json');
    $str .= "\n".'  format de sortie : <select name="output" id="output" >'; 
    foreach($loutput as $opt) {
      $str .= "\n".'<option value="'.$opt.'">'.$opt.'</option>';
    }
    $str .= "\n".'</select></span><br/>';
    /* ------ window -----*/
    $str .= "\n".'<span id="win" hidden> window : <textarea name="window" id="window" style="left:1px;vertical-align:top; resize: none;" cols="1" rows="1" >'.(isset($window)?$window:'').'</textarea></span>';
    /* ------ text -----*/
    $str .= "\n".'<br /> texte : <textarea name="text" id="text" style="left:100px;vertical-align:top" cols="80" rows="10">'.(isset($text)?$text:'').'</textarea>';
    $str .= "\n".'<br /> <input type="submit" name="lancer" value="lancer" /> ';
    /* ------ reaffichage de formulaire -----*/
    $str .= "\n".'<span>réafficher le formulaire : ';
  	$str .= "\n".'<input type="checkbox" id="formule" name="formule" value="formule" '.((@$_REQUEST['formule']=='formule')?'checked':'').' /> </span>'; 
  	/* ------ format personnalisé -----*/
    $str .= "\n".'<span>format personnalisé : ';
  	$str .= "\n".'<input type="checkbox" id="persform" name="persform" onclick="persFormat()"/> </span>'; 
 // 	$str .= '</form>';
  	/* ------ mode avancée -----*/
    $str .= "\n".'<span>mode avancé : ';
  	$str .= "\n".'<input type="checkbox" id="advmode" name="advmode" onclick="modeAvance()"/> </span>'; 
  	$str .= '</form>';
    echo $str;
	echo "\n".'</fieldset>'."<br />\n";
	
}

/**
 * ====================================================================================
 * format personnalisé 
 * ------------------------------------------------------------------------------------
**/
if(PAGE_BOOLEAN){
	echo "\n".'<style>.pers{background-color:#F8ECE0;border:solid 1px #F5D0A9;font-size:12px;line-height:1.9} .pers>legend{background-color:#F6E3CE;border:solid 1px #F7BE81}</style>';
	echo "\n".'<span id="personal" hidden><fieldset class="pers" ><legend>Format personnalisé :</legend>';
	/* ------ formalisme et format de la sortie -----*/
	echo "\n".'<span>';
	$l_formalism_format = array('original_xml', 'original_txt', 'origianl_json', 'graphe-Q_xml', 'graphe-Q_txt', 'graphe-Q_json');
	echo "\n".'  formalisme et format : <select name="formalism_format" id="formalism_format" onchange="setFormatPersoFormulaire(this.value)">';
	foreach($l_formalism_format as $opt) {
		echo "\n".'<option value="'.$opt.'">'.$opt.'</option>';
	}
	echo "\n".'</select></span>';
	
	
	echo "\n".'</fieldset>'."<br />\n";
	echo '</span>';
}  


/**
 * ====================================================================================
 * traitement
 * ------------------------------------------------------------------------------------
**/

  //    $text = "cela va bien.";
  //    $window = 5;
  //    $debug = 1;
  //    $lang = 'fra';
  //    $output = 'graphe-Q';
    //  $lemma = 'DELAF';
  // Normalisation du texte
 /*
function traitement($text, $window, $debug, $lang, $output, $lemma ){
  $text = preg_replace('/\s+/', ' ', $text);  // Blancs multiples -> 1 seul espace
  $text = preg_replace('/\\\\/', '', $text);  // Enlever les antislashs
  $connex = connex();
  $str_output="";
  
  $once = 1;
  if(strtolower($lemma)=='xelda'){
        	$arr=lemma_xelda ($once, $str_output, $output, $text, $lang);
        	$once=$arr[1];
        	$str_output=$arr[0];
	}
  if(strtolower($lemma)=='xip'){
        	$arr=lemma_xip($once, $str_output, $output, $text, $lang);
        	$once=$arr[1];
        	$str_output=$arr[0];
	}
  if(strtolower($lemma)=='delaf'){
  	$tokens = preg_split('/([\.\s\/\\*+$€&~#"\'\{\[\(|`_@)\]°}=%!:;,?-]+)/', $text, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);  // Tokenisation
	  foreach($tokens as $i=>$token) { // Pour chaque token
		for($wR=0; $wR<$window; ++$wR)  {// Pour chaque offset (vers la droite)
		  if($i+$wR<count($tokens)) {  // Si la fenêtre est valide
			$firstTokI = $i;// - $wL;
			$lastTokI = $i + $wR;
			$form = '';
			for($j=$firstTokI; $j<=$lastTokI; ++$j) // Composition d'une forme
			  $form .= $tokens[$j];
		
				$arr=lemma_delaf ($once, $str_output, $output, $form, $lang, $firstTokI,$lastTokI,$wR);
				$once=$arr[1];
				$str_output=$arr[0];
		  } // fin if($i+$wR<count($tokens))
		  } // fin for($wR=0; $wR<$window; ++$wR)
		  } // fin foreach
      }
 //   $str_output =  get_output($str_output, $output); 
    $str_output = compl_output($once, $output, $str_output);
//	print $str_output;
  mysql_close($connex);
  return $str_output; 
  }
  */
  
  function getResultNaif($text, $window, $lang, $lemma){
  	$text = preg_replace('/\s+/', ' ', $text);  // Blancs multiples -> 1 seul espace
  	$text = preg_replace('/\\\\/', '', $text);  // Enlever les antislashs
  
  	$str_output="";

  	if(strtolower($lemma)=='xelda'){
        $str_output=lemma_xelda_n ($text, $lang);
	}
	
	if(strtolower($lemma)=='xip'){
    	$str_output=lemma_xip_n($text, $lang);
	}
	
	if(strtolower($lemma)=='delaf'){
		$str_output=lemma_delaf_n($text, $lang, $window);
	}
	
	if(strtolower($lemma)=='ariane-heloise'){
		$str_output=lemma_ariane_n($text, $lang);
	}
	
	if(strtolower($lemma)=='jieba'){
		$str_output=lemma_jieba_n($text, $lang);
	}
	
	if(strtolower($lemma)=='stanford-segmenter'||strtolower($lemma)=='stanford-corenlp'){
		$str_output=lemma_stanford_n($text, $lang);
	}
	
  	return $str_output; 
  	
  }
  
   function getResultBrut($res_n, $lemma, $text, $lang){
  	$str_output="";

  	if(strtolower($lemma)=='xelda'){
        $str_output=lemma_xelda_b ($res_n);
	}
	
	if(strtolower($lemma)=='xip'){
    	$str_output=lemma_xip_b($res_n, $text);
	}
	
	if(strtolower($lemma)=='delaf'){
		$str_output=lemma_delaf_b($res_n, $text);
	}
	
	if(strtolower($lemma)=='ariane-heloise'){
		$str_output=lemma_ariane_b($res_n, $text);
	}
	
	if(strtolower($lemma)=='jieba'){
		$str_output=lemma_jieba_b($res_n, $text);
	}
	
	if(strtolower($lemma)=='stanford-segmenter'||strtolower($lemma)=='stanford-corenlp'){
		$str_output=lemma_stanford_b($res_n, $lang, $text);
	}
	
  	return $str_output; 
  	
  }
  
  
  /**
 * ====================================================================================
 * affichage simple
 * ------------------------------------------------------------------------------------
**/
$res_n='';
$res_b='';
$res_li='';
$res_lf='';
$res_n=getResultNaif($text, $window, $lang,  $lemma);
$res_b=getResultBrut($res_n, $lemma, $text, $lang);
$res_li=getResultLemmatix($res_b, $lang, $lemma);
$res_lf=getResultLemmatixFinal($res_li, $lemma, $lang, $l_pos);
//$res=traitement($text, $window, $debug, $lang, $output, $lemma);
$res_final=getResultFinal($res_lf, $formalism, $output);
if(PAGE_BOOLEAN){
	echo "\n".'<style>.result{background-color:#FBEFF8;border:solid 1px #F6CEEC;font-size:12px;line-height:1.9} .result>legend{background-color:#F8E0F1;border:solid 1px #F5A9E1}</style>';
	echo "\n".'<form id="resultsimple">';
	echo "\n".'<fieldset class="result"><legend>Résultat :</legend>';
	if($res_final=='ERROR'){
		if($_SERVER['QUERY_STRING']==0){
			echo '';
		}else {
			echo 'ERROR : please use advanced mode and check your parameters and your configure file.';
		}
	}else{
		echo $res_final;
	}
	echo "\n".'</fieldset>'."<br />\n".'</form>';
}else if(!PAGE_BOOLEAN){
	echo $res_final;
}  

  /**
 * ====================================================================================
 * affichage avancée
 * ------------------------------------------------------------------------------------
**/
if(PAGE_BOOLEAN){
	echo "\n".'<style>.results{background-color:#FBEFF8;border:solid 1px #F6CEEC;font-size:12px;line-height:1.9;word-break: break-all;word-wrap: break-word;} .results>legend{background-color:#F8E0F1;border:solid 1px #F5A9E1}</style>';
	echo "\n".'<style>.resultsButton{background-color:#F7819F;border:solid 1px #F5A9E1;}</style>';
	echo "\n".'<form id="resultavance" hidden="hidden">';
	echo "\n".'<fieldset class="results"><legend>Mode avancée :</legend>';
	echo "\n".'<button id="naive" type="button" value="Sortie naïve" onclick="getResulatAvance(this.value)" class="resultsButton" style="font:italic bold 100% arial">Étape 1 : Sortie native</button>';
	echo "\n".'<button id="brute" type="button" value="Sortie brute" onclick="getResulatAvance(this.value)" class="resultsButton">Étape 2 : Sortie brute</button>';
	echo "\n".'<button id="lemmatint" type="button" value="Lemmatix interm" onclick="getResulatAvance(this.value)" class="resultsButton">Étape 3 : Sortie en "lemmatix interm"</button>';
	echo "\n".'<button id="lemmatfinal" type="button" value="Lemmatix final" onclick="getResulatAvance(this.value)" class="resultsButton">Étape 4 : Sortie en "lemmatix final"</button>';
	echo "\n".'<button id="final" type="button" value="Sortie finale" onclick="getResulatAvance(this.value)" class="resultsButton">Étape 5 : Sortie fusionnée</button>';
	echo "\n".'<div id="resNaive" >';
	if($lemma!='delaf'&& $lemma!='jieba'){
		echo $res_n;
	}
	else{
		print_r ($res_n);
	}
	echo "\n".'</div>';
	echo "\n".'<div id="resBrute" hidden="hidden">';
	foreach($res_b as $arr) {
      print_r($arr);
      echo '<br/>';
    }
	echo "\n".'</div>';
	echo "\n".'<div id="resLemmatint" hidden="hidden">';
	$temp_res_li = $res_li;
	$temp_res_li = str_replace('\'', '\\\'', $temp_res_li);
	//echo $temp_res_li;
	if(!empty($temp_res_li)){
	?>
	<script type="text/javascript">
	LoadXMLString('resLemmatint', '<?php echo $temp_res_li ?>');
	</script>
	<?php
	}
	echo "\n".'</div>';
	echo "\n".'<div id="resLemmatfinal" hidden="hidden">';
	$temp_res_lf = $res_lf;

	if(!empty($temp_res_lf)){
		if($temp_res_lf=='ERROR'){
			echo 'Error, please check last step. Or wrong parameter for formalism or output';
		}else{
			$temp_res_lf = explode("?>", $temp_res_lf)[1]; /*enlever la notation de xml, cette notation est fait par la methode asXML(), apres la notation, il y a "retour" a la fin de la ligne. Si on envoye ce string avec "retour" a javascript, ca devient un vrais retour, mais pas = '\r'. puis javascript tombe dans une erreur*/
			$temp_res_lf = trim($temp_res_lf); /*enlever "retour"*/
			$temp_res_lf = str_replace('\'', '\\\'', $temp_res_lf);
	?>
	<script type="text/javascript">
	LoadXMLString('resLemmatfinal', '<?php echo $temp_res_lf ?>');
	</script>
	<?php
		}
	}
	echo "\n".'</div>';
	echo "\n".'<div id="resFinal" hidden="hidden">';
	if(strcasecmp($output, 'xml')!==0){
		echo $res_final;
	}else{
		$temp_res_final=$res_final;
		echo 'temp res final : '.$temp_res_final;
		if(!empty($temp_res_final)){
			if($temp_res_final=='ERROR'){
				echo 'Configuration error, please check your LEXTOH/_config/l_pos.conf file';
			}else{
				$temp_res_final = explode("?>", $temp_res_final)[1]; /*enlever la notation de xml, cette notation est fait par la methode asXML(), apres la notation, il y a "retour" a la fin de la ligne. Si on envoye ce string avec "retour" a javascript, ca devient un vrais retour, mais pas = '\r'. puis javascript tombe dans une erreur*/
				$temp_res_final = trim($temp_res_final); /*enlever "retour"*/
				$temp_res_final = str_replace('\'', '\\\'', $temp_res_final);
				?>
				<script type="text/javascript">
				LoadXMLString('resFinal', '<?php echo $temp_res_final ?>');
				</script>
				<?php
			}
		}
	}
	echo "\n".'</div>'.'</fieldset></form>';
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
	$str .='<dt> Exemple 1, du texte, dans un formulaire, français comme langue, <em>xip</em> comme lemmatiseur, <tt>json</tt> comme format de sortie : </dt><dd><a href="?text=bonjour, tu vas bien?&lemma=xip&lang=fra&output=json&formalism=original&formule=1">     http://46.105.41.94/Ci-Hai/Lextoh/index.php?text=bonjour,%20tu%20vas%20bien?&lemma=xip&lang=fra&output=json&formalism=original&formule=1</a></dd>';
	$str .= '<dt> Exemple 2, du texte, dans un formulaire, français comme langue, <em>delaf</em> comme lemmatiseur, <tt>txt</tt> comme format de sortie, avec window comme 5 : </dt><dd><a href="?text=bonjour, tu vas bien?&lemma=delaf&window=5&lang=fra&output=txt&formalism=original&formule=1">     http://46.105.41.94/Ci-Hai/Lextoh/index.php?text=bonjour, tu vas bien?&lemma=delaf&window=5&lang=fra&output=txt&formalism=original&formule=1</a></dd>';
	$str .= '<dt> Exemple 3, du texte, dans un formulaire, chinois comme langue, <em>jieba</em> comme segmenter, <tt>txt</tt> comme format de sortie, avec trace pour le débogage : </dt><dd><a href="?text=我从来不认为这是一个好的解决办法。&lemma=jieba&lang=zho&output=txt&formalism=original&formule=1&debug=1">     http://46.105.41.94/Ci-Hai/Lextoh/index.php?text=我从来不认为这是一个好的解决办法。&lemma=jieba&lang=zho&output=txt&formalism=original&formule=1&debug=1</a></dd>';
	$str .= '<dt> Exemple 4, du texte, sans formulaire, anglais comme langue, <em>xelda</em> comme lemmatiseur, <tt>graphe-Q</tt> comme format de sortie : </dt><dd><a href="?text=Hello, this is my first test.&lemma=xelda&lang=eng&output=txt&formalism=graphe-q">     http://46.105.41.94/Ci-Hai/Lextoh/index.php?text=Hello, this is my first test.&lemma=xelda&lang=eng&output=txt&formalism=graphe-q</a></dd>';
	$str .= '</dl>';
	echo $str;
	echo "</fieldset>";
}

 /**
 * ====================================================================================
 * fin de la page
 * ------------------------------------------------------------------------------------
**/ 
if(PAGE_BOOLEAN){
echo "</body>";
}

mysql_close($connex);
?>
