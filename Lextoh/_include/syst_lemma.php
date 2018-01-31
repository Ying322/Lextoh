<?php
  // TODO Actuellement, on convertit les chaînes en minuscule avec strtolower, ça ne marche pas pour les caractères accentués
function getResultFinal($text, $formalism, $output){
	if(!(strcasecmp($output,'xml')==0||strcasecmp($output,'json')==0||strcasecmp($output,'txt')==0)){
		return 'ERROR';
	}else
	if(!(strcasecmp($formalism, 'original')==0||strcasecmp($formalism, 'orig')==0
		||strcasecmp($formalism, 'grapheq')==0||strcasecmp($formalism, 'graphe-q')==0
		||strcasecmp($formalism, 'graphe q')==0||strcasecmp($formalism, 'graphe_q')==0)){
		return 'ERROR';
	}else
	if($text=='ERROR'||empty($text)){
		return 'ERROR';
	}else {
		$res = new SimpleXMLElement($text);
		if(strcasecmp($output,'xml')==0){ //output xml
			if(strcasecmp($formalism, 'original')==0||strcasecmp($formalism, 'orig')==0){ //formalism original
				return $text;
			}else{ //formalism graphe q
				$xml = new SimpleXMLElement($text);
				$lastEnd=0;
				$xml['output']=$output;
				$xml['formalism']=$formalism;
				foreach ($xml->lexeme as $lexeme){
					$start = $lexeme['startpos'];
					//if ($start!=$lastEnd){
						//FIXME create node for espace
						/*$newNode = $xml->addChild('lexeme');
						$newNode->addAttribute('startpos', '-'.$lastEnd.'-');
						$newNode->addAttribute('endpos', '-'.$start.'-');
						$newNode->addAttribute('form', ' ');
						$newNode->addChild('entry', '$OCC($FORME( ))');*/
					//}
					$lastEnd = $lexeme['endpos'];
					$newStart = '-'.$start.'-';
					$newEnd = '-'.$lastEnd.'-';
					$lexeme['startpos'] = $newStart;
					$lexeme['endpos'] = $newEnd;
					$form=$lexeme['form'];
					$newForm=gQEncode($form);
					foreach($lexeme->entry as $entry){
						$lemma = $entry->lemma;
						$newLemma = gQEncode($lemma);
						$pos = $entry->pos;
						$res =  "\$OCC(\$FORME($newForm),\$LU(\$LEMMA($newLemma),\$CAT($pos)))";
						$entry[0] =  $res;
						
					}
					
				}
				return $xml->asXML();
			}
		}else if(strcasecmp($output,'txt')==0){ //output xml
			if(strcasecmp($formalism, 'original')==0||strcasecmp($formalism, 'orig')==0){ //formalism original
				
				//form=bonjour, lemma=bonjour, pos=ADV
				//form=, lemma=, pos=PUNCT
				$xml = new SimpleXMLElement($text);
				$sys = $xml['syst'];
				$lang = $xml['lang'];
				$res = "system=$sys, langue=$lang, output=$output, formalism=$formalism<br/>";
				foreach ($xml->lexeme as $lexeme){
					$form=$lexeme['form'];
					foreach($lexeme->entry as $entry){
						$lemma = $entry->lemma;
						$pos = $entry->pos;
						if(empty($lemma)){
							$res.="form=$form</br>";
						}else{
							$res.="form=$form, lemma=$lemma, pos=$pos</br>";
						}
					}
				}
				return $res;
			}else{ //formalism graphe q
				$xml = new SimpleXMLElement($text);
				$sys = $xml['syst'];
				$lang = $xml['lang'];
				$res = "system=$sys, langue=$lang, output=$output, formalism=$formalism<br/>";
				$lastEnd=0;
				foreach ($xml->lexeme as $lexeme){
					$form=$lexeme['form'];
					$newForm=gQEncode($form);
					$start=$lexeme['startpos'];
					$end=$lexeme['endpos'];
				//	echo "start : $start lastend : $lastEnd<br/>";
					if (intval($start)!=intval($lastEnd)){
						//echo "start!=lastEnd start : $start lastend : $lastEnd <br/>";
						$espaceForm=gQEncode(' ');
						$res.="-$lastEnd-\$OCC(\$FORME($espaceForm))-$start-<br/>";
					}
					foreach($lexeme->entry as $entry){
						$lemma = $entry->lemma;
						$newLemma = gQEncode($lemma);
						$pos = $entry->pos;
						$res.="-$start-\$OCC(\$FORME($newForm),\$LU(\$LEMMA($newLemma),\$CAT($pos)))-$end-<br/>";
					}
					$lastEnd=$end;
				}
				return $res;
			
			}
		}else if(strcasecmp($output,'json')==0){
			if(strcasecmp($formalism, 'original')==0||strcasecmp($formalism, 'orig')==0){ //formalism original
			
				//var JSONObject=[
// 				{"form":"bonjour" , "lemma":"bonjour" , "pos":"ADV"},
// 				{"form":"" , "lemma":"" , "pos":"PUNCT"},
// 				{"form":"" , "lemma":"" , "pos":""},
				$xml = new SimpleXMLElement($text);
				$sys = $xml['syst'];
				$lang = $xml['lang'];
				$res = "var JSONObject=[<br/>";
				$res.="{\"system\":\"$sys\", \"langue\":\"$lang\", \"output\":\"$output\", \"formalism\":\"$formalism\"},<br/>";
				foreach ($xml->lexeme as $lexeme){
					$form=$lexeme['form'];
					foreach($lexeme->entry as $entry){
						$lemma = $entry->lemma;
						$pos = $entry->pos;
						if(strlen(trim($lemma))==0){
							$res.="{\"form\":\"$form\"},</br>";
						}else{
							$res.="{\"form\":\"$form\", \"lemma\":\"$lemma\", \"pos\":\"$pos\"},</br>";
						}
					}
				}
				$res=substr($res, 0, strlen($res)-6); //-6 correspond aux ",<br/>", c'est pour elever ","
				$res.='<br/>]';
				return $res;
			}else{ //formalism graphe q
// 				var JSONObject=[
// 				{"form":"bonjour" , "lemma":"bonjour" , "pos":"ADV"},
// 				{"form":"" , "lemma":"" , "pos":"PUNCT"},
// 				{"form":"" , "lemma":"" , "pos":""},
				$xml = new SimpleXMLElement($text);
				$sys = $xml['syst'];
				$lang = $xml['lang'];
				$res = "var JSONObject=[<br/>";
				$res.="{\"system\":\"$sys\", \"langue\":\"$lang\", \"output\":\"$output\", \"formalism\":\"$formalism\"},<br/>";
				foreach ($xml->lexeme as $lexeme){
					$form=$lexeme['form'];
					$newForm=gQEncode($form);
					$start=$lexeme['startpos'];
					$end=$lexeme['endpos'];
					//echo "start : $start lastend : $lastEnd<br/>";
					if (intval($start)!=intval($lastEnd)){
						//echo "start!=lastEnd start : $start lastend : $lastEnd <br/>";
						$espaceForm=gQEncode(' ');
						$res.="{-$lastEnd-\$OCC(\$FORME($espaceForm))-$start-},<br/>";
					}
					foreach($lexeme->entry as $entry){
						$lemma = $entry->lemma;
						$newLemma = gQEncode($lemma);
						$pos = $entry->pos;
						$res.="{-$start-\$OCC(\$FORME($newForm),\$LU(\$LEMMA($newLemma),\$CAT($pos)))-$end-},<br/>";
					}
					$lastEnd=$end;
				}
				$res=substr($res, 0, strlen($res)-6); //-6 correspond aux ",<br/>", c'est pour elever ","
				$res.='<br/>]';
				return $res;
			}
		}
	}
}

function getResultLemmatixFinal ($text, $syst, $lang, $l_l_pos){
	$xml='';
	foreach($l_l_pos as $l_pos){
		if($l_pos['lemma']==$syst&&$l_pos['langs']==$lang){
			$myPosList = $l_pos['window'];
			break;		
		}
	}
	if(empty($myPosList)){
		return 'ERROR';	
	}else {
		if(!empty($text)){
			$res_lf = new SimpleXMLElement($text);
			$arr_pos = explode(";",$myPosList);
			foreach ($res_lf->lexeme as $myentry) {
				unset($l_entryString);
		  		$l_entryString=array();
		  		foreach($myentry->entry as $entry){
					$lemma = $entry->lemma;
					$pos=$entry->pos;
					$pos=strtoupper($pos);
					$change = false;
					foreach($arr_pos as $myPos){
						$lemmatixPos = explode(":",$myPos)[0];
						$lemmatixPos = trim($lemmatixPos);
						$tool_pos = explode(":",$myPos)[1];
						if(strpos($tool_pos, ",")===false){
							$tool_l_pos = array($tool_pos);
						}else{
							$tool_l_pos = explode(",",$tool_pos);
						}
						foreach($tool_l_pos as $t_pos){
							$t_pos = trim($t_pos);
							if(strcasecmp($t_pos,$pos)==0){
								$pos=$lemmatixPos;
								$change=true;
								break;
							}
						}
						if($change) break;
					}

					if(!$change){
						$pos='UNK';	
					}
					$entry->pos=$pos;
					$node = $entry->xpath("./morpho-tag-list");
					if ( ! empty($node)) {
   		 				unset($node[0][0]);
					}
					$entryString=$lemma.'.'.$pos.'.'.$start;
					if(in_array($entryString, $l_entryString)){
						$entry['class']='repeat';
					}else{
						array_push($l_entryString,$entryString);
					}
				}//fin du foreach($myentry->entry as $entry)
			}//fin du foreach ($res_lf->lexeme as $myentry)
		$xml = $res_lf->asXml();
		$rmRepeatXml = new SimpleXMLElement($xml);
		$l_node = $rmRepeatXml->xpath('//entry[@class=\'repeat\']');
		foreach($l_node as $removeNode){
			$dom=dom_import_simplexml($removeNode);
			$dom->parentNode->removeChild($dom);		
		}
		return $rmRepeatXml->asXML();
		}//fin du if
	}//fin du else
}

function getResultLemmatix($arrRes, $lang, $syst){
	$laststart = '';
	$str_output ='';
	if(count($arrRes)>0&&$lang&&$syst){
		$str_output.= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$str_output.= "<lemmatix syst=\"$syst\" lang=\"$lang\">";
		$once=true;
		$memArr = array();
		foreach($arrRes as $arr){
			$form = $arr['form'];
			$lemma = $arr['lemma'];
			$pos = $arr['pos'];
			$start = $arr['start'];
			$end = $arr['end'];
			$type = $arr['type'];
			$allTag =  $arr['allTag'];
			$l_allTag = explode("+", $allTag);
			$confidence = $arr['confidence'];
			if($confidence==''){
				if($pos!='UNK')
					$confidence='reliable';
				else
					$confidence='';
			}
			$str = $form.$lemma.$pos.$start.$end.$type.$allTag.$confidence;
			if(array_search($str, $memArr)===false){
				array_push($memArr, $str);
			}else{
				continue; //eviter les doublons, surtout pour delaf
			}
			if($start!==$laststart){
				//nouvelle forme
				if($once){
					$once=false;
				}else{
					$str_output.="</entry>";
					$str_output.="</lexeme>";
				}
				$str_output.="<lexeme startpos=\"$start\" endpos=\"$end\" form=\"$form\">";
			}else{
				$str_output.="</entry>";
			}
			$str_output.="<entry>";
			if(strpos(trim($lemma), ' ')!=false){
				if(!$type){
					$type='compound';
				}
			}
			if(!empty($lemma)){
				$str_output.="<lemma confidence=\"$confidence\" type=\"$type\">$lemma</lemma>";
			}else {
				$str_output.="<lemma confidence=\"\" type=\"\">$lemma</lemma>";
			}
			if(!empty($pos)){
				$str_output.="<pos confidence=\"$confidence\">$pos</pos>";
			}else {
				$str_output.="<pos confidence=\"\">$pos</pos>";
			}
	
			$str_output.="<morpho-tag-list>";
			foreach($l_allTag as $tag){
				if($tag!=null){
					$str_output.="<morpho-tag>+$tag</morpho-tag>";
				}			
			}
			$str_output.="</morpho-tag-list>";

			$laststart=$start;
		
		}	
		$str_output.="</entry>";
		$str_output.="</lexeme>";
		$str_output.="</lemmatix>";
	}
	return $str_output;
}
/*
function lemma_delaf ($once, $str_output, $output, $form, $lang, $firstTokI, $lastTokI, $wR){
		$form = minuscula($form);
	
        $query = 'SELECT * from delafform WHERE (form="'.mysql_real_escape_string($form).'" OR form="'.mysql_real_escape_string(strtolower($form)).'") AND lang="'.mysql_real_escape_string($lang).'"';
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());   // Recherche dans la BD
        $nbLines = 0;
        $lastRes = '';
       
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {  // Pour chaque lemme trouvé
          if(strtolower($line['form']) == strtolower($form)) {  // Vérifier l'égalité (MySQL ignore les espaces en début et fin de chaîne)
			if(strtolower($output)=='txt'){
				$lemma = $line['lemma'];
            	$pos = $line['pos'];
            	$frm = $form;
            	$res ="$frm,$lemma,$pos". '<br/>';
            	if($res != $lastRes) {  // Si ce n'est pas un doublon
             	 ++$nbLines;
             	 $str_output.= "form=$frm, lemma=$lemma, pos=$pos".'<br/>';
            	}
            	$lastRes = $res;
            }
            
            if(strtolower($output)=='json'){
            	if($once) {
            		$str_output.=  "var JSONObject=[".'<br/>';
            		$once = 0;
            	}
				$lemma = $line['lemma'];
            	$pos = $line['pos'];
            	$frm = $form;
            	$res ="$frm,$lemma,$pos". '<br/>';
            	if($res != $lastRes) {  // Si ce n'est pas un doublon
             	 ++$nbLines;
             	$str_output.=  "{\"form\":\"$frm\" , \"lemma\":\"$lemma\" , \"POS\":\"$pos\"},".'<br/>';
            	}
            	$lastRes = $res;
            	
            }
            
            if(strtolower($output)=='xml'){
            	if($once) {
            		$str_output.=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
            		$str_output.=  "<lemmatix>";
            		$once = 0;
            	}
				$lemma = $line['lemma'];
            	$pos = $line['pos'];
            	$frm = $form;
            	$res ="$frm,$lemma,$pos". '<br/>';
            	if($res != $lastRes) {  // Si ce n'est pas un doublon
             	 ++$nbLines;
             	 $str_output.="<entry>";
             	 $str_output.="<form>$frm</form>";
             	 $str_output.="<lemma>$lemma</lemma>";
             	 $str_output.="<pos>$pos</pos>";
             	 $str_output.="</entry>";
             //	$str_output.=  "{\"form\":\"".$frm."\" , \"lemma\":\"".$lemma."\" , \"POS\":\"".$pos."\"},".'<br/>';
            	}
            	$lastRes = $res;
            	
            }
				
			if(strtolower($output)=='graphe-q'||strtolower($output)=='grapheq'){
				$lemma = gQEncode($line['lemma']);
            	$pos = $line['pos'];
           		$frm = gQEncode($form);
            	$res = "-$firstTokI-" . "\$OCC(\$FORME($frm),\$LU(\$LEMMA($lemma),\$CAT($pos)))" . '-'.($lastTokI+1).'-' . '<br/>';
            	if($res != $lastRes) {  // Si ce n'est pas un doublon
              		++$nbLines;
              		$str_output.=  $res;
            	}
            	$lastRes = $res;
			}
          } // fin if(strtolower($line['form']) == strtolower($form)) 
        } // fin while
        
        if(strtolower($output)=='graphe-q'||strtolower($output)=='grapheq'){
        	if($nbLines == 0 && $wR == 0) {  // Si rien trouvé alors que la fenêtre ne contient qu'un seul token
          		$str_output.=  "-$firstTokI-";
          		$str_output.=  "\$OCC(\$FORME(".gQEncode($form)."))";
          		$str_output.=  '-'.($lastTokI+1).'-';
          		$str_output.=  '<br/>';
        	}
        }
         if(strtolower($output)=='json'){
        	 if($once) {
            		$str_output.=  "var JSONObject=[".'<br/>';
            		$once = 0;
            	}
         	if($nbLines == 0 && $wR == 0) {  // Si rien trouvé alors que la fenêtre ne contient qu'un seul token
         		$str_output.=  "{\"form\":\"".$form."\"},".'<br/>';
         	}
         }
         
		if(strtolower($output)=='txt'){
         	if($nbLines == 0 && $wR == 0) {  // Si rien trouvé alors que la fenêtre ne contient qu'un seul token
         		$str_output.=  "form=$form, lemma non trouvé".'<br/>';
         	}
         }
        if(strtolower($output)=='xml'){
        	 if($once) {
            		$str_output.=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
            		$str_output.=  "<lemmatix>";
            		$once = 0;
            }
         	if($nbLines == 0 && $wR == 0 && $form!=" ") {  // Si rien trouvé alors que la fenêtre ne contient qu'un seul token
         		$str_output.="<entry>";
             	 $str_output.="<form>$form</form>";
             	 $str_output.="</entry>";
         	}
         }
  $arr = Array($str_output, $once);
  return $arr;

}


function lemma_xelda ($once, $str_output, $output, $text, $lang){
//if(!$service||!$text||!$lang||!$input||!$output){
	$text = minuscula($text);
	$url = "http://localhost/Ci-Hai/LingServices/?serv=morphoanalysis&output=xml&input=plaintext&tokenType=FST&maType=FSTPOSTag&lang=$lang&text=".urlencode($text);
	$file = file_get_contents($url);
//	echo $file.'<br/>';
	$xml = new SimpleXMLElement($file);
	foreach ($xml->sentence->{'lexeme-list'}->lexeme as $entry) {
		$form = $entry->{'surface-form'};
		$senseArray = array();
		$senseArray = $entry->{'sense-list'}->sense;
		$firstTokI = $entry['start'];
		$lastTokI = $firstTokI+$entry['length'];
		//echo 'form = '.$form.' nbr de sense = '.count($senseArray).'<br/>';
		//lemmatix_interm ($once, $str_output, $syst, $form, $lemma, $confidence, $comp, $l_pos, $l_morphoTags, $firstTokI, $lastTokI)
		if(strtolower($output)=='txt'){
			for($m=0; $m<count($senseArray); $m++){
				$frm=$form;
				$lemma= $senseArray[$m]->{'base-form'};
				$pos= $senseArray[$m]->{'part-of-speech'};
				if ($senseArray[$m]->{'part-of-speech'}['confidence']=='guessed') {
					$str_output.= " form=$frm, lemma guessed =$lemma, pos guessed =$pos".'<br/>';
				} else {
					$str_output.= "form=$frm, lemma=$lemma, pos=$pos".'<br/>';
				}
			}
		}
		if(strtolower($output)=='xml'){
			for($m=0; $m<count($senseArray); $m++){
				if($once) {
					$str_output.=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
					$str_output.=  "<lemmatix>";
					$once = 0;
				}
				$lemma = $senseArray[$m]->{'base-form'};
				$pos = $senseArray[$m]->{'part-of-speech'};
				$frm = $form;
				$str_output.="<entry>";
				$str_output.="<form>$frm</form>";
				if ($senseArray[$m]->{'part-of-speech'}['confidence']=='guessed') {
					$str_output.="<lemma confidence=\"guessed\">$lemma</lemma>";
					$str_output.="<pos confidence=\"guessed\">$pos</pos>";
				} else {
					$str_output.="<lemma>$lemma</lemma>";
					$str_output.="<pos>$pos</pos>";
				}
				$str_output.="</entry>";
			}
				
		}
		if(strtolower($output)=='json'){
			for($m=0; $m<count($senseArray); $m++){
				if($once) {
            		$str_output.=  "var JSONObject=[".'<br/>';
            		$once = 0;
            	}
				$lemma = $senseArray[$m]->{'base-form'};
            	$pos = $senseArray[$m]->{'part-of-speech'};
            	$frm = $form;
            	if ($senseArray[$m]->{'part-of-speech'}['confidence']=='guessed') {
            		$str_output.=  "{\"form\":\"$frm\" , \"confidence\":\"guessed\" , \"lemma\":\"$lemma\" , \"POS\":\"$pos\"},".'<br/>';
            	}else {           	
             		$str_output.=  "{\"form\":\"$frm\" , \"lemma\":\"$lemma\" , \"POS\":\"$pos\"},".'<br/>';
             	}

            }
		}
		if(strtolower($output)=='graphe-q'||strtolower($output)=='grapheq'){
			for($m=0; $m<count($senseArray); $m++){
				$lemma = gQEncode($senseArray[$m]->{'base-form'});
            	$pos = $senseArray[$m]->{'part-of-speech'};
           		$frm = gQEncode($form);
            	$res = "-$firstTokI-" . "\$OCC(\$FORME($frm),\$LU(\$LEMMA($lemma),\$CAT($pos)))" . '-'.($lastTokI).'-' . '<br/>';
            	$str_output.=  $res;
			}
		}
	}
	$arr = Array($str_output, $once);
	return $arr;
}

function lemma_xip ($once, $str_output, $output, $text, $lang){
	$text = minuscula($text);
	if(strtolower($lang)=='eng'||strtolower($lang)=='english'||strtolower($lang)=='en') {
		$command = "echo \"$text\" | /home/ying/Ying/lingOutils/xip-13.00-28/bin/linux64/xipparse-en -a";
	} else if(strtolower($lang)=='fra'||strtolower($lang)=='french'||strtolower($lang)=='fr'){
		$command = "echo \"$text\" | /home/ying/Ying/lingOutils/xip-13.00-28/bin/linux64/xipparse-fr -a";
	}
	$arr = parserResXip($command, $text);
	foreach ($arr as $entryArr){
		$frm = $entryArr[0];
		$lemma = $entryArr[1];
		$pos = $entryArr[2];		
		$confidence = '';
		$firstTokI = 0;
		$lastTokI = 0;
//		echo count($entryArr);
		if(count($entryArr)==6){
			$confidence='guessed';
			$firstTokI=$entryArr[4];
			$lastTokI=$entryArr[5];
		}else {
			$firstTokI=$entryArr[3];
			$lastTokI=$entryArr[4];
		}
		
		if(strtolower($output)=='txt'){
			if($confidence=='guessed'){
				$str_output.= " form=$frm, lemma guessed =$lemma, pos guessed =$pos".'<br/>';
			} else {
		 		$str_output.= "form=$frm, lemma=$lemma, pos=$pos".'<br/>';
		 	}
		}
		if(strtolower($output)=='xml'){
			if($once) {
				$str_output.=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
				$str_output.=  "<lemmatix>";
				$once = 0;
			}
			$str_output.="<entry>";
			$str_output.="<form>$frm</form>";
			if($confidence=='guessed'){
				$str_output.="<lemma confidence=\"guessed\">$lemma</lemma>";
				$str_output.="<pos confidence=\"guessed\">$pos</pos>";
			} else{
				$str_output.="<lemma>$lemma</lemma>";
				$str_output.="<pos>$pos</pos>";
			}
			$str_output.="</entry>";
		}
		if(strtolower($output)=='json'){
			if($once) {
				$str_output.=  "var JSONObject=[".'<br/>';
				$once = 0;
			}
			if($confidence=='guessed'){
				$str_output.=  "{\"form\":\"$frm\" , \"confidence\":\"guessed\" , \"lemma\":\"$lemma\" , \"pos\":\"$pos\"},".'<br/>';
			}else {           	
				$str_output.=  "{\"form\":\"$frm\" , \"lemma\":\"$lemma\" , \"pos\":\"$pos\"},".'<br/>';
			} 
		}
		if(strtolower($output)=='graphe-q'||strtolower($output)=='grapheq'){
			$lemma = gQEncode($lemma);
           	$frm = gQEncode($frm);
            $res = "-$firstTokI-" . "\$OCC(\$FORME($frm),\$LU(\$LEMMA($lemma),\$CAT($pos)))" . '-'.($lastTokI).'-' . '<br/>';
            $str_output.=  $res;
		}	
		
	}
	
	 $arr = Array($str_output, $once);
  	 return $arr;
}

function parserResXip($command, $text){
	$res = shell_exec($command);
	$str = explode('0>TOP', $res)[1];
	$lemArray = explode(',',$str);
	$xipResArr = array();
	$offset = 0;
	foreach($lemArray as $entry){
		$arr = explode('{',$entry);
		$l = count($arr);
		$pos = $arr[$l-2];
		$confidence = strpos($arr[$l-1], '+Guessed+');
		$term = explode('^', $arr[$l-1]);
		$form = $term[0];
		$lemma = $term[1];	
		$firstTokI = strpos($text, $form, $offset);
		$lastTokI = $firstTokI+strlen($form);
		$offset = $lastTokI;
		//echo ' firstTokI'.$firstTokI.', lastTokI'.$lastTokI;
		if($confidence===false){
			$entryArr = array($form, $lemma, $pos, $firstTokI, $lastTokI);
		}else {
			$entryArr = array($form, $lemma, $pos, $confidence, $firstTokI, $lastTokI);
		}
		
		array_push($xipResArr, $entryArr);
		
	}
	return $xipResArr;
}
  
function compl_output($once, $output, $str_output){
      if(!$once&&strtolower($output)=='json'){
      	$length = $str_output.length-6; // Pour enlever la derniere virgule, 6 corresponds au ",<br/>"
      	$str_output = substr($str_output, 0 , $length).'</br>]';
      }
      if(!$once&&strtolower($output)=='xml'){
      	$str_output.= '</lemmatix>';
      }
      
      return $str_output;
}    
*/

function parserConf($filename, $delimiter)
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
        	if(count($row)==3&&!startsWith($row[0], '#')){
                $lemma =  $row[0];
                $s_lang = $row[1];
                $s_window = $row[2];
                $l_lemma=array("lemma"=>$lemma, "langs"=>$s_lang, "window"=>$s_window);
                array_push($data, $l_lemma);
            }
        }
        fclose($handle);
    }
    return $data;
}

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

/*function get_output($str_output, $output){
	//print_r($str_output);
	$xml = simplexml_load_string($str_output);
	$l_lexeme = $xml->lexeme;
	$str='';
	$once = 1;
	foreach ($l_lexeme as $lexeme){
		$form = $lexeme['form'];
		$startpos = $lexeme['startpos'];
		$endpos = $lexeme['endpos'];
		$l_entry = $lexeme->entry;
		//echo "form=$form<br/>";
		foreach($l_entry as $entry){
			$lemma = $entry->lemma;
			$pos = $entry->pos;
			//echo "lemma=$lemma<br/>pos=$pos<br/>"; 
			$l_morphoTags = $entry->{'morpho-tag-list'}->{'morpho-tag'};
			$mpts='';
			foreach($l_morphoTags as $mpt){
				$mpts.='+'.$mpt;
			}
			//echo "mpts=$mpts<br/>";
			$confidence = $entry->pos['confidence']; 
			
			if(strtolower($output)=='txt'){
				if($lemma!-null){
					if($confidence=='guessed'){
						$str.= "form=$form, lemma guessed=$lemma, pos guessed=$pos, morpho tags=$mpts, start=$startpos, end=$endpos".'<br/>';
					} else {	 		
						$str.= "form=$form, lemma=$lemma, pos=$pos, morpho tags=$mpts, start=$startpos, end=$endpos".'<br/>';
		 			}
		 		}else{
		 			$str.= "form=$form, start=$startpos, end=$endpos".'<br/>';
		 		}
			}
			if(strtolower($output)=='xml'){
				$str = $str_output;
			}
			if(strtolower($output)=='json'){
				if($once) {
					$str.=  "[".'<br/>';
					$once = 0;
				}
				if($lemma!-null){
					if($confidence=='guessed'){
						$str.=  "{\"form\":\"$form\" , \"confidence\":\"guessed\" , \"lemma\":\"$lemma\" , \"pos\":\"$pos\", \"morphoAllTags\":\"$mpts\", \"start\":\"$startpos\", \"end\":\"$endpos\"},".'<br/>';
					}else {           	
						$str.=  "{\"form\":\"$form\" , \"lemma\":\"$lemma\" , \"pos\":\"$pos\", \"morphoAllTags\":\"$mpts\", \"start\":\"$startpos\", \"end\":\"$endpos\"},".'<br/>';
					} 
				}else{
					$str.=  "{\"form\":\"$form\"}";
				}
			}
			if(strtolower($output)=='graphe-q'||strtolower($output)=='grapheq'){
           		$frm = gQEncode($form);
           		if($lemma!=null){
           			$lma = gQEncode($lemma);
            		$res = "-$startpos-" . "\$OCC(\$FORME($frm),\$LU(\$LEMMA($lma),\$CAT($pos)))" . '-'.($endpos).'-' . '<br/>';
            	}else{
            		$res = "-$startpos-" . "\$OCC(\$FORME($frm))" . '-'.($endpos).'-' . '<br/>';
            	}
            	$str.=  $res;
			}	
		}
	}
	//echo "$str";
	return $str;
}
*/



   
?>
