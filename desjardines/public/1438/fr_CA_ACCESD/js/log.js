function sortie(url, transit) {

    if (chargee) {
	  	if (transit == "aca"){
		  	window.location.href = 'https://services.desjardins.com/acadie/accd_fr.nsf/ins-accesd?openform';
	  	}else{
		  	window.location.href = 'http://www.desjardins.com' + url;
	  	}
	}
}

function navig(url, transit) {
	
    if (chargee) {

	  	if (transit == "aca"){
		  	window.location.href = 'http://www.acadie.com/accesd/fr/navigue/index.htm';
	  	}else{
		 	window.location.href = 'http://www.desjardins.com' + url;
	  	}
	}
}

function afficherVisa() {

	window.open('http://www.desjardins.com/fr/dossiers/organismes/visa/index.html', "Visa", "resizable=1,location=0,menubar=1,status=1,scrollbars=1,width=550,height=350");

}

function afficherAccessible(url) {

	window.open(url, "Accessible", "resizable=0,location=0,menubar=0,status=0,scrollbars=0,width=425,height=170");

}

var activeField = null;

function autoJump_onKeyDown(fieldName) {

	var field = document.LogonForm.elements[fieldName];

	activeField = field;

	field.lastValue = field.value;

}

function autoJump_onKeyUp(fieldName,nextFieldName,maxLength) {

	var field = document.LogonForm.elements[fieldName];

	var nextField = document.LogonForm.elements[nextFieldName];

	if (field == activeField && field.value != field.lastValue && field.value.length >= maxLength){

		nextField.focus();

		activeField = null;

	}

}

function autoFocus_onLoad() {
    if (chargee) {

      var field_card_num     = document.LogonForm.elements['card_num'];
      var field_passwd       = document.LogonForm.elements['passwd'];
      var field_reponse_defi = document.LogonForm.elements['reponseDefi'];
      var field_Continuer    = document.LogonForm.elements['Continuer'];
      var field_num_user     = document.LogonForm.elements['code_usager'];
   


      if (field_card_num != null){
         field_card_num.focus();
       }else if (field_num_user != null){
          field_num_user.focus();
       }else if (field_reponse_defi != null){
          field_reponse_defi.focus();

       }else if (field_passwd != null){
          field_passwd.focus();
       }else{
          field_Continuer.focus();
       }
    }
}

function L_naviguer(url) {

    if (chargee) {

		document.location = url;

	}

}

function L_courrier(url) {

    parent.location=url;

}

function L_naviguer_pop(url) {

	window.open(url, 'naviguerpop', 'toolbar=no,menubar=no,location=no,status=yes,scrollbars=yes,resizable=yes,width=500,height=500');

}

function L_menu(cle) {

	if (chargee) {

		var url="";

		if(cle == "inscrire"){

			url = inscrip;

		}else if(cle =="information"){

			url = info;

		}else if(cle =="navigateur"){

			url = naviga;

		}else if(cle =="pdf"){

			url = pdf;

		}else if(cle =="questions"){

			url = quest;

		}else if(cle =="service_membre"){

			url = membre;

		}else if(cle =="demande_carte"){

			url = demcarte;

		}else if(cle =="connexion"){

                        url = connexion;

                }

		L_naviguer(url);

	}

}

function L_menu_pop(cle) {

	var url="";

	if(cle == "demonstration"){

		url = demo;

    }else if(cle =="securite"){

        url = secur;

    }else if(cle =="confidentialite"){

        url = conf;

    }else if(cle =="condition"){

        url = util;

    }else if(cle=="joindre"){

        url=joind;

    }else if(cle =="vider"){

        url = vide;

    }else if(cle=="signaler"){

        url = signaler;

    }else if(cle =="proteger"){

        url = proteger;

    }

	L_naviguer_pop(url);

}

function Aide(url) {

    window.open(url, 'Aide', 'resizable=1,location=0,menubar=1,status=1,scrollbars=1,width=500,height=300');

}

function Joindre(url) {

    window.open(url, 'Joindre', 'resizable=1,location=0,menubar=1,status=1,scrollbars=1,width=500,height=300');

}

function MM_openBrWindow(theURL,winName,features) {

	window.open(theURL,winName,features);

}

function cliquerJoindre(){
	
   var lienJoindre = document.getElementById("joindreEntete");
   
   document.getElementById("joindreMessage").href=lienJoindre;
   
}