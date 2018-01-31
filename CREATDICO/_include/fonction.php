<?php
/**
* lit un ficier CSV et construit le tableau PHP correspondant
* @file string 	nom du fichier 
* @return array	tableau PHP correspondant, NULL si erreur
**/
function getLemmaList($langueSource, $strPage, $lemma){
	/* Début traitement du contenu de la page */
	/*$strPage = str_replace("\r\n", chr(0), $strPage); // permet de sauver les retour à la ligne
	 $strPage = strip_tags($strPage);
	 $strPage=strtolower($strPage);
	 $strPage = preg_replace('/\p{P}(?<!\')/',' ', $strPage);
	 $strPage = preg_replace('@[0-9]@',chr(0), $strPage);*/
	if($langueSource&&$strPage&&$lemma){
		$url = "http://localhost/Ci-Hai/Lextoh/index.php?lang=$langueSource&formalism=original&window=5&output=txt&lemma=$lemma&text=".urlencode($strPage);
		//echo 'lemmatisation url = '.$url.'<br/>';
		$lemmas = file_get_contents($url);
		return $lemmas;
	}else return null;
}

function parserConf($filename, $delimiter)
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(count($row)==2){
				$lemma =  $row[0];
				$s_lang = $row[1];
				$l_lemma=array("lemma"=>$lemma, "langs"=>$s_lang);
				array_push($data, $l_lemma);
			}
		}
		fclose($handle);
	}
	return $data;
}


function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
                $data[$row[0]] =  $row[1];
                $data['dico_'.$row[0]] = $row[2];
                $data['trans_'.$row[0]] = $row[3];
        }
        fclose($handle);
    }
    return $data;
}

/*
$l_serv = csv_to_array ('l_serv.csv', "\t");
print_r($l_serv);
*/

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function convert_langCode2_to_langCode3($langCode){
	$langCode = strtolower($langCode);
	switch($langCode){
		case "bg":
			$newLangCode="bul";
			break;
		case "cs":
			$newLangCode="ces";
			break;
		case "da":
			$newLangCode="dan";
			break;
		case "de":
			$newLangCode="deu";
			break;
		case "el":
			$newLangCode="ell";
			break;
		case "en":
			$newLangCode="eng";
			break;
		case "es":
			$newLangCode="spa";
			break;
		case "et":
			$newLangCode="est";
			break;
		case "fi":
			$newLangCode="fin";
			break;
		case "fr":
			$newLangCode="fra";
			break;
		case "ga":
			$newLangCode="gle";
			break;
		case "hr":
			$newLangCode="hrv";
			break;
		case "hu":
			$newLangCode="hun";
			break;
		case "it":
			$newLangCode="ita";
			break;
		case "la":
			$newLangCode="lat";
			break;
		case "lt":
			$newLangCode="lit";
			break;
		case "lv":
			$newLangCode="lav";
			break;
		case "mt":
			$newLangCode="mlt";
			break;
		case "nl":
			$newLangCode="nld";
			break;
		case "pl":
			$newLangCode="pol";
			break;
		case "pt":
			$newLangCode="por";
			break;
		case "ro":
			$newLangCode="ron";
			break;
		case "sk":
			$newLangCode="slk";
			break;
		case "sl":
			$newLangCode="slv";
			break;
		case "sv":
			$newLangCode="swe";
			break;
		default:
			$langCode="*";
	}
	return $newLangCode;

}

function convert_langCode3_to_langCode2($langCode){
	$langCode = strtolower($langCode);
	switch($langCode){
		case "bul":
			$newLangCode="bg";
			break;
		case "ces":
			$newLangCode="cs";
			break;
		case "czs":
			$newLangCode="cs";
			break;
		case "dan":
			$newLangCode="da";
			break;
		case "deu":
			$newLangCode="de";
			break;
		case "ger":
			$newLangCode="de";
			break;
		case "ell":
			$newLangCode="el";
			break;
		case "gre":
			$newLangCode="el";
			break;
		case "eng":
			$newLangCode="en";
			break;
		case "spa":
			$newLangCode="es";
			break;
		case "esp":
			$newLangCode="es";
			break;
		case "est":
			$newLangCode="et";
			break;
		case "fin":
			$newLangCode="fi";
			break;
		case "fra":
			$newLangCode="fr";
			break;
		case "fre":
			$newLangCode="fr";
			break;
		case "gle":
			$newLangCode="ga";
			break;
		case "hrv":
			$newLangCode="hr";
			break;
		case "scr":
			$newLangCode="hr";
			break;
		case "hun":
			$newLangCode="hu";
			break;
		case "ita":
			$newLangCode="it";
			break;
		case "lat":
			$newLangCode="la";
			break;
		case "lit":
			$newLangCode="lt";
			break;
		case "lav":
			$newLangCode="lv";
			break;
		case "mlt":
			$newLangCode="mt";
			break;
		case "nld":
			$newLangCode="nl";
			break;
		case "dut":
			$newLangCode="nl";
			break;
		case "pol":
			$newLangCode="pl";
			break;
		case "por":
			$newLangCode="pt";
			break;
		case "ron":
			$newLangCode="ro";
			break;
		case "rum":
			$newLangCode="ro";
			break;
		case "slk":
			$newLangCode="sk";
			break;
		case "slo":
			$newLangCode="sk";
			break;
		case "slv":
			$newLangCode="sl";
			break;
		case "swe":
			$newLangCode="sv";
			break;
		default:
			$langCode="s";
	}
	return $newLangCode;
	
}

?>
