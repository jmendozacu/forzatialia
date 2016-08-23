/**
 */
var Pos_Eta_Configurable = Class.create();
Pos_Eta_Configurable.prototype = {
    initialize: function(addressUrl, config){
        this.config     = config;
        this.attribute = this.config.attribute;
        this.attributeConfig = this.config.attributeConfig;
        this.product     = config.config;

        this.productId = '';
        this.settings   = $$('.super-attribute-select');
        this.addressUrl = addressUrl;
        this._ajaxSuccess = this.ajaxSuccess.bindAsEventListener(this);
        this._ajaxFailure = this.ajaxFailure.bindAsEventListener(this);

        // Put events to check select reloads
        this.settings.each(function(element){
            Event.observe(element, 'change', this.updateEtaButton.bind(this))
        }.bind(this));
        this.qtyObserver = false;

    },

    qtyUpdated: function(event) {
    	this.etaButtonShow();
    },

    updateEtaButton: function(event){
    	this.etaButtonShow();
    	if (!this.qtyObserver) {
//            Event.observe('qty', 'keyup', this.qtyUpdated.bind(this));
    	}
        this.productId = '';
    	var productString = '';
    	$H(this.attributeConfig).each(function(pair){
    		attributeElement = $(pair.key);
            productString = productString + pair.value + '_' + attributeElement.value + '_';
    	})
    	productString = 'attr_'+productString;
    	if (this.product[productString]) {
            this.productId = this.product[productString];
    	}
        var element = Event.element(event);
    },

    getEta: function(productId){
    	this.setLoadWaiting();
    	var qty = $('qty').value;
        request = new Ajax.Request(
            this.addressUrl + '?productId='+this.productId+'&qty='+qty,
            {method:'post', onSuccess: this._ajaxSuccess, onFailure: this._ajaxFailure}
        );
    },

    etaButtonShow: function() {
       	Element.hide('eta-result');
       	Element.show('eta-check-availability');
    	Element.hide('disclaimer-message');
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
