<html>
<head>
<title>Extroh</title>
</head>
<?php

include ('../functions.php');
include ('_include/getResult.php');
include('../menu.php');
// Lire les paramètres
  $tool = stripslashes($_POST['tool']?$_POST['tool']:$_GET['tool']);
  $lang = stripslashes($_POST['lang']?$_POST['lang']:$_GET['lang']);
  $text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
  $input = stripslashes($_POST['input']?$_POST['input']:$_GET['input']);
  $output = stripslashes($_POST['output']?$_POST['output']:$_GET['output']);
  $tokenType = stripslashes($_POST['tokenType']?$_POST['tokenType']:$_GET['tokenType']);
  $morphoAnalysisType = stripslashes($_POST['maType']?$_POST['maType']:$_GET['maType']);
  $position = stripslashes($_POST['position']?$_POST['position']:$_GET['position']);
  $disambiguationType = stripslashes($_POST['disamType']?$_POST['disamType']:$_GET['disamType']);
  $extractionType = stripslashes($_POST['extraType']?$_POST['extraType']:$_GET['extraType']);
  $extractionPattern = stripslashes($_POST['extraPat']?$_POST['extraPat']:$_GET['extraPat']);


//echo "service=$service text=$text lang=$lang input=$input output=$output tokenType=$tokenType maType=$morphoAnalysisType </br>";

if(!$tool||!$lang||!$text){
//Documentation
?>
    <html>
    <body bgcolor="#F4f4f4" text="#333333" link="#CC0000" vlink="#CC0000" alink="#CC0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <br/>
    <div>Works with POST or GET</div>
    <div>Services from Xelda and xip</div>
    <div>Arguments: 
    <ul>
      <li><i>tool</i> : xelda or xip</li>
      <li><i>text</i></li> 
      <li><i>lang</i></li> 
 <!--     <li><i>input</i></li> 
      <li><i>output</i></li> 
      <li><i>tokenType</i></li> 
      <li><i>position</i></li> 
      <li><i>morphoAnalysisType</i></li> 
      <li><i>disambiguationType</i></li> 
      <li><i>extractionType</i></li>
      <li><i>extractionPattern</i></li>
    --!>
    </ul></div>
    Exemple:
    <br/>
 <?php
      $tool = "xelda";	
      $text = "My first test is a xelda test.";
      $lang = 'eng';
     
      print "tool=$tool&lang=$lang&text=$text".'<br/>';
 }

$text = minuscula($text);
//-connection direct -name XeldaServer TextExtraction plaintext text "This is a pretty good test." English - FST FSTPOSTag HMM FSTNounPhrase Max
if(strtolower($tool)=='xelda'){
 	  $input = 'plaintext';
      $output = 'text';
      $tokenType = 'FST';
	  $position ='-';
	  $morphoAnalysisType = 'FSTPOSTag';
	  $disambiguationType =  'HMM';
	  $extractionType = 'FSTNounPhrase';
	  $extractionPattern = 'Max';
	  $command = "/home/moses/lingOutils/xelda/xelda/bin/xelda.sh -connection direct -name XeldaServer TextExtraction $input $output \"$text\" $lang $position $tokenType $morphoAnalysisType $disambiguationType $extractionType $extractionPattern";
	  getXeldaRes(shell_exec($command));
}

if(strtolower($tool)=='xip'){
	if(strtolower($lang)=='eng'||strtolower($lang)=='english'||strtolower($lang)=='en') {
		$command = "echo \"$text\" | /home/moses/lingOutils/xip-13.00-28/bin/linux64/xipparse-en";
	} else if(strtolower($lang)=='fra'||strtolower($lang)=='french'||strtolower($lang)=='fr'){
		$command = "echo \"$text\" | /home/moses/lingOutils/xip-13.00-28/bin/linux64/xipparse-fr";
	}
	getXipRes(shell_exec($command));
}




?>

