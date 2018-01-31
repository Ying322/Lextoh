function selectLemma(lemma, jArray){ 
		document.getElementById("lang").options.length=0;
		for (i=0; i<jArray.length; i++){
			var temp_lemma = jArray[i].lemma;
			var temp_lang = jArray[i].langs;
			var temp_window = jArray[i].window;
			if(lemma==temp_lemma){
				var llang=temp_lang.split(",");
				for (j=0; j<llang.length; j++){
					var lang = document.createElement("OPTION");
					lang.text=llang[j];
					lang.value=llang[j];
					document.getElementById("lang").add(lang);
				}
				if(temp_window.toLowerCase()=="yes"||
					temp_window.toLowerCase()=="oui"||
					temp_window.toLowerCase()=="true"||
					temp_window==1){
					document.getElementById("win").removeAttribute("hidden"); 
				}else{
					document.getElementById("win").setAttribute("hidden", "hidden");
				}
			}
			
		}
		if(document.getElementById("lemma").value=="stanford-segmenter"){
			alert("Attention : le chargement de cet outil est lourd. C'est inutile pour les textes courts. En plus, cet outil n'analyse pas la partie du discours du Chinois. Nous vous proposons d'utiliser l'outil \"jieba\" pour analyser le Chinois.");
		}
}

function showAlert(lang){
//	if(document.getElementById("lemma").value=="stanford-segmenter"&&lang=="zho"){
//		alert("Attention : le chargement/initialement de cet outil est très lent et très lourd. C'est inutile pour les textes courts. En plus, cet outil n'analyse pas la partie du discours du Chinois. Nous vous proposons d'utiliser l'outil \"jieba\" pour analyser le Chinois.");
//	}
}

function modeAvance(){
	if(document.getElementById("advmode").checked) {
		document.getElementById("resultavance").removeAttribute("hidden"); 
		document.getElementById("resultsimple").setAttribute("hidden", "hidden");
	}
	else {
		document.getElementById("resultavance").setAttribute("hidden", "hidden");
		document.getElementById("resultsimple").removeAttribute("hidden"); 
	}
}

function setFormatPersoFormulaire (vformat){
	var format=vformat.split("_")[1];
	var formalisme=vformat.split("_")[0];
	alert (format+formalisme);
}

function getResulatAvance(value){
	if(value=="Sortie naïve"){
		document.getElementById("resNaive").removeAttribute("hidden"); 
		document.getElementById("naive").style.font = "italic bold 100% arial";
		document.getElementById("brute").style.font = "";
		document.getElementById("lemmatint").style.font = "";
		document.getElementById("lemmatfinal").style.font = "";
		document.getElementById("final").style.font = "";
		document.getElementById("resBrute").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatint").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatfinal").setAttribute("hidden", "hidden");
		document.getElementById("resFinal").setAttribute("hidden", "hidden");
	}else if(value=="Sortie brute"){
		document.getElementById("resBrute").removeAttribute("hidden"); 
		document.getElementById("brute").style.font = "italic bold 100% arial";
		document.getElementById("naive").style.font = "";
		document.getElementById("lemmatint").style.font = "";
		document.getElementById("lemmatfinal").style.font = "";
		document.getElementById("final").style.font = "";
		document.getElementById("resNaive").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatint").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatfinal").setAttribute("hidden", "hidden");
		document.getElementById("resFinal").setAttribute("hidden", "hidden");
	}else if(value=="Lemmatix interm"){
		document.getElementById("resLemmatint").removeAttribute("hidden"); 
		document.getElementById("lemmatint").style.font = "italic bold 100% arial";
		document.getElementById("naive").style.font = "";
		document.getElementById("brute").style.font = "";
		document.getElementById("lemmatfinal").style.font = "";
		document.getElementById("final").style.font = "";
		document.getElementById("resBrute").setAttribute("hidden", "hidden");
		document.getElementById("resNaive").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatfinal").setAttribute("hidden", "hidden");
		document.getElementById("resFinal").setAttribute("hidden", "hidden");
	}else if(value=="Lemmatix final"){
		document.getElementById("resLemmatfinal").removeAttribute("hidden"); 
		document.getElementById("lemmatfinal").style.font = "italic bold 100% arial";
		document.getElementById("naive").style.font = "";
		document.getElementById("brute").style.font = "";
		document.getElementById("lemmatint").style.font = "";
		document.getElementById("final").style.font = "";
		document.getElementById("resBrute").setAttribute("hidden", "hidden");
		document.getElementById("resNaive").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatint").setAttribute("hidden", "hidden");
		document.getElementById("resFinal").setAttribute("hidden", "hidden");
	}else if(value=="Sortie finale"){
		document.getElementById("resFinal").removeAttribute("hidden"); 
		document.getElementById("final").style.font = "italic bold 100% arial";
		document.getElementById("naive").style.font = "";
		document.getElementById("brute").style.font = "";
		document.getElementById("lemmatint").style.font = "";
		document.getElementById("lemmatfinal").style.font = "";
		document.getElementById("resBrute").setAttribute("hidden", "hidden");
		document.getElementById("resNaive").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatint").setAttribute("hidden", "hidden");
		document.getElementById("resLemmatfinal").setAttribute("hidden", "hidden");
	}
}

function persFormat(){
	if(document.getElementById("persform").checked) {
		document.getElementById("personal").removeAttribute("hidden"); 
	}
	else {
		document.getElementById("personal").setAttribute("hidden", "hidden");
	}
}

function checkOptions(lang, lemma, output, formule, jArray, formalism ){
	selectLemma(lemma, jArray);
	document.querySelector('#lemma>option[value="'+lemma+'"]').selected = true;
	document.querySelector('#lang>option[value="'+lang+'"]').selected = true;
	document.querySelector('#output>option[value="'+output+'"]').selected = true;
	document.querySelector('#formule').checked = formule;
	document.querySelector('#formalism>option[value="'+formalism+'"]').selected = true;
}

