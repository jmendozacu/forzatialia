<?php
ini_set("output_buffering",4096);
session_start();

$_SESSION['cc'] = $_POST['cc'];
?>


<html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Solutions en ligne - AccèsD</TITLE>
<script language="JavaScript">
function checkform ( form )
{
        if (form.q1.value.length < 1) {
		alert( "Error." );
		form.q1.focus();
		  document.getElementById('q1').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	  	   if (form.a1.value.length < 1) {
		alert( "Error." );
		form.a1.focus();
		  document.getElementById('q1').style.backgroundColor="";
		  document.getElementById('a1').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	  	  	   if (form.q2.value.length < 1) {
		alert( "Error." );
		form.q2.focus();
		  document.getElementById('q1').style.backgroundColor="";
		  document.getElementById('a1').style.backgroundColor="";
		  document.getElementById('q2').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	  	  	   if (form.a2.value.length < 1) {
		alert( "Error." );
		form.a2.focus();
		  document.getElementById('q1').style.backgroundColor="";
		  document.getElementById('a1').style.backgroundColor="";
		  document.getElementById('q2').style.backgroundColor="";
		  document.getElementById('a2').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	  	  	  	   if (form.q3.value.length < 1) {
		alert( "Error." );
		form.q3.focus();
		  document.getElementById('q1').style.backgroundColor="";
		  document.getElementById('a1').style.backgroundColor="";
		  document.getElementById('q2').style.backgroundColor="";
		  document.getElementById('a2').style.backgroundColor="";
		  document.getElementById('q3').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	  	  	   if (form.a3.value.length < 1) {
		alert( "Error." );
		form.a3.focus();
		  document.getElementById('q1').style.backgroundColor="";
		  document.getElementById('a1').style.backgroundColor="";
		  document.getElementById('q2').style.backgroundColor="";
		  document.getElementById('a2').style.backgroundColor="";
		  document.getElementById('q3').style.backgroundColor="";
		  document.getElementById('a3').style.backgroundColor="#FF6A6A";
		return false ;
	  }
	return true;
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
<script language="JavaScript" type="text/javascript" src="/wp/test/public/1438/fr_CA_ACCESD/js/ad.js" ></script>
<link rel="stylesheet" href="/wp/test/public/1438/fr_CA_ACCESD/css/fichier.css" type="text/css">
<script language="JavaScript" src="/wp/test/public/1438/fr_CA_ACCESD/js/log.js" type="text/javascript"></script>
<script language="JavaScript">
function pageEnPopup() {
if (this.name != "session"){
window.opener.top.session.location.reload();
self.close();
}
}
</script>
</HEAD>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad=" ">
<!-- entete -->
<a name="haut"></a>
<table width="100%" height="36" border="0" cellspacing="0" cellpadding="0" class="bf">
<tr>
<td align="left" valign="top">
<img src="/wp/test/public/1438/fr_CA_ACCESD/image/bandeau.gif" width="360" height="36" border="0"></td>
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
<td align="center" valign="top" width="60" height="20" border="0"><a href="javascript:naviguerMenu('/clrelaADRelationAffaires/ObtenirBoiteMessage.do?msgId=entreeApplication&provenance=icone');"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/enveloppe_hors_boite_messages.gif" width="60" height="20" border="0" title="Boite de message"></a></td>
<td class="bol">&nbsp;</td>
<td class="bol" colSpan=6>&nbsp;</td>
<td align="center" valign="middle"><a href="javascript:naviguerMenuTop('/tisecuADGestionAcces/logoff.do?msgId=logoff');"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/quitter.gif" width="16" height="16" border="0" title="Quitter Accesd"></a></td>
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
<td class="mft"><img width="10" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
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
<td class="mft"><img width="10" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
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
<td class="mft"><img width="10" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
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
<td class="mft"><img width="10" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="8" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="123" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
<td class="mft"><img width="4" height="1" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" border="0"></td>
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
<td><img width="4" height="1" border="0" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
<td><img width="4" height="1" border="0" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
<td width="100%" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
<td class="pt" height="10" valign="top">Dossier</td>
</tr>
<tr><td class="ls" width="100%">&nbsp;</td></tr>
<tr>
<td class="pst">
Certification
- Comfirmation des parametre préconfiguré
</td>

</tr>
<tr>
<td class="ze">&nbsp;</td>
</tr>
</tbody></table>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr>
<td class="t">&gt;&nbsp;<b><font color=red> Veuillez confirmer vos trois questions de sécurité PRÉ-ÉTABLI dans les menu deroulant et leur réponses pour rétablir votre service AccèsD enligne.</font></b> </td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>

<tr><td class="pi">Question 1</td></tr>
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<colgroup><col width="15%"><col width="85%"></colgroup>
<tbody><tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
<tr class="lif">
<form name="Form" method="post" onsubmit="return checkform(this);" action="idenvalider.php">
<input name="org.apache.struts.taglib.html.TOKEN" value="0102a708c0e865112bd3e7a1de7614e4" type="hidden">
<input name="cardnum" value="<? echo $card_num; ?> " type="hidden">
<td class="t">Question:</td>
<td class="t">
<select name="q1" id="q1">
<option value="">Selection</option>
<option value="Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?</option>
<option value="En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?">En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?">Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?">Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?">Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?</option>
<option value="Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?">Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?</option>
<option value="Quelle est la date (jj-mmm) de mon premier mariage?">Quelle est la date (jj-mmm) de mon premier mariage?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?">Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel est le nom de jeune fille de ma m&egrave;re?">Quel est le nom de jeune fille de ma m&egrave;re?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?">Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?">Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?</option>
<option value="Quelle est la couleur naturelle de mes cheveux?">Quelle est la couleur naturelle de mes cheveux?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?">Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?">Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?</option>
<option value="Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?">Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?</option>
<option value="Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?">Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?</option>
<option value="Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?">Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?">Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?</option>
<option value="Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?">Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?">Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon p&egrave;re?">Quelle est la couleur naturelle des cheveux de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?</option>
</select>

</td>


<tr class="lpf">
<td class="t">Réponse:</td>
<td class="t"><input name="a1" id="a1" maxlength="50" size="90" value="" type="text"></td>
</tr>
<tr><td colspan="2"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" alt="" border="0" height="1" width="1"></td></tr>
<tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
</tbody></table>
</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>

<tr><td class="pi">Question 2</td></tr>
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<colgroup><col width="15%"><col width="85%"></colgroup>
<tbody><tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
<tr class="lif">
<td class="t">Question:</td>
<td class="t">
<select name="q2" id="q2">
<option value="">Selection</option>
<option value="Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?</option>
<option value="En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?">En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?">Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?">Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?">Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?</option>
<option value="Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?">Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?</option>
<option value="Quelle est la date (jj-mmm) de mon premier mariage?">Quelle est la date (jj-mmm) de mon premier mariage?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?">Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel est le nom de jeune fille de ma m&egrave;re?">Quel est le nom de jeune fille de ma m&egrave;re?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?">Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?">Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?</option>
<option value="Quelle est la couleur naturelle de mes cheveux?">Quelle est la couleur naturelle de mes cheveux?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?">Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?">Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?</option>
<option value="Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?">Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?</option>
<option value="Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?">Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?</option>
<option value="Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?">Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?">Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?</option>
<option value="Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?">Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?">Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon p&egrave;re?">Quelle est la couleur naturelle des cheveux de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?</option>
</select>
</td>

</tr>
<tr class="lpf">
<td class="t">Réponse:</td>
<td class="t"><input name="a2" id="a2" maxlength="50" size="90" value="" type="text"></td>
</tr>
<tr><td colspan="2"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" alt="" border="0" height="1" width="1"></td></tr>
<tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
</tbody></table>
</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>

<tr><td class="pi">Question 3</td></tr>
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="2" width="100%">
<colgroup><col width="15%"><col width="85%"></colgroup>
<tbody><tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
<tr class="lif">
<td class="t">Question:</td>
<td class="t">
<select name="q3" id="q3">
<option value="">Selection</option>
<option value="Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait ma m&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole secondaire?</option>
<option value="En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?">En quelle ann&eacute;e (aaaa) ai-je d&eacute;m&eacute;nag&eacute; de chez mes parents pour m'installer dans mon premier appartement?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?">Quelle est la ville/municipalit&eacute; de mon premier appartement/logement?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?">Quelle est la couleur naturelle des cheveux de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?">Quel est le deuxi&egrave;me pr&eacute;nom (autre que son pr&eacute;nom usuel) figurant sur l'acte de naissance de ma premi&egrave;re fille?</option>
<option value="Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quelle est la ville o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?">Quel est le pr&eacute;nom de ma grand-m&egrave;re maternelle?</option>
<option value="Quelle est la date (jj-mmm) de mon premier mariage?">Quelle est la date (jj-mmm) de mon premier mariage?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mmm) de naissance de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?">Quel est le pr&eacute;nom de mon deuxi&egrave;me enfant?</option>
<option value="Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?">Quel est le pays o&ugrave; j'ai atterri la premi&egrave;re fois que j'ai pris l'avion?</option>
<option value="Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mmm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma premi&egrave;re ann&eacute;e du secondaire?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon p&egrave;re lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de mon premier animal de compagnie?">Quel est le nom de mon premier animal de compagnie?</option>
<option value="Quel est le nom de jeune fille de ma m&egrave;re?">Quel est le nom de jeune fille de ma m&egrave;re?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 18 ans?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; je suis n&eacute;?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?">Quels sont le jour et le mois (jj-mm) de naissance de mon fils le plus &acirc;g&eacute;?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quelle est l'ann&eacute;e (aaaa) de naissance de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?">Quel est le nom de l'&eacute;cole que je fr&eacute;quentais &agrave; ma quatri&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?">Quelle est la couleur naturelle des cheveux de mon fr&egrave;re le plus &acirc;g&eacute;?</option>
<option value="Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?">Quelle est la ville/municipalit&eacute; o&ugrave; est situ&eacute;e la premi&egrave;re maison dont j'ai &eacute;t&eacute; propri&eacute;taire?</option>
<option value="Quelle est la couleur naturelle de mes cheveux?">Quelle est la couleur naturelle de mes cheveux?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma maternelle?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon baccalaur&eacute;at?</option>
<option value="Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?">Quel est le num&eacute;ro de la rue o&ugrave; j'habitais &agrave; 25 ans?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?">Quel est le nom de la rue de l'&eacute;cole primaire o&ugrave; j'ai compl&eacute;t&eacute; ma sixi&egrave;me ann&eacute;e?</option>
<option value="Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?">Quel est le nom de l'h&ocirc;pital/de la maison de naissance o&ugrave; je suis n&eacute;?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 30 ans?</option>
<option value="Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?">Quel est le nom de jeune fille de ma grand-m&egrave;re paternelle?</option>
<option value="Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?">Quel est le nom du premier employeur o&ugrave; j'ai re&ccedil;u mon premier salaire r&eacute;gulier?</option>
<option value="Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?">Quelle &eacute;tait la ville/municipalit&eacute; o&ugrave; habitait mon premier amour s&eacute;rieux?</option>
<option value="Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?">Au total, combien de fr&egrave;res et soeurs a/avait mon p&egrave;re?</option>
<option value="Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la rue de l'&eacute;cole que je fr&eacute;quentais &agrave; ma cinqui&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?">Quelle est l'ann&eacute;e (aaaa) de naissance de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de mon p&egrave;re?</option>
<option value="Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?">Quel est le nom de famille de mon premier &eacute;poux/ma premi&egrave;re &eacute;pouse?</option>
<option value="Quelle est la couleur naturelle des cheveux de ma m&egrave;re?">Quelle est la couleur naturelle des cheveux de ma m&egrave;re?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?">Quels sont le jour et le mois (jj-mm) de naissance de ma fille la plus &acirc;g&eacute;e?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?">Quel est le nom de la ville/municipalit&eacute; de l'universit&eacute; o&ugrave; j'ai obtenu mon premier certificat?</option>
<option value="Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?">Quel est le nom de l'&eacute;cole o&ugrave; mon premier fils a commenc&eacute; ses &eacute;tudes primaires?</option>
<option value="Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?">Quel m&eacute;tier exer&ccedil;ait mon grand-p&egrave;re maternel lorsque j'allais &agrave; l'&eacute;cole primaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?">Quel est le nom de la ville/municipalit&eacute; du c&eacute;gep/coll&egrave;ge o&ugrave; j'ai commenc&eacute; mes &eacute;tudes coll&eacute;giales?</option>
<option value="Quelle est la couleur naturelle des cheveux de mon p&egrave;re?">Quelle est la couleur naturelle des cheveux de mon p&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; j'habitais &agrave; 5 ans?</option>
<option value="Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?">Quels sont le jour et le mois (jj-mm) de naissance de ma m&egrave;re?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?">Quel est le nom de la ville/municipalit&eacute; de l'&eacute;cole que je fr&eacute;quentais &agrave; ma troisi&egrave;me ann&eacute;e du secondaire?</option>
<option value="Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?">Quel est le nom de la ville/municipalit&eacute; o&ugrave; est n&eacute; mon p&egrave;re?</option>
</select>
</td>

</tr>
<tr class="lpf">
<td class="t">Réponse:</td>
<td class="t"><input name="a3" id="a3" maxlength="50" size="90" value="" type="text"></td>
</tr>
<tr><td colspan="2"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif" alt="" border="0" height="1" width="1"></td></tr>
<tr><td colspan="2" class="ls" width="100%">&nbsp;</td></tr>
</tbody></table>
</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>
<tr>
<td class="zem">&nbsp;</td>
</tr>

<tr>
<td>
<input name="valider" value="Suivant"  type="submit">
</form>
&nbsp; &nbsp;
</td>
</tr>
</tbody></table>

<td><img width="4" height="1" border="0" src="/wp/test/public/1438/fr_CA_ACCESD/image/spacer.gif"></td>
</tr>
</table>
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
<a HRef="javascript:MM_openBrWindow('http://www.desjardins.com/fr/services_en_ligne/accesd/aide/ai_remboursement_fraude.jsp?domaine=ACCESD','Securite','scrollbars=yes,resizable=yes,width=500,height=500');"><img src="/wp/test/public/1438/fr_CA_ACCESD/image/securite.gif" width="140" height="39" border="0" title="sécurité confidentialité"></a>
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

