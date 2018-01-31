/*function SelectDico(serveur){
	if(serveur=="papillon"){
		document.getElementById("dico").options.length=0;
		var All = document.createElement("OPTION");
		All.text="Tous";
		All.value="*";
	//	document.getElementById("dico").add(All);
		var Cedict = document.createElement("OPTION");
		Cedict.text="Cedict";
		Cedict.value="Cedict";
		document.getElementById("dico").add(Cedict);	
		var DicofulUS = document.createElement("OPTION");
		DicofulUS.text="DicofulUS-index";
		DicofulUS.value="DicofulUS-index";
		document.getElementById("dico").add(DicofulUS);
		var FeM = document.createElement("OPTION");
		FeM.text="FeM";
		FeM.value="FeM";
		document.getElementById("dico").add(FeM);
		var JMdict = document.createElement("OPTION");
		JMdict.text="JMdict";
		JMdict.value="JMdict";
		document.getElementById("dico").add(JMdict);
		var Littre = document.createElement("OPTION");
		Littre.text="Littre";
		Littre.value="Littre";
		document.getElementById("dico").add(Littre);
		var Papillon = document.createElement("OPTION");
		Papillon.text="Papillon";
		Papillon.value="Papillon";
		document.getElementById("dico").add(Papillon);
		var VietDict = document.createElement("OPTION");
		VietDict.text="Littre";
		VietDict.value="Littre";
		document.getElementById("dico").add(VietDict);	
	}else if(serveur=="pivax"){
		document.getElementById("dico").options.length=0;
		var All = document.createElement("OPTION");
		All.text="Tous";
		All.value="*";
	//	document.getElementById("dico").add(All);
		var CommonUNLDict = document.createElement("OPTION");
		CommonUNLDict.text="CommonUNLDict";
		CommonUNLDict.value="CommonUNLDict";
		document.getElementById("dico").add(CommonUNLDict);
	}
}*/

var xmlHttp;

function SelectDico(serveur){

	document.getElementById("ls").options.length=0;
	var lang = document.createElement("OPTION");
	lang.text="choisir un dictionnaire d'abord";
	document.getElementById("ls").add(lang);

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
				var n_dico = l_dico_ls[i].split(":")[1];
				n_dico = n_dico.split("<br/>")[0];
				if(n_dico!=null){
					var dico = document.createElement("OPTION");
					dico.text=n_dico;
					dico.value=n_dico;
					document.getElementById("dico").add(dico);
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

function SelectLang(dico){
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 

		if(dico=="choisir le dictionnaire"){
			document.getElementById("ls").options.length=0;
			var lang_null = document.createElement("OPTION");
			lang_null.text="choisir un dictionnaire d'abord";
			document.getElementById("ls").add(lang_null);
		} else{
			var doc = xmlHttp.responseText;
			var l_dico_ls = doc.split("<br/><br/>");

			if(dico!="Tous"){
				if(l_dico_ls.length>0){
					for (i=0; i<l_dico_ls.length; i++){
						var n_dico = l_dico_ls[i].split(":")[1];
						n_dico = n_dico.split("<br/>")[0];
						if(n_dico==dico){
							var langs = l_dico_ls[i].split(":")[2];
							var l_lang = langs.split(",");	
							if(l_lang.length>0){
								document.getElementById("ls").options.length=0;
								var opt = document.createElement("OPTION");
								opt.text="choisir la langue source";
								document.getElementById("ls").add(opt);
								for(j=0;j<l_lang.length;j++){
									var n_lang = l_lang[j].trim();
									if(n_lang.length>0){
										var lang = document.createElement("OPTION");
										lang.text=n_lang;
										lang.value=n_lang;
										document.getElementById("ls").add(lang);
									} //fin du if(n_lang.length>0)
								} //fin du for(j=0;j<l_lang.length;j++)
							} // fin du if(l_lang.length>0)
							else {
								document.getElementById("ls").options.length=0;
								var lang_null = document.createElement("OPTION");
								lang_null.text="choisir un dictionnaire d'abord";
								document.getElementById("ls").add(lang_null);
							}
						}	//fin du if(n_dico==dico)
					}
				} // fin du if(l_dico_ls.length>0)
			}// fin du if(dico!="Tous")
			else {
				document.getElementById("ls").options.length=0;
				var opt = document.createElement("OPTION");
				opt.text="choisir la langue source";
				document.getElementById("ls").add(opt);
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
					document.getElementById("ls").add(lang);
				}	
			} //fin du else		
		}
	} //fin du if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	
}