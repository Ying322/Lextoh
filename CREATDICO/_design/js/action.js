var xmlHttp;

function updateDicoList(serveur, langArray){
	document.getElementById("lang").options.length=0;
	var lang = document.createElement("OPTION");
	lang.text="choisir un dictionnaire d'abord";
	document.getElementById("lang").add(lang);
	
	document.getElementById("lt").options.length=0;
	var lt = document.createElement("OPTION");
	lt.text="choisir un dictionnaire d'abord";
	document.getElementById("lt").add(lt);
	
	if(serveur=="iate"||serveur=="wiktionary"){
		//set dico option
		document.getElementById("dico").options.length=0;
		var dico_serv = document.createElement("OPTION");
		dico_serv.text=serveur;
		dico_serv.value=serveur;
		document.getElementById("dico").add(dico_serv);
		//set lang option
		document.getElementById("lang").options.length=0;
		document.getElementById("lt").options.length=0;
		for (i=0; i<langArray.length; i++){
			var temp_lemma = langArray[i].lemma;
			if(temp_lemma==serveur){
				var temp_lang = langArray[i].langs;
				var llang=temp_lang.split("/");
				for(j=0; j<llang.length; j++){
					var lang = document.createElement("OPTION");
					lang.text=llang[j];
					lang.value=llang[j];
					document.getElementById("lang").add(lang);
					
					var lt = document.createElement("OPTION");
					lt.text=llang[j];
					lt.value=llang[j];
					document.getElementById("lt").add(lt);
				}	
			}
				
		}
	}else { //instance de jibiki
		if (serveur.length!=0){	
			xmlHttp=GetXmlHttpObject();
			if (xmlHttp==null) {
	  			alert ("Browser does not support HTTP Request");
	  			return null;
	  		}
	  		var url="_include/param.php?serv="+serveur;
	  		xmlHttp.onreadystatechange=stateChanged; 
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);
		}else {
			document.getElementById("dico").options.length=0;
			var dico_null = document.createElement("OPTION");
			dico_null.text="choisir un serveur d'abord";
			document.getElementById("dico").add(dico_null);
			
		}
	}
}

//no jibiki instance
function setLangOption(serv, langList){
	document.getElementById("lang").options.length=0;

}

function stateChanged(){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var doc = xmlHttp.responseText;
		var l_dico_ls = doc.split("<br/><br/>");
		
		if(l_dico_ls.length>0){
			document.getElementById("dico").options.length=0;
			var opt = document.createElement("OPTION");
			opt.text="choisir le dictionnaire";
			document.getElementById("dico").add(opt);
			var All = document.createElement("OPTION");
			All.text="Tous";
			All.value="Tous";
			document.getElementById("dico").add(All);
			for (i=0; i<l_dico_ls.length; i++){
				if(l_dico_ls[i].indexOf(":")>=0){
					var n_dico = l_dico_ls[i].split(":")[1];
					n_dico = n_dico.split("<br/>")[0];
					if(n_dico!=null){
						var dico = document.createElement("OPTION");
						dico.text=n_dico.trim();
						dico.value=n_dico.trim();
						document.getElementById("dico").add(dico);
					}	
				}
			}
		}else {
			document.getElementById("dico").options.length=0;
			var dico_null = document.createElement("OPTION");
			dico_null.text="choisir un serveur d'abord";
			document.getElementById("dico").add(dico_null);
		}
		
		
	}
}

function GetXmlHttpObject() { 
	var xmlHttp=null;
	try {
 		// Firefox, Opera 8.0+, Safari
 		xmlHttp=new XMLHttpRequest();
 	} catch (e) {
 		// Internet Explorer
 		try {
  			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  		} catch (e) {
  			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  		}
	 }
	return xmlHttp;
}

function updateLangList(dico){
	//alert("updateLangList"+dico);
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 

		if(dico=="choisir le dictionnaire"){
			document.getElementById("lang").options.length=0;
			var lang_null = document.createElement("OPTION");
			lang_null.text="choisir un dictionnaire d'abord";
			document.getElementById("lang").add(lang_null);
			
			document.getElementById("lt").options.length=0;
			var lt_null = document.createElement("OPTION");
			lt_null.text="choisir un dictionnaire d'abord";
			document.getElementById("lt").add(lt_null);
		} else{
			var doc = xmlHttp.responseText;
			var l_dico_ls = doc.split("<br/><br/>");

			if(dico!="Tous"){
				if(l_dico_ls.length>0){
					for (i=0; i<l_dico_ls.length; i++){
						if(l_dico_ls[i].indexOf(":")>=0){
							var n_dico = l_dico_ls[i].split(":")[1];
							n_dico = n_dico.split("<br/>")[0];
							n_dico = n_dico.trim();
							if(n_dico==dico){
								var langs = l_dico_ls[i].split(":")[2];
								var l_lang = langs.split(",");	
								if(l_lang.length>0){
									document.getElementById("lang").options.length=0;
									var opt = document.createElement("OPTION");
									opt.text="choisir la langue source";
									document.getElementById("lang").add(opt);
									
									document.getElementById("lt").options.length=0;
									var opt_lt = document.createElement("OPTION");
									opt_lt.text="choisir la langue cible";
									document.getElementById("lt").add(opt_lt);
									
									for(j=0;j<l_lang.length;j++){
										var n_lang = l_lang[j].trim();
										if(n_lang.length>0){
											var lang = document.createElement("OPTION");
											lang.text=n_lang;
											lang.value=n_lang;
											document.getElementById("lang").add(lang);
											
											var langTar = document.createElement("OPTION");
											langTar.text=n_lang;
											langTar.value=n_lang;
											document.getElementById("lt").add(langTar);
											
										} //fin du if(n_lang.length>0)
									} //fin du for(j=0;j<l_lang.length;j++)
								} // fin du if(l_lang.length>0)
								else {
									document.getElementById("lang").options.length=0;
									var lang_null = document.createElement("OPTION");
									lang_null.text="choisir un dictionnaire d'abord";
									document.getElementById("lang").add(lang_null);
									
									document.getElementById("lt").options.length=0;
									var lt_null = document.createElement("OPTION");
									lt_null.text="choisir un dictionnaire d'abord";
									document.getElementById("lt").add(lt_null);
								}
							}	//fin du if(n_dico==dico)
						}
					}
				} // fin du if(l_dico_ls.length>0)
			}// fin du if(dico!="Tous")
			else {
				document.getElementById("lang").options.length=0;
				var opt = document.createElement("OPTION");
				opt.text="choisir la langue source";
				document.getElementById("lang").add(opt);
				var arr = new Array(0);
				if(l_dico_ls.length>0){		
					for (i=0; i<l_dico_ls.length; i++){
						if(l_dico_ls[i].length>0){
						var langs = l_dico_ls[i].split(":")[2];
						var l_lang = langs.split(",");	
						for(j=0;j<l_lang.length;j++){
							if(l_lang[j].trim().length>0){
								var isExist = false;
								for(l=0; l<arr.length; l++){								
									if(arr[l]==l_lang[j]){
										isExist=true;
										break;
									}
								}
								if (!Boolean(isExist)){	
									arr.push(l_lang[j]);							
								}
							}
							}
						}
					} 	// fin du for (i=0; i<l_dico_ls.length; i++) 
				}
				for (i=0; i<arr.length; i++){					
					var n_lang = arr[i];
					var lang = document.createElement("OPTION");
					lang.text=n_lang;
					lang.value=n_lang;
					document.getElementById("lang").add(lang);
				}	
			} //fin du else		
		}
	} //fin du if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	
}

function updateLemmaList(lang, jArray){
	document.getElementById("lemma").options.length=0;
	for (i=0; i<jArray.length; i++){
		var temp_lemma = jArray[i].lemma;
		var temp_lang = jArray[i].langs;
		var llang=temp_lang.split(",");
		if(llang.indexOf(lang)>=0){
			var lemma = document.createElement("OPTION");
			lemma.text=temp_lemma;
			lemma.value=temp_lemma;
			document.getElementById("lemma").add(lemma);
		}		
	}
	
}

function checkOptions(lang, lemma, dico, serv, output, formule, jArray, langArray){
	updateLemmaList(lang,jArray);
	document.querySelector('#serv>option[value="'+serv+'"]').selected = true;
	updateDicoList(serv, langArray);
	document.querySelector('#lemma>option[value="'+lemma+'"]').selected = true;
	document.querySelector('#output>option[value="'+output+'"]').selected = true;
	document.querySelector('#formule').checked = formule;

	setTimeout(function(){
		document.querySelector('#dico>option[value="'+dico+'"]').selected = true;
	//},1000);

	
	//document.querySelector('#dico>option[value="'+dico+'"]').selected = true;
		updateLangList(dico);
		document.querySelector('#lang>option[value="'+lang+'"]').selected = true;
	},1000);
}
