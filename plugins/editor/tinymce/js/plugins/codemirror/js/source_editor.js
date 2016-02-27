tinyMCEPopup.requireLangPack();

// Global vars:
var tinymce,     // Reference to TinyMCE
	editor,      // Reference to TinyMCE editor
	codemirror,  // CodeMirror instance
	chr = 0,     // Unused utf-8 character, placeholder for cursor
	CMsettings;  // CodeMirror settings

(function()
{// Initialise (before load)
	"use strict";

	tinymce = parent.tinymce;
	editor = tinymce.activeEditor;

	var i, userSettings = editor.settings.codemirror ? editor.settings.codemirror : null;
	CMsettings = {
		path: userSettings.path || 'CodeMirror',
		indentOnInit: userSettings.indentOnInit || false,
		config: {// Default config
			mode: 'htmlmixed',
			lineNumbers: true,
			lineWrapping: true,
			indentUnit: 1,
			tabSize: 1,
			matchBrackets: true,
			styleActiveLine: true
		},
		jsFiles: [// Default JS files
			'lib/codemirror.js',
			'addon/edit/matchbrackets.js',
			'mode/xml/xml.js',
			'mode/javascript/javascript.js',
			'mode/css/css.js',
			'mode/htmlmixed/htmlmixed.js',
			'addon/dialog/dialog.js',
			'addon/search/searchcursor.js',
			'addon/search/search.js',
			'addon/selection/active-line.js'
		],
		cssFiles: [// Default CSS files
			'lib/codemirror.css',
			'addon/dialog/dialog.css'
		]
	};

	// Merge config
	for (i in userSettings.config) {
		CMsettings.config[i] = userSettings.config[i];
	}
	
	// Merge jsFiles
	for (i in userSettings.jsFiles) {
		if (!inArray(userSettings.jsFiles[i], CMsettings.jsFiles)) {
			CMsettings.jsFiles.push(userSettings.jsFiles[i]);
		}
	}

	// Merge cssFiles
	for (i in userSettings.cssFiles) {
		if (!inArray(userSettings.cssFiles[i], CMsettings.cssFiles)) {
			CMsettings.cssFiles.push(userSettings.cssFiles[i]);
		}
	}

	// Add trailing slash to path
	if (!/\/$/.test(CMsettings.path)) {
		CMsettings.path += '/';
	}

	// Write stylesheets
	for (i = 0; i < CMsettings.cssFiles.length; i++) {
		document.write('<li'+'nk rel="stylesheet" type="text/css" href="' + CMsettings.path + CMsettings.cssFiles[i] + '" />');
	}

	// Write JS source files
	for (i = 0; i < CMsettings.jsFiles.length; i++) {
		document.write('<scr'+'ipt type="text/javascript" src="' + CMsettings.path + CMsettings.jsFiles[i] + '"></scr'+'ipt>');
	}

}());

tinyMCEPopup.onInit.add(onLoadInit);

function saveContent() {
	tinyMCEPopup.editor.setContent(codemirror.getValue());
	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinymce.isGecko)
		document.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");

	document.getElementById('htmlSource').value = tinyMCEPopup.editor.getContent({source_view : true});

	
	start();
	resizeInputs();
}

function setWrap(val) {
	var v, n, s = document.getElementById('htmlSource');

	s.wrap = val;

	if (!tinymce.isIE) {
		v = s.value;
		n = s.cloneNode(false);
		n.setAttribute("wrap", val);
		s.parentNode.replaceChild(n, s);
		n.value = v;
	}
}

function setWhiteSpaceCss(value) {
	var el = document.getElementById('htmlSource');
	tinymce.DOM.setStyle(el, 'white-space', value);
}

function turnWrapOff() {
	if (tinymce.isWebKit) {
		setWhiteSpaceCss('pre');
	} else {
		setWrap('off');
	}
}

function turnWrapOn() {
	if (tinymce.isWebKit) {
		setWhiteSpaceCss('pre-wrap');
	} else {
		setWrap('soft');
	}
}

function resizeInputs() {
	var vp = tinyMCEPopup.dom.getViewPort(window), el;

	el = document.querySelector('.CodeMirror');

	if (el) {
		el.style.height = (vp.h - 65) + 'px';
	}
}



function inArray(key, arr)
{
	"use strict";
	arr = '|' + arr.join('|') + '|';
	return arr.indexOf('|'+key+'|') != -1;
}
function start()
{
	"use strict";

	if (typeof(window.CodeMirror) !== 'function') {
		alert('CodeMirror not found in "' + CMsettings.path + '", aborting...');
		return;
	}
	CodeMirror.defineInitHook(function(inst) 
	{
		// Move cursor to correct position:
		inst.focus();
		var cursor = inst.getSearchCursor(String.fromCharCode(chr), false);
		if (cursor.findNext()) {
			inst.setCursor(cursor.to());
			cursor.replace('');
		}
		
		// Indent all code, if so requested:
		if (editor.settings.codemirror.indentOnInit) {
			var last = inst.lineCount();
			inst.operation(function() {
				for (var i = 0; i < last; ++i) {
					inst.indentLine(i);
				}
			});
		}
	});
	// Instantiante CodeMirror:
	codemirror = CodeMirror.fromTextArea(document.getElementById('htmlSource'), CMsettings.config);
	codemirror.isDirty = false;
	/*codemirror.foldCode(CodeMirror.Pos(0, 0));
 	codemirror.foldCode(CodeMirror.Pos(21, 0));*/
	codemirror.on('change', function(inst) {
		inst.isDirty = true;
	});
}




function findDepth(haystack, needle)
{
	"use strict";

	var idx = haystack.indexOf(needle), depth = 0, x;
	for (x = idx; x >= 0; x--) {
		switch(haystack.charAt(x)) {
			case '<': depth--; break;
			case '>': depth++; break;
		}
	}
	return depth;
}
