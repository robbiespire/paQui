(function() {
    tinymce.create('tinymce.plugins.product', {
        init : function(ed, url) {
            ed.addButton('product', {
                title : 'Add product',
                image : url+'/product-icon.png',
                onclick : function() {
					ed.windowManager.open({
						file : url + '/product-window.php',
						width : 370,
						height : 250,
						inline : 1
					});					 

                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('product', tinymce.plugins.product);
})();