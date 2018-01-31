<?php
//resultat natif
function lemma_stanford_n ($text, $lang){
	//local
	//$local = "/home/ying/Ying/lingOutils/stanford-corenlp-full-2015-04-20/";
	//server
	$local = "/var/www/Ci-Hai/lingOutils/stanford-corenlp-full-2015-04-20/";
	$filepath = $local."temp/corenlp_temp_$lang";
	$file = fopen($filepath, "w");
	chmod($filepath, 0777);
	fwrite($file, $text);
	if(strtolower($lang)=='eng'||strtolower($lang)=='english'||strtolower($lang)=='en') {
		$command = 'java -cp '.$local.'stanford-corenlp-3.5.2.jar:'.$local.'stanford-corenlp-3.5.2-models.jar:'.$local.':xom.jar:'.$local.'joda-time.jar:'.$local.'jollyday.jar:'.$local.'ejml-3.5.2.jar -Xmx2g edu.stanford.nlp.pipeline.StanfordCoreNLP -props '.$local.'properties -file '.$filepath.' -outputDirectory '.$local.'temp';
		//echo $command;		
		shell_exec($command);
		$outputFilePath = $filepath.'.out';
		chmod($outputFilePath, 0777);
		$outputfile = fopen($outputFilePath, "r");
		$res = fread($outputfile, filesize($outputFilePath));
		fclose($filepath);
		fclose($outputFilePath);
	} else if(strtolower($lang)=='zho'||strtolower($lang)=='chinese'||strtolower($lang)=='zh'){
	
		//local
		//$command = "/home/ying/Ying/lingOutils/stanford-segmenter-2015-04-20/segment.sh -k pku $filepath UTF-8 0";
		//serveur danang
		$command = "/var/www/Ci-Hai/lingOutils/stanford-segmenter-2015-04-20/segment.sh -k pku $filepath UTF-8 0";
		//echo $command;		
		$res = shell_exec($command);
		//$res='斯坦福 的 分词器 不好用 。 ';
	}
	
	return $res;
}

function lemma_stanford_b ($text, $lang, $orgText){
	$l_arr = Array();
	if(strcasecmp($lang,'en')== 0||strcasecmp($lang,'eng')== 0){
		$l_str = explode('[', trim($text));
		for($i=1; $i<count($l_str); $i++){
			$str=$l_str[$i];
			$l_flag = explode(']',trim($str));
			$arr = array();
			$flag = $l_flag[0];
			$arr_entry = explode(' ',$flag);
			$form=$arr_entry[0];
			$form= explode('=',$form)[1];
			$start=$arr_entry[1];
			$start= explode('=',$start)[1];
			$end=$arr_entry[2];
			$end= explode('=',$end)[1];
			$pos=$arr_entry[3];
			$pos= explode('=',$pos)[1];
			$lemma=$arr_entry[4];
			$lemma= explode('=',$lemma)[1];
			$arr['form']=$form;
			$arr['pos']=$pos;
			$arr['lemma']=$lemma;
			$arr['start']=$start;
			$arr['end']=$end;
			if($pos=='NN'&&$lemma==$form){
				$arr['confidence']='guessed';
			}else{
				$arr['confidence']='reliable';
			}
			array_push($l_arr, $arr);
		}
		
	}else if(strcasecmp($lang,'zh')== 0||strcasecmp($lang,'zho')== 0){
		$l_str = explode(' ', trim($text));
		$offset = 0;
		$start = 0;
		$end = 0;
		for($i=0; $i<count($l_str); $i++){
			$form=$l_str[$i];
			unset($arr);
			$arr=array();
			$start = strpos($orgText, $form, $offset);
			$end = $start+strlen($form);
			$offset=$end;
			$arr['form']=$form;
			$arr['lemma']=$form;
			$arr['start']=$start;
			$arr['end']=$end;
			$arr['pos']='UNK';
			array_push($l_arr, $arr);
		}
	}		
	return $l_arr;
	
}
?>
