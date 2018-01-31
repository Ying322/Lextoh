<?php
//resultat naif
function lemma_ariane_n ($text, $lang){
	if(strcasecmp($lang,'fra(fra4)')==0||strpos($lang,'fra4')!==false){
		//$command =  "curl \"http://www.taranis-software.com/Heloise/Ying/test.php\" --data \"InputText=$text&CoupleLangue=FR4-XML\"";
		$command =  "curl \"http://www.lingwarium.org/heloise/heloise/workspaces/Ying/api/test.php\" --data \"InputText=$text&CoupleLangue=FR4-XML\"";
		$res = shell_exec($command);
	}

	return $res;
}

/* Example de structure de resultat de Ariane-Heloise
SimpleXMLElement Object ( 
		[NUMERO] => 8 
		[FORME] => SimpleXMLElement Object ( ) 
		[UL] => ULOCC 
		[NOEUD] => Array (   //plusieurs possiblites de lemmatisation
				[0] => SimpleXMLElement Object ( 
						[NUMERO] => 9 
						[FORME] => TU 
						[UL] => TU 
						) 
				[1] => SimpleXMLElement Object ( 
						[NUMERO] => 10 
						[FORME] => TU 
						[UL] => TU 
						[CAT] => A 
						[GNR] => MAS 
						[NB] => Array ( 
								[0] => SING 
								[1] => PLUR 
								) 
					) 
) 
SimpleXMLElement Object ( 
		[NUMERO] => 11 
		[FORME] => SimpleXMLElement Object ( ) 
		[UL] => ULOCC 
		[NOEUD] => SimpleXMLElement Object (  // une seule possibilite de lemmatisation
				[NUMERO] => 12 
				[FORME] => VAS 
				[UL] => ALLER 
				[CAT] => V 
				[NB] => Array ( 
						[0] => SING 
						[1] => PLUR 
					) 
				[PERS] => Array ( 
						[0] => 2 
						[1] => 3 
					)
			) 
	)  
*/

function lemma_ariane_b ($text, $textOrig){
	$l_arr = Array();
	$xml = new SimpleXMLElement($text);
	$offset=0;
	foreach($xml->NOEUD->NOEUD->NOEUD as $entry){
		$l_node = $entry->NOEUD;
		$form = $l_node[0]->FORME.''; //."" c'est pour transformer element de xml vers string
		$firstTokI = stripos($textOrig, $form, $offset);
		$lastTokI = $firstTokI+strlen($form);
		$offset = $lastTokI;
		foreach($l_node as $realNode){ // toutes les possibilites de lemmatisation
			$lemma = $realNode->UL.'';
			$pos = $realNode -> CAT.'';
			unset($allTag);
			$allTag='';	
			$lastName='';
			foreach($realNode->children() as $tag){
				$name = $tag->getName();
				if($name=='NB'||strpos($name, 'SUB')===0||$name=='TEMPS'||$name=='MODE'||$name=='PERS'||$name=='GNR'){
					$val = $tag->__toString();
					if($lastName==$name){
						$allTag=$allTag.';'.$val;
					}else {
						$allTag=$allTag.'+'.$name.'='.$val;
					}
					$lastName=$name;
				}
			}
			unset($arr);
			$arr['form']=$form.""; 
			$arr['lemma']=$lemma."";
			$arr['pos']=$pos."";
			$arr['confidence']='reliable';
			$arr['allTag']=$allTag;
			$arr['start']=$firstTokI;
			$arr['end']=$lastTokI;
			array_push($l_arr, $arr);
		}
	}
	return $l_arr;
}



?>
