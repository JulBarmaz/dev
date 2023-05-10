/*
Plugin Readmore
Copyright (c) 2009-2010
Radius17. All rights reserved.

Designed for CKEDITOR
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
 */
(function() {
	CKEDITOR.plugins.add('readmore',
		{
			init : function(editor) {
				editor.addCommand('addReadmore', CKEDITOR.plugins.addReadmoreCmd);
				editor.ui.addButton('Readmore', {
					label : "Add ReadMore tag",
					command : 'addReadmore',
					icon : this.path + 'images/readmore.gif'
				});
				
			}
		});
//	CKEDITOR.addCss('hr#system-readmore {border-color: #FF0000;	border-style:dotted none none none; }');
//	CKEDITOR.addCss('hr#system-readmore {border-color: #FF0000;	border-style:dotted none none none; }');
	CKEDITOR.addCss('hr#system-readmore {background: url("/redistribution/ckeditor4/plugins/readmore/images/readmore.png") repeat scroll 0 0 transparent; border: 0px none; height: 8px; margin: 0; padding: 0; }');
	
	CKEDITOR.plugins.addReadmoreCmd =
	{
		exec : function( editor )
		{
			myhtml=editor.getData();
			if (myhtml.match(/<hr\s+id=(\"|')system-readmore(\"|')\s*\/*>/i)) {
				alert('Readmore TAG alredy present!');
				return false;
			} 
			editor.fire( 'saveSnapshot' );
			element=editor.document.createElement( 'hr' );
			element.setAttribute('id','system-readmore');
			editor.insertElement( element );
			editor.fire( 'saveSnapshot' );
		}
	};

})();