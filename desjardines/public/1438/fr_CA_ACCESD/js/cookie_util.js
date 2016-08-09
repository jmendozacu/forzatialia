function setCookie (nom, value) {
  var argv = setCookie.arguments;
  var argc = setCookie.arguments.length;
  var expires = (argc > 2) ? argv[2] : null;
  var path = (argc > 3) ? argv[3] : null;
  var domain = (argc > 4) ? argv[4] : null;
  var secure = (argc > 5) ? argv[5] : false;
  document.cookie = nom + "=" + value +
    ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
    ((path == null) ? ("; path=/") : ("; path=" + path)) +
    ((domain == null) ? ("") : ("; domain=" + domain)) +
    ((secure == true) ? "; secure" : "");
}
function getCookie(name) {
   var arg = name+"=";
   var alen = arg.length;
   var clen = document.cookie.length;
   var i = 0;
   while (i < clen) {
      var j = i + alen;
      if (document.cookie.substring(i, j) == arg) return getCookieVal(j);
      i = document.cookie.indexOf(" ", i) + 1;
      if (i == 0) break;
   }
   return null;
}
function getCookieVal(offset) {
   var endstr = document.cookie.indexOf (";", offset);
   if (endstr == -1) endstr = document.cookie.length;
   return document.cookie.substring(offset, endstr);
}

function valideDispoCookie(chaineAffiche) {
   var nomDuCookie = 'ACD_COOKIE_DISPO';
   setCookie(nomDuCookie,'etat_dispo');
   if( getCookie(nomDuCookie) == null ){
      document.write(chaineAffiche);
   }
}

// gestion multi-valeur dans un cookie
function getValeurData(valCookie, nomElem) {
   nomElem = nomElem + "=";
   var debVal = valCookie.indexOf(nomElem);
   if( debVal >= 0 ){
     debVal = debVal + nomElem.length;
     var finVal = valCookie.indexOf("&",debVal);
     return valCookie.substring(debVal, finVal);
   } else {
     return "";
   }
}
// gestion multi-valeur dans un cookie
function modifierValeurData(valCookie, nomElem, nouvVal) {
   nomElem = nomElem + "=";
   var debVal = valCookie.indexOf(nomElem);
   var finVal = 0;
   if( debVal >= 0 ){
      finVal = valCookie.indexOf("&",debVal)+1;
   }
   return valCookie.substring(0,debVal)+nomElem+nouvVal+"&"+valCookie.substring(finVal);
}
// gestion multi-valeur dans un cookie
function enleverValeurData(valCookie, nomElem) {
   nomElem = nomElem + "=";
   var debVal = valCookie.indexOf(nomElem);
   var finVal = 0;
   if( debVal >= 0 ){
      finVal = valCookie.indexOf("&",debVal)+1;
   }
   return valCookie.substring(0,debVal)+valCookie.substring(finVal);
}