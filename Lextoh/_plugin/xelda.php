<?php
//resultat naif
function lemma_xelda_n ($text, $lang){
	
	$url = "http://localhost/Ci-Hai/LingServices/?serv=morphoanalysis&output=xml&api=1&input=plaintext&tokenType=FST&maType=FSTPOSTag&lang=$lang&text=".urlencode($text);
	$file = file_get_contents($url);
	return $file;
}

//list de arry (key, value)
function lemma_xelda_b ($text){
	$l_arr = Array();
	$xml = new SimpleXMLElement($text);
	foreach ($xml->sentence->{'lexeme-list'}->lexeme as $entry) {
		$arr = Array();
		$form = $entry->{'surface-form'};
		$senseArray = array();
		$senseArray = $entry->{'sense-list'}->sense;
		$firstTokI=$entry['start']+0; //+0 c'est pour transformer element de xml vers int
		$lastTokI=$firstTokI+$entry['length'];
		for($m=0; $m<count($senseArray); $m++){
				$frm=$form;
				$lemma= $senseArray[$m]->{'base-form'};
				$pos= $senseArray[$m]->{'part-of-speech'};
				if ($senseArray[$m]->{'part-of-speech'}['confidence']=='guessed') {
					$arr['form']=$frm.""; //."" c'est pour transformer element de xml vers string
					$arr['lemma']=$lemma."";
					$arr['pos']=$pos."";
					$arr['confidence']='guessed';
					$arr['start']=$firstTokI;
					$arr['end']=$lastTokI;
				} else {
					$arr['form']=$frm."";
					$arr['lemma']=$lemma."";
					$arr['pos']=$pos."";
					$arr['confidence']='reliable';
					$arr['start']=$firstTokI;
					$arr['end']=$lastTokI;
				}
				array_push($l_arr, $arr);
			}
	}
	return $l_arr; 
}


?>
