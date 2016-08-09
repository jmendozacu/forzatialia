var jQueryScriptOutputted = false;
function initJQuery() {
    varjQueryFileName = "jquery.min.js";
	
    //if the jQuery object isn't available
    if (typeof(jQuery) == 'undefined') {
    
        if (! jQueryScriptOutputted) {
            //only output the script once..
            jQueryScriptOutputted = true;
            
            //output the script (load it from google api)
			var scripts= document.getElementsByTagName('script');
			var scriptPath= scripts[scripts.length-1].src.split('?')[0].split('/').slice(0, -1).join('/')+'/';      // remove any ?query 
																											  //+ remove last filename part of path			
            document.write("<scr" + "ipt type=\"text/javascript\" src=\""+scriptPath+varjQueryFileName+"\"></scr" + "ipt>");
        }
        setTimeout("initJQuery()", 50);
    }        
}
initJQuery();