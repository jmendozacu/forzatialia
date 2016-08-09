/**
 */
var Pos_Eta = Class.create();
Pos_Eta.prototype = {
    initialize: function(addressUrl, productId){
        this.addressUrl = addressUrl;
        this._ajaxSuccess = this.ajaxSuccess.bindAsEventListener(this);
        this._ajaxFailure = this.ajaxFailure.bindAsEventListener(this);
        this.productId = productId;
        this.qtyObserver = false;
    },

    qtyUpdated: function(event) {
    	this.etaButtonShow();
    },

    etaButtonShow: function() {
       	Element.hide('eta-result');
       	Element.show('eta-check-availability');
    	Element.hide('disclaimer-message');
    },

    getEta: function(productId){
    	if (!this.qtyObserver) {
//            Event.observe('qty', 'keyup', this.qtyUpdated.bind(this));
    	}
    	this.setLoadWaiting();
    	var qty = $('qty').value;
        request = new Ajax.Request(
            this.addressUrl + '?productId='+this.productId+'&qty='+qty,
            {method:'get', onSuccess: this._ajaxSuccess, onFailure: this._ajaxFailure}
        );
    },

    ajaxFailure: function(){
    	this.clearLoadWaiting();
    	$('eta-result').update('Sorry, service is unavailable. Please try again later');
    	Element.show('eta-result');
    	Element.hide('eta-check-availability');
    	Element.hide('disclaimer-message');
    },

    ajaxSuccess: function(transport){
    	this.clearLoadWaiting();
        if (transport && transport.responseText){
        	var result = transport.responseText.evalJSON();
        	$('eta-result').update(result['eta']);
        	if (result['success'] == true) {
            	Element.show('disclaimer-message');
        	}
        } else {
        	$('eta-result').update('Sorry, service is unavailable. Please try again later');
        }
    	Element.show('eta-result');
    	Element.hide('eta-check-availability');
    },

    setLoadWaiting: function() {
        Element.show('eta-please-wait');
    },

    clearLoadWaiting: function() {
        Element.hide('eta-please-wait');
    },

}
