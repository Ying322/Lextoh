<?php
function lemma_jieba_n ($text, $lang){
	//local
	//$command = "python /home/ying/Ying/lingOutils/jieba-master/jiebaAPI.py $text";	
	//serveur
	$command = "python /var/www/Ci-Hai/lingOutils/jieba-master/jiebaAPI.py $text";
	$json = exec($command);
	$res = json_decode($json, TRUE);
	//print_r($res) ;
	return $res;
}

//list de arry (key, value)
function lemma_jieba_b ($res_n, $text){
	$l_arr = array();
	$lastFrom = '';
	$l_id = array();	
	$offset = 0;
	$start = 0;
	$end = 0;
	foreach($res_n as $arr){
		$form=$arr['word'];
		$pos = $arr['flag'];
		$start = strpos($text, $form, $offset);
		$end = $start+strlen($form);
		$offset=$end;
		$arr['form']=$form;
		$arr['lemma']=$form;
		$arr['pos']=$pos;
		$arr['confidence']='reliable';
		$arr['start']=$start+0;
		$arr['end']=$end;
		unset($arr['word']);
		unset($arr['flag']);
		array_push($l_arr, $arr);
	}
	return $l_arr; 
}
?>
