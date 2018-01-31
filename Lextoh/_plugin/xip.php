<?php
function lemma_xip_n ($text, $lang){
	$url = "http://atoum.imag.fr/getalp/Services/Web/CREATDICO/callXip.php?lang=$lang&text=$text";
	$res= file_get_contents($url);
	return $res;
}
//list de arry (key, value)
function lemma_xip_b ($text, $textOrig){
	$l_arr = Array();
	$l_str = explode('>TOP', $text);
	for($i=1; $i<count($l_str); $i++){
		$str=$l_str[$i];
		$lemArray = explode('},',$str);
		$offset = 0;
		foreach($lemArray as $entry){
			$entry.="}";
			$entryArr = Array();
			$firstTokI = 0;
			$lastTokI = 0;
			$term = '';
			$form = '';
			$lemma = '';
			$allTag = '';
			$type = '';
			$arr = explode('{',$entry);
			$l = count($arr);
			$pos = $arr[$l-2];
			$confidence = strpos($arr[$l-1], '+Guessed+');
			$term = explode('^', $arr[$l-1]);
			$form = $term[0];
			$lemma = $term[1];	
			$allTag = explode(':}',$term[2])[0];		
			$firstTokI = strpos($textOrig, $form, $offset);
			$lastTokI = $firstTokI+strlen($form);
			$offset = $lastTokI;
			//echo ' firstTokI'.$firstTokI.', lastTokI'.$lastTokI;
			if($confidence===false){
				$entryArr['form']=$form; 
				$entryArr['lemma']=$lemma;
				$entryArr['pos']=$pos;
				$entryArr['allTag']=$allTag;
				if(stripos($allTag,"ABBR")){
					$entryArr['type']='abbreviation';
				}else if(stripos($allTag,"ACRON")){
					$entryArr['type']='acronym';
				}else if(stripos($allTag,"ABR")){
					$entryArr['type']='abbreviation';
				}
				$entryArr['confidence']='reliable';
				$entryArr['start']=$firstTokI;
				$entryArr['end']=$lastTokI;
			}else {
				$entryArr['form']=$form; 
				$entryArr['lemma']=$lemma;
				$entryArr['pos']=$pos;
				$entryArr['allTag']=$allTag;
				if(stripos($allTag,"ABBR")){
					$entryArr['type']='abbreviation';
				}else if(stripos($allTag,"ACRON")){
					$entryArr['type']='acronym';
				}else if(stripos($allTag,"ABR")){
					$entryArr['type']='abbreviation';
				}
				$entryArr['confidence']='guessed';
				$entryArr['start']=$firstTokI;
				$entryArr['end']=$lastTokI;
			}
			array_push($l_arr, $entryArr);
		}
	}
	return $l_arr;
}
?>
