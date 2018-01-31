<?php
	function getXeldaRes($str){
		$arr = explode('*', $str);
		for($m=1; $m<count($arr); $m++){
			$text = $arr[$m];
			echo $m.' : '.$text.'<br/>';
		}
	}
	
	function getXipRes($str){
		$res = explode('0>TOP', $str)[1];
		$NPArray = explode('NP{',$res);
		for($m=1; $m<count($NPArray); $m++){
			$NP = $NPArray[$m];
			//echo $NP;
			$posPD = strpos($NP, "}");
			$posPG = strpos($NP, "{");
			//echo $posPD.'---'.$posPG.'<br/>';
			if($posPG==false || $posPD<$posPG){	
				$NP = substr($NP, 0, $posPD);
				echo $m.' : '.$NP.'<br/>';
			}else {
				$arr = explode(' ', $NP);
				//print_r($arr);
				$r = '';
				for($n=0; $n<count($arr); $n++){
					$a = $arr[$n];
					if(!strpos($a, "{")&&!strpos($a, "}")){
					 $r.=$a;
					}else{
						if(strpos($a, "{")){
							$p1=strpos($a, "{");
							$p2=strpos($a, "}");
							//echo "p1=$p1, p2=$p2";
							$r.=substr($a, $p1+1, $p2-$p1-1);	
						}else {
							$r.=substr($a, 0, strpos($a,"}"));	
						}
					}
					if($n<count($arr)){
					 $r.=" ";
					}
				}
				echo $m.' : '.$r.'<br/>';
			}
		}
	}
   
?>
