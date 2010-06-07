/*
 * JUP - plugin for file uploads with JSON support
 *
 * @name: JUP 
 * @author: Khaled Jouda
 * @version 1.0.1
 * @date 04.08.2009
 * @category jQuery plugin 
 * @Copyright (c) 2009 Khaled Jouda (doroubna.com)
 *
 */
(function($){
	function JUP(form, settings) {
	
		var defaults = {
				url : null, /** if not set, form 'action' will be used  */ 
				validate:null, /** optional function to validate the form */
				beforeUpload:null, /** optional function to call before uploading starts*/
				onComplete:null, /** optional function to call after uploading is completed*/
				json:true /** wether to expect JSON response from upload script or not*/
		};
		
		var result = null;
		
		var settings = $.extend({}, defaults, settings);
		
		if( settings.url != null ){
			form.attr('action', settings.url);
		}
		
		form.attr("enctype", "multipart/form-data");
		
		
		/**
		 * @desc submits the form asynchronously
		 * 
		 */
		form.submit(function(e){
			/** Do we need to validate the form? */	
			if(  $.isFunction(settings.validate) ) {
			    var fields = getFields(form); /** get form fields */
			    if( !settings.validate(fields) ){
			        /** not valid */
				    e.preventDefault();
				    return false;
				}
			}
			
			/** is beforeUpload action set? */
			if( $.isFunction(settings.beforeUpload) ){
				settings.beforeUpload(fields);
			}
			
			/** Generate the iframe to use*/
			var t = new Date().getTime();
			var iframeId = iframeId = "JUPiFrame" + t;
			var iFrame = $('<iframe id="' + iframeId + '" name="' + iframeId + '" src="javascript:;" style="display:none" />').appendTo(document.body);
			
			form.attr("target", iframeId);
			
			form.find("input[type=submit]").attr("disabled",true);
			
			
			$("#"+iframeId).load(function(){
			    /** form submission is complete,, */
			    
				form.find("input[type=submit]").attr("disabled", false);
				
				var response = iFrame.contents().find("body").html();
				
				if( $.isFunction(settings.onComplete) ){
				
					if(settings.json){
						try{
							response = eval('(' + response + ')');
						}catch(e){
							response = false;
						}
					}
					
					settings.onComplete(response, form.attr('id'));
					
				}
				
				setTimeout(iFrame.remove, 100);/** why timeout? 
											to prevent firefox from showing 'loading' status forever 
											after iframe removal.. calling 'iframe.remove' inside 'iframe.load' causes this problem 
											*/
			});
			
		});
		/**
		 * @desc gets fields of the form
		 * @param form: jquery object of the form
		 * @return {array} all fields that have names with their values
		 */
		function getFields(form){
			var fields = {};
			form.find("input,select,textarea").each(function(){
				/** only fields that have names are returned */
				var input = $(this);
				var inputName = input.attr("name");
				
				if( inputName.length > 1 && inputName.substr(inputName.length-2,inputName.length) == "[]" ){
					/** input field is an array, example: <input type="checkbox" name="options[]" />  */
					inputName = inputName.substr(0,inputName.length-2);
					if( !fields.hasOwnProperty(inputName) ){
						fields[inputName] = []; 
					}
					fields[inputName].push(input.val());
				}
				else if( inputName.length > 0 ){
					fields[inputName] = input.val();
				}
			});
			return fields;
		};
	};
	
	$.fn.jup = function(settings){
		return this.each(function(){
			var jup = JUP($(this), settings);
		});
	};
})(jQuery);

