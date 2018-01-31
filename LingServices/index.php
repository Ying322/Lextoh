<?php

include ('../functions.php');
// Lire les paramètres
  $service = stripslashes($_POST['serv']?$_POST['serv']:$_GET['serv']);
  $lang = stripslashes($_POST['lang']?$_POST['lang']:$_GET['lang']);
  $text = stripslashes($_POST['text']?$_POST['text']:$_GET['text']);
  $input = stripslashes($_POST['input']?$_POST['input']:$_GET['input']);
  $output = stripslashes($_POST['output']?$_POST['output']:$_GET['output']);
  $tokenType = stripslashes($_POST['tokenType']?$_POST['tokenType']:$_GET['tokenType']);
  $morphoAnalysisType = stripslashes($_POST['maType']?$_POST['maType']:$_GET['maType']);
  $morphoAnalysisLevel = stripslashes($_POST['maLevel']?$_POST['maLevel']:$_GET['maLevel']);
  $position = stripslashes($_POST['position']?$_POST['position']:$_GET['position']);
  $disambiguationType = stripslashes($_POST['disamType']?$_POST['disamType']:$_GET['disamType']);
  $extractionType = stripslashes($_POST['extraType']?$_POST['extraType']:$_GET['extraType']);
  $extractionPattern = stripslashes($_POST['extraPat']?$_POST['extraPat']:$_GET['extraPat']);
  $targetLang = stripslashes($_POST['tl']?$_POST['tl']:$_GET['tl']);
  $dictionaryLookupType = stripslashes($_POST['dicoType']?$_POST['dicoType']:$_GET['dicoType']);
  $dictionaryLookupLevel = stripslashes($_POST['dicoLevel']?$_POST['dicoLevel']:$_GET['dicoLevel']);
  $dictionaryIdiomStrategy = stripslashes($_POST['dicoIdiom']?$_POST['dicoIdiom']:$_GET['dicoIdiom']);
  $dictionaryDomainStrategy = stripslashes($_POST['dicoDom']?$_POST['dicoDom']:$_GET['dicoDom']);
  $languageIdentificationType = stripslashes($_POST['langIdType']?$_POST['langIdType']:$_GET['langIdType']);
  $languageList = stripslashes($_POST['langList']?$_POST['langList']:$_GET['langList']);
  $api = stripslashes($_POST['api']?$_POST['api']:$_GET['api']);

if(!$api){
	include('../menu.php');
}
//echo "service=$service text=$text lang=$lang input=$input output=$output tokenType=$tokenType maType=$morphoAnalysisType </br>";
if(!$service||!$text||!$lang||!$input||!$output){
//Documentation
?>
    <html>
    <body bgcolor="#F4f4f4" text="#333333" link="#CC0000" vlink="#CC0000" alink="#CC0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <br/>
    <div>Works with POST or GET</div>
    <div>Services from Xelda.</div>
    <div>Arguments: 
    <ul>
      <li><i>serv</i> : Service 
      	<ul>
      		<li>tokenization (args : serv, input, output, text, lang, tokenType)</li>
      		<li>morphoanalysis (args : serv, input, output, text, lang, tokenType, maType, [MorphoAnalysisLevel])</li>
      		<li>disambiguation (args : serv, input, output, text, lang, position, tokenType, maType, disamType)</li>
      		<li>textextraction (args : serv, input, output, text, lang, position, tokenType, maType, disamType, extraType, extraPat)</li>
      		<li>dictionarylookup (args : serv, input, output, text, lang, tl, position, tokenType, maType, disamType, dicoType, dicoLevel, dicoIdiom, [DictionaryDomainStrategy])</li>
      		<li>languageidentification (args : serv, input, output, text, position, langType, langList)</li> 
      	</ul>
      	</li>
      <li><i>lang</i> : Language 
      	<ul>
      		<li>plain English name</li> 
      		<li>ISO639-1 Alpha-2 language name code</li> 
      		<li>ISO639-2 Alpha-3 bibliographic language name code</li> 
      		<li>ISO639-2 Alpha-3 terminology language name code) </li> 
      	</ul>
      	</li>
      <li><i>text</i></li>
      <li><i>input</i> : Input Format 
      	<ul>
      		<li>xml</li>
      		<li>html</li>
      		<li>PlainText</li>
      	</ul>
	  </li>
      <li><i>output</i> : Output Format 
      	<ul>
      	 <li>xml</li>
      	 <li>xmlfragment</li>
      	 <li>html</li>
      	 <li>text</li>
      	 <li>dhtml</li>
      	</ul></li>
      <li><i>tokenType</i> : Tokenization Type 
      	<ul>
      		<li>Basic: corresponds to the rough tokenizer, which is used to tokenize unsupported languages. </li>
      		<li>FST: corresponds to the finite-state transducer tokenizer, which is used for all supported languages. </li>
      		<li>FSTCustom: corresponds to a custom finite-state transducer tokenizer. </li>
      		<li>FSTSentenceSegmentation: corresponds to a particular finite-state transducer tokenizer, which is used to segment a text into sentences.</li>
      	</ul>
      </li>
      <li><i>maType</i> : MorphoAnalysis Type 
      <ul>
      	<li>FSTPOSTag: uses a finite-state morphological analyzer which returns one condensed part-of-speech tag with each analysis.</li>
      	<li>FSTCustom: uses of a custom finite-state morphological analyzer.</li>
      	<li>RelationalMorphoAnalysis: uses the relational morphology transducer.</li>
      	<li>ReverseRelationalMorphoAnalysis: uses the reverse relational morphology transducer.</li>
      	<li>FSTMorphoTags (not available by default, contact xelda-support for more information): uses a finite-state morphological analyzer which returns all the morphological tags with each analysis.</li>
      	<li>FSTAllTags: uses a finite-state morphological analyzer that returns all the morphological tags and a final reduced part-of-speech tag.</li>
      </ul>
      </li>
      <li><i>maLevel</i> : MorphoAnalysis Level
      <ul>
      	<li>Lookup: indicates that the lexical transducer will be used for morphological analysis (from lower-side to upper-side).</li>
      	<li>Lookdown: indicates that the lexical transducer will be used for morphological generation (from upper-side to lower-side).</li>
      </ul>
      </li>
      <li><i>position</i> : Part of the text to process. The format is startoffset-endoffset, with the first position being zero (0). If you specify startoffset only, it will returns only the result for the word at this position. If you specify “-“ , the whole input is considered.</li>
      <li><i>disamType</i> : Disambiguation Type
      <ul><li>HMM: uses the Hidden Markov Model, a mathematical algorithm.</li></ul></li>
      <li><i>extraType</i> : TextExtraction Type
      <ul><li>FSTNounPhrase: extracts noun phrases.</li><li>FSTCustomPhrase: extract user-defined phrases. (Reserved for future use).</li></ul></li>
      <li><i>extraPat</i> : TextExtraction Pattern
      <ul><li>Max: indicates that only maximum-length noun phrases are extracted.</li><li>Sub: indicates that maximum-length noun phrases and their sub noun phrases are extracted.</li></ul>
      </li>
      <li><i>tl</i> : target language</li>
      <li><i>dicoType</i> : DictionaryLookup Type
      <ul><li>WithDisambiguation: relevant parts of the dictionary entry are extracted according to the part of speech information (contextual).</li><li>WithoutDisambiguation: the full dictionary entry is returned without taking into account the part of speech information.</li></ul>
      </li>
      <li><i>dicoLevel</i> : DictionaryLookup Level
      <ul><li>WithExamples: examples are included in the dictionary lookup entry.</li><li>WithoutExamples: no examples are displayed in the dictionary lookup entry</li></ul>
      </li>
      <li><i>dicoIdiom</i> : DictionaryLookup Idiom Strategy
      <ul><li>WithIdioms: indicates that you want the service to return idioms.</li><li>WithoutIdioms: indicates not to find any idiom.</li></ul>
      </li>
      <li><i>dicoDom</i> : DictionaryLookup Domain Strategy
      <ul><li>Addition sign (+): indicates to always look in this dictionary. For example, "math, science, +general, default" means to first look in the "math" dictionary. If the word is not found, look in the "science" dictionary. Always look in the "general" dictionary. If the word is not found in one of the previous dictionaries, look in the "default" dictionary.</li><li>Subtraction sign (-): indicates to always look in this dictionary and remove previous results if the word is found in this dictionary. For example, "general, -math" means to first look in the "general" dictionary and to always look into the "math" dictionary. If the entry is found, replace the result from the "general" dictionary with this entry.</li><li>Question mark (?): indicates that if the dictionary does not exist, an error is not returned. For example, "general, math?" means that if the "math" dictionary is not found, the system will not return an error. However, if the "general" dictionary is not found, the system will generate an error.</li></ul>
      </li>
      <li><i>langIdType</i> : LanguageIdentification Type
      <ul><li>TRISHORT: the only language identifier available so far. It uses a combination of trigram and shortword statistical techniques.</li></ul>
      </li>
      <li><i>langList</i> : LanguageIdentification Strategy. You can provide a list of languages when you create a language identification request object to help narrow the search.
      <ul><li>If the list is empty or the first character is an asterisk (*), the server will use its default list of languages.
The default list of languages is defined in the LanguageIdentification.TRISHORT.defaultLanguagesList resource.</li><li>f the list is in the format "[+-]item1 [+-]item2 ...", then the list is derived from the server default list of languages where each item starting with a + is added to the list and each item starting with a – is removed from the default list.</li><li>If the list is not empty and the first character is not an asterisk (*), addition sign (+), or dash (-), then the items correspond to the exact list of languages.</li></ul>
      </li>
    </ul></div>
    Exemple:
    <br/>
 <?php
      $service = "tokenization";	
      $text = "This is my first test.";
      $lang = 'eng'; 
      $input = 'plaintext';
      $output = 'text';
      $tokenType = 'FST';

      print "serv=$service&lang=$lang&text=$text&output=$output&input=$input&tokenType=$tokenType<br/>";
 }

$text = minuscula($text);

//local
//$mainCommand = "/home/ying/Ying/lingOutils/xelda/bin/xelda.sh -connection direct -name XeldaServer ";
//serveur danang
$mainCommand = "/var/www/Ci-Hai/lingOutils/xelda/bin/xelda.sh -connection direct -name XeldaServer ";
if(strtolower($service)=='tokenization') $command = $mainCommand."$service $input $output \"$text\" $lang $tokenType";
if(strtolower($service)=='morphoanalysis') $command = $mainCommand."$service $input $output \"$text\" $lang $tokenType $morphoAnalysisType $morphoAnalysisLevel";
if(strtolower($service)=='disambiguation') $command = $mainCommand."$service $input $output \"$text\" $lang $position $tokenType $morphoAnalysisType $disambiguationType";
if(strtolower($service)=='textextraction') $command = $mainCommand."$service $input $output \"$text\" $lang $position $tokenType $morphoAnalysisType $disambiguationType $extractionType $extractionPattern";
if(strtolower($service)=='dictionarylookup') $command = $mainCommand."$service $input $output \"$text\" $lang $targetLang $position $tokenType $morphoAnalysisType $disambiguationType $dictionaryLookupType $dictionaryLookupLevel $dictionaryIdiomStrategy $dictionaryDomainStrategy";
if(strtolower($service)=='languageidentification') $command = $mainCommand."$service $input $output \"$text\" $position $languageIdentificationType $languageList";

print shell_exec($command);

?>

