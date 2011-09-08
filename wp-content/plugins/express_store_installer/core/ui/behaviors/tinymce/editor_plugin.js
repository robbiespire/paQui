/*
 * editor_plugin.js - Ecart TinyMCE Plugin
 
(function(){tinymce.create("tinymce.plugins.Ecart",{init:function(a,b){a.addCommand("mceEcart",function(){a.windowManager.open({file:b+"/dialog.php",width:320,height:200,inline:1},{plugin_url:b})});a.addButton("Ecart",{title:a.getLang("Ecart.desc"),cmd:"mceEcart",image:b+"/ecart.png"})}});tinymce.PluginManager.add("Ecart",tinymce.plugins.Ecart)})();if(typeof(tinyMCE)!="undefined"&&typeof(EcartDialog)!="undefined"){tinyMCE.addI18n(tinyMCEPreInit.mceInit.language+".Ecart",EcartDialog)};*/