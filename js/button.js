function tinyplugin() {
	return "[twitterlockerplugin]";
}

(function() {
    tinymce.create('tinymce.plugins.twitterlockerplugin', {
        init : function(ed, url){
            ed.addButton('twitterlockerplugin', {
                title : 'Wrap content in Twitter Conent Locker container',
                onclick : function() {
					ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
					tinyMCE.activeEditor.selection.setContent('[twitterlocker]' + ilc_sel_content + '[/twitterlocker]')
                },
                image: url + "/../images/locker.png"
            });
        },
        getInfo : function() {
            return {
                longname : 'Facebook Like Locker',
                author : 'Ivan Churakov',
                authorurl : 'http://www.icprojects.net/about/',
                infourl : 'http://www.icprojects.net/twitter-content-locker.html',
                version : '1.28'
            };
        }
    });
    tinymce.PluginManager.add('twitterlockerplugin', tinymce.plugins.twitterlockerplugin);
    
})();