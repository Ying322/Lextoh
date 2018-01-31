
<?php

function getTranslationListGlosbe($lemmaList, $dico, $serv, $lang, $lt){
	$newLemmaList = explode("form=", $lemmaList);
	$arr_trad = array();
	for($i=1; $i<count($newLemmaList); $i++){
		$lemma = $newLemmaList[$i];
		//echo $lemma;
		$lemma = split( ', lemma=', $lemma)[1];
		$lemma = split( ', pos=', $lemma)[0];
		$url = "https://glosbe.com/gapi/translate?from=$lang&dest=$lt&format=json&phrase=$lemma&pretty=true";
		//echo 'url = '.$url.'<br/>';
		$trad = file_get_contents($url);
		//echo 'trad = '.$trad.'<br/>';
		$json = json_decode($trad, true);
		$authors = $json['authors'];
		$arr_id = array();
		foreach ($authors as $key=>$value){
			if(stristr($value['N'], $dico)!==false){
				$arr_id[$key]=$value['N'];
				
			}
		}
		/*
		echo '<br/>';
		print_r($arr_id);
		echo '<br/>';
		*/
		$newJson = array();
		foreach($json['tuc'] as $value){
			if (array_key_exists($value['authors'][0], $arr_id)){
				$keyId = $value['authors'][0];
				$value['authors'][0] = $arr_id[$keyId];
				array_push($newJson, $value);
			}
		}
		/*
		echo '<br/>';
		print_r($newJson);
		echo '<br/>';
		*/
		$newJson['lemme']=$lemma;
		array_push($arr_trad,$newJson);
	}
	return $arr_trad;
}

function getTragetInfoListGlosbe($traductionArray, $serv, $dico, $l_serv){
	
 		
}
?>
