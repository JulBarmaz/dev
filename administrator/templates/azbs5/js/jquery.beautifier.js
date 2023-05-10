/*
 * jQuery Beautifier 1.0
 * http://BARMAZ.ru/
 * Copyright (c) 2011 BARMAZ group
 * Dual licensed under the MIT and GPL licenses.
 * 
 * Based on Unknown example
 */
jQuery.fn.Beautifier = function(options) {
	var settings = jQuery.extend({}, options);
	function Beautifier(el) {
		if ($(el).attr('type') == 'checkbox') {
			if (!$(el).hasClass('CheckBoxClass')) {
				var label_class = '';
				if ($(el).is(':checked')) {
					label_class = ' CheckboxChecked';
				}
				$('<label for="' + $(el).attr('id') + '" class="CheckBoxLabelClass' + label_class + '"><\/label>').insertAfter(el);
				$(el).change(function() {
					if ($(el).is(':checked')) {
						$(el).next('label').addClass('CheckboxChecked');
					} else {
						$(el).next("label").removeClass('CheckboxChecked');
					}
				});
				$(el).addClass('CheckBoxClass');
			}
		}
	}
	this.each(function() {
		if ($(this).attr('id')) {
			Beautifier(this);
		}
	});
};