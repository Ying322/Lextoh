<?php
  $lang = stripslashes($_POST['lang']?$_POST['lang']:$_GET['lang']);
  $text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
  if(strtolower($lang)=='eng'||strtolower($lang)=='english'||strtolower($lang)=='en') {
		$command = "echo \"$text\" | /home/zhangy/xip-13.00-28/bin/linux64/xipparse-en -a";
	} else if(strtolower($lang)=='fra'||strtolower($lang)=='french'||strtolower($lang)=='fr'){
		$command = "echo \"$text\" | /home/zhangy/xip-13.00-28/bin/linux64/xipparse-fr -a";
	}
	$res = shell_exec($command); // resultat en codage ISO 8859-1
	$res = utf8_encode($res);
	echo $res;
?>
