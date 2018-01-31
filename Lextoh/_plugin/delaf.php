<?php
function lemma_delaf_n($text, $lang,$window){
	//$result = "";
    $l_line = array();
	$tokens = preg_split('/([\.\s\/\\*+$€&~#"\'\{\[\(|`_@)\]°}=%!:;,?-]+)/', $text, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY); 

	foreach($tokens as $i=>$token) { // Pour chaque token
  
		for($wR=0; $wR<=$window; ++$wR)  {// Pour chaque offset (vers la droite)
		  if($i+$wR<count($tokens)) {  // Si la fenêtre est valide
			$firstTokI = $i;// - $wL;
			$lastTokI = $i + $wR;
			$form = '';
			for($j=$firstTokI; $j<=$lastTokI; ++$j) // Composition d'une forme
			  $form .= $tokens[$j];
			//echo $form.'<br/>';
			if($i===0){
				$query = "SELECT * from delafform WHERE (form collate utf8_bin='".mysql_real_escape_string($form)."' OR form collate utf8_bin='".mysql_real_escape_string(ucfirst($form))."' OR form collate utf8_bin='".mysql_real_escape_string(lcfirst($form))."') AND lang='".mysql_real_escape_string($lang)."'";
				//echo $query;
			}else{
				$query = "SELECT * from delafform WHERE form collate utf8_bin='".mysql_real_escape_string($form)."' AND lang='".mysql_real_escape_string($lang)."'";
			}
			//$query = 'SELECT * from delafform WHERE (form="'.mysql_real_escape_string($form).'" OR form="'.mysql_real_escape_string(strtolower($form)).'") AND lang="'.mysql_real_escape_string($lang).'"';

    		$res = mysql_query($query) or die('Query failed: ' . mysql_error());   // Recherche dans la BD
			
    		while ($line = mysql_fetch_array($res, MYSQL_ASSOC)) {
    		 if(strtolower($line['form']) == strtolower($form)) { 
    		 	if($wR>0){
    		 		$line['type']='compound';
    		 	}
    			array_push($l_line, $line);
             }
    		} //fin du while
    		if(!mysql_num_rows($res)&&$wR==0){
    			$line['form']=$form;
    			array_push($l_line, $line);
    		}
    	  }//fin du if
    	}//fin du for
	}//fin du foreach
	return $l_line;
}

function lemma_delaf_b($arrSql, $text){
	$l_arr = array();
	$lastFrom = '';
	$l_id = array();	
	$offset = 0;
	$start = 0;
	$end = 0;
	foreach($arrSql as $arr){
		$form=$arr['form'];
		$id = $arr['id'];
		//echo $offset.'<br/>';
		//print_r($l_id);
		//echo '<br/>';
		//echo 'search result : '.array_search($id, $l_id).'<br/>';
		$arr['allTag']=$arr['type'];
		unset($arr['type']);
		if(strpos($arr['allTag'], 'compound_comp')){
			$arr['type']='compound';
		}
		if($form!=$lastForm||($form==$lastForm&&(array_search($id, $l_id)||array_search($id, $l_id)===0))){
			$start = strpos($text, $form, $offset);
			$end = $start+strlen($form);
			$offset=$end;
			unset($l_id);
			$l_id = array();
			array_push($l_id,$id);
			//echo "apres get start=$start, end=$end, form=$form, lastform=$lastForm, id=$id".'<br/>';
		}else{
			array_push($l_id,$id);
			//echo "no get start=$start, end=$end, form=$form, lastform=$lastForm, id=$id".'<br/>';
		}
		$lastForm = $form;
	   	//echo $start;
		$arr['start']=$start+0;
		$arr['end']=$end;
		array_push($l_arr, $arr);
	}
	return $l_arr; 
}
?>
