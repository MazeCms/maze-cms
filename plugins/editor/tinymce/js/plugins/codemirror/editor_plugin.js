(function() {
tinymce.PluginManager.requireLangPack('codemirror');

tinymce.create('tinymce.plugins.codemirror',{
	 
	init : function(editor, url) {
	  
		  editor.addCommand('mceCodemirror',function(){		  
			  
			  var win = editor.windowManager.open({
				  title: editor.getLang("codemirror.title"),
				  url: url + '/source_editor.html',
				  width: 800,
				  height: 550,
				  inline : 1,
				  resizable : true,
				  maximizable : true
			  },
			   {
					plugin_url : url // Plugin absolute URL
				});
		  });
	 	
		editor.addButton('code', {
				title : editor.getLang("codemirror.code"),
				cmd : 'mceCodemirror',
				icon: 'code'
		});
		 
	}
	
});
	// Register plugin
	tinymce.PluginManager.add('codemirror', tinymce.plugins.codemirror);
})();
