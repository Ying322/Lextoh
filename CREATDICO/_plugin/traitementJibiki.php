
<?php

function getTranslationListJibiki($lemmaList, $dico, $serv, $langueSource, $l_serv){
	foreach($l_serv as $k=>$v){
		if($serv=="$k"){
			$url="$v";
			$url = str_replace('$dico',trim($dico),$url);
			$url = str_replace('$langueSource',trim($langueSource),$url);
		//	echo 'last url = '.$url;
			break;
		}
	}
	$newLemmaList = explode("form=", $lemmaList);
	$traductionArray= array();
//	print_r($newLemmaList);
//	echo '<br/>';
	//echo count($newLemmaList).'<br/>';
	for ($i=1; $i<count($newLemmaList); $i++){
	//	echo "i = $i <br/>";
		$lemmaItem = $newLemmaList[$i];
	//	echo "newLemmaList =  $lemmaItem";
		$lemma = explode("lemma=",$lemmaItem)[1];
		$lemma = explode(", ", $lemma)[0];
//		echo 'lemma='.$lemma.'<br/>';
		$lemma = trim($lemma);
// 		array_push($lemmaArray, $lemma);
// 		echo 'lemmaArray = ';
// 		print_r($lemmaArray);
// 		echo '<br/>';
		$pos = explode('pos=', $lemmaItem)[1];
		$pos = explode('<', $pos)[0];
		$form = explode(",", $lemmaItem)[0];
		$form = trim($form);
		unset($tradArray);
		$tradArray = array();
		if (empty($lemma)){
			$lemma = 'Lemme non trouvé pour forme '.$form;
			$pos = "Indisponible";
			$tradArray[]="Indisponible";
			
		}else {
			$newUrl = str_replace('$lemm',$lemma,$url);
		//echo "final url = ".$newUrl."<br/>";
			$trad= file_get_contents($newUrl);
			//echo $trad;
			if(startsWith($trad, "<?xml ")){
				$xml = new SimpleXMLElement($trad);
				$nbrXml=count($xml);
				if($nbrXml===0){
					$tradArray[]="Lemme non trouvé dans ce dictionnaire";
				}else {
					for ($m=0;$m<$nbrXml;$m++){
						if($serv=='pivax'){
						//	$tradArray[]= $xml->entry[$m]->headword . "<span style=\"color:blue\"> (" . $xml ->entry[$m]['lang'] . ")</span>";
							$tradArray[]= $xml->entry[$m]->headword . " (" . $xml ->entry[$m]['lang'] . ")";
						}else if($serv=='papillon'){							
							foreach($xml->entry[$m]->children() as $child){
								$val = $child['value'];
								$targLang = $child['lang'];
								if($val=='cdm-translation'&&!empty($targLang)){
									array_push($tradArray, "$child ($targLang)");
								}						
							}
						//	$tradArray[]= $xml->entry[$m]->key['value=cdm-translation'] . " (" . $xml ->entry[$m]->key["value='cdm-translation'"]['lang'] . ")";
						}
						$tradArray=array_unique($tradArray);
					}
				}
			}else {
				$tradArray[]="Lemme non trouvé dans ce dictionnaire";
			}
		}
		$arr = array($lemma, $pos, $tradArray);
		array_push($traductionArray, $arr);
	}
	return $traductionArray;
}

function getTragetInfoListJibiki($traductionArray, $serv, $dico, $l_serv){
	$transListArr=array();
	foreach($traductionArray as $arr){
		$lemma = $arr[0];
		$pos = $arr[1];
		$translationList = $arr[2];
		foreach($l_serv as $k=>$v){
			if('trans_'.$serv=="$k"){
				$url="$v";
				$url = str_replace('$dico',trim($dico),$url);
				break;
			}
		}
 		foreach($translationList as $translation){
 			$list = explode(" (", $translation);
 			$term = $list[0];
//   			echo "term = $term<br/>";
 			$lang = explode( ")", $list[1])[0];
 			
//  			echo "lang = $lang<br/>";
 			if(!empty($lang)){
 				$newUrl = str_replace('$lemm',trim($term),$url);
 				$newUrl = str_replace('$lang',trim($lang),$newUrl);
 			//	echo 'url = '.$newUrl.'<br/>';
 				$trad = file_get_contents($newUrl);
 				//echo $trad;
 				if(startsWith($trad, "<?xml ")){
 					$xml = new SimpleXMLElement($trad);
 					$nbrXml=count($xml);
 			//		echo $nbrXml.'<br/>';
 					for ($m=0;$m<$nbrXml;$m++){
 						$handle = $xml->entry[$m]->handle;
 						$newUrl2 =  explode("/cdm-headword", $newUrl)[0];
 						$newUrl2.="/handle/$handle";
 						//echo 'newUrl='.$newUrl2.'<br/>';
 						$newtrad= file_get_contents($newUrl2);
 						//echo 'new trad ='.$newtrad.'<br/>';
 						if(startsWith($trad, "<?xml ")){
 							//	$key = "$lemma_$pos_$term_$lang_$handle";
 							//	xmlArray($key)=$newtrad;
 							unset($transArr);
 							$transArr=array("lemme"=>$lemma, "pos"=>$pos, "traduction"=>$term, "langue"=>$lang, "xml"=>$newtrad);
 							array_push($transListArr, $transArr);
 						}
 					}
 					
 				}
 				
 			}else{
 				unset($transArr);
 				$transArr=array("lemme"=>$lemma, "pos"=>$pos, "traduction"=>$term);
 				array_push($transListArr, $transArr);
 			}

 		}
 		//echo '<br/>';
	}
	return $transListArr;
}
?>
