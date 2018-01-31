<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head>  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">  <title>JDico</title>  <link href="_design/style.css" rel="StyleSheet" type="text/css" /> <script src="_design/js/action.js"></script>
</head><body>
  <div id="entete">
     <img src="_design/images/world.png" style="height:100px;width:80%;" />
  </div><!-- id="entete" -->

 <div id="corps">  <form action="_include/affichage.php" id="profil" method="get" enctype="multipart/form-data">   <fieldset><legend>Paramétrage de la demande</legend><?php 
	//echo (is_file('_include/param.php'))? 'coucou' : 'pb!';
	require('_include/fonction.php');
	$l_serv = csv_to_array ('_config/l_serv.csv', "\t");
	//print_r($l_serv);


  ?><div id="c_serv">  <label for="serveur">serveur ： </label>    <select name="serveur" id="serveur" onchange="SelectDico(this.value)">      <option value="">choisir..</option>
<?php
  foreach($l_serv as $k=>$v){
if(!startsWith($k, 'dico_')) {?>      <option value="<?php echo $k ?>"><?php echo $k ?></option>
<?php
 } }
?>    </select></div><!-- id="c_serv" --><div id="c_dico">	<label for="dico">dictionaire ： </label>    <select name="dico" id="dico" onchange="SelectLang(this.value)">     <option value="">choisir un serveur d'abord</option>     </select>    <label for="ls">langue source ： </label>    <select name="ls" id="ls" >      <option value="">choisir un dictionnaire d'abord</option>        </select></div><!-- id="c_dico" --><div id="c_lemmatiseur"><label for="lemma">lemmatiseur ： </label>    <select name="lemma" id="lemma">     <option value="">choisir un lemmatiseur</option>     </select></div> <!--id="c_lemmatiseur"--><div id="c_sortie"><label for="sortie">format de sortie ： </label>    <select name="sortie" id="sortie">     <option value="">choisir un format de sortie</option>     </select></div> <!--id="c_lemmatiseur"--><div><label for="segment">segment ：</label><input type="txt" size="60px" value="" name="segment" id="segment"/></div><p class="submit"><input type="submit" id="boutton-submit" value="Envoyer" /></p>

    </fieldset>  </form>  <div class="description"> 
    <p> 
Vous pouvez également utiliser le URL : /_include/affichage.php?ls=[langueSource]&serveur=[Serveur]&dico=[dictionnaire]&segment=[Votre+segment]
    </p><p> 
Par exemple : /_include/affichage.php?ls=fra&serveur=papillon&dico=DicofulUS-index&segment=Tu+a+une+bonne+chance 
    </p>  </div><!-- class="description" --> </div><!-- id="corps" -->

<!--iframe id="connecteur" src="http://getalp.imag.fr/pivax/" ></iframe-->
  <div id="signature">
    &copy; Ying Zhang
  </div></body></html>