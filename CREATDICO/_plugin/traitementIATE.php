
<?php

function getTranslationListIATE($lemmaList, $dico, $serv, $langueSource, $l_serv){
	$langueSource = convert_langCode3_to_langCode2($langueSource);
	foreach($l_serv as $k=>$v){
		if($serv=="$k"){
			$url="$v";
	//		$url = str_replace('$dico',trim($dico),$url);
			$url = str_replace('$langueSource',trim($langueSource),$url);
		//	echo 'last url = '.$url;
			break;
		}
	}
	$newLemmaList = explode("form=", $lemmaList);
	$traductionArray= array();	
	$header = array(
			'User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1',
	);
	
	for ($i=1; $i<count($newLemmaList); $i++){
		$lemmaItem = $newLemmaList[$i];
		$lemma = explode("lemma=",$lemmaItem)[1];
		$lemma = explode(", ", $lemma)[0];
		$lemma = trim($lemma);
		$pos = explode('pos=', $lemmaItem)[1];
		$pos = explode('<', $pos)[0];
		$form = explode(",", $lemmaItem)[0];
		$form = trim($form);
		unset($tradArray);
		$tradArray = array();
		$linkArray = array();
		if (empty($lemma)){
			$lemma = 'Lemme non trouvé pour forme '.$form;
			$pos = "Indisponible";
			$tradArray[]="Indisponible";
		}else {
			$newUrl = str_replace('$lemm',$lemma,$url);
			//$trad= file_get_contents($newUrl, false, $context);
			unlink(dirname(__FILE__)."/cookie_iate_$i.txt");
			unset($cookie);
			$cookie = dirname(__FILE__)."/cookie_iate_$i.txt";
		//	echo 'set cookie : '.$cookie.'<br/>';
			unset($curl);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $newUrl);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //ne recuperer que le resulat de curl_exec et ne pas afficher sur la page
			chmod($cookie, 0777);
			$trad = curl_exec($curl);
			curl_close($curl);
			$html = str_get_html($trad);
			foreach($html->find('table tr[onclick]') as $ret){
				if($ret->find('a', 0)){
					if($link){
						array_push($linkArray, $link);
						array_unique($linkArray);
						//	echo "push $link in linkarray<br/>";
						unset($link);
					}
					$linkele=$ret->find('a', 0);
					if(empty($link)){
						$link="($cookie)";
					}
					$link.=$linkele->href;
				}
				//headword[lang]
				foreach($ret->find('.termRecord') as $ele){
					if($ele->find('b')){
						$langele = $ele->find('b', 0);
						$lang = $langele->plaintext;
					}else{
						$hw = $ele->plaintext;
						$len=strlen($lang);
						if($len==2){
							$lang=convert_langCode2_to_langCode3($lang);
						}
						array_push($tradArray, "$hw($lang)");
						//$link="$hw($lang)|".$link;
						
						$link=$link."[$hw($lang)]";
						$tradArray=array_unique($tradArray);
						
					}	
				}
				
			}
		}
		$arr = array($lemma, $pos, $tradArray, $linkArray);
		array_push($traductionArray, $arr);
	}
	return $traductionArray;
}

function getTragetInfoListIATE($traductionArray, $serv, $dico, $l_serv){
	$urlArray = array();
	$transListArr = array();
	$header = array(
			'User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1',
	);
	foreach($traductionArray as $arr){
		$urlArray = $arr[3];
		$lemma = $arr[0];
		$pos = $arr[1];
		foreach($urlArray as $link){
			$var = explode('[',$link);
			$url = explode(')',$var[0])[1];
			$cookie = explode(')',$var[0])[0];
			$cookie = explode('(', $cookie)[1];
			$lang = explode('(', $var[1])[1];
			$lang = explode(')', $lang)[0];
			unset($l_hw);
			for($i=1; $i<count($var); $i++){
				$hw = explode('(', $var[$i])[0];
				if(empty($l_hw)){
					$l_hw=$hw;
				}else{
					$l_hw.=",$hw";
				}
			}
			unset($curl);
			//echo 'url : '.$url.'<br/>';
			//echo "get cookie : $cookie<br/>";
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$newtrad = curl_exec($curl);
			curl_close($curl);
			
			$html = str_get_html($newtrad);
			$newHtml='';
			foreach($html->find('div[id=entryDetailPage]') as $ret){
				$content = trim($ret->plaintext);
				if(!empty($content)&&!startsWith($content, 'Source:')){
					$newHtml.=$ret;
					
				}	
			}
			//echo "detail html : $html<br/>";
			//$newtrad = file_get_contents($url, false, $context);
			unset($transArr);
			//A faire : transmettre html dans un fichier xml -> uniformiser le format de sortie detaille
			$transArr=array("lemme"=>$lemma, "pos"=>$pos, "traduction"=>$l_hw, "langue"=>$lang, "html"=>$newHtml);
			array_push($transListArr, $transArr);
		}
		
	}
	return $transListArr;
 		
}
?>
