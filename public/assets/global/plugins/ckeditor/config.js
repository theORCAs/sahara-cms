/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    //config.language = 'tr';
    config.toolbar = "SaharaSmall";
    
    config.toolbar_SaharaDefault =
    [
    	{ name: 'document', items : [ 'DocProps','Preview','Print','-','Templates' ] },
    	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
    	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
        { name: 'styles', items : [ 'Format','Font','FontSize' ] },
    	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
    	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
    	'/',
    	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
    	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote', 
        '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
    	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
    	{ name: 'insert', items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
    ];
    config.toolbar_SaharaSmall = [
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
		{ name: 'styles', items : [ 'Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor', 'NumberedList','BulletedList', 'Table' ] },
        { name: 'links', items : [ 'Link','Unlink'] },
	];
};
