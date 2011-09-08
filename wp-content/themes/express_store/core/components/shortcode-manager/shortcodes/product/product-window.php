<?php 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Insert Products From The Catalog</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php
    	//GET THE CURRENT WORDPRESS INSTALL LOCATION
        $url_with_file = $_SERVER['HTTP_REFERER'];
        $file_pos = strpos($url_with_file,"/wp-admin");
        $url = substr($url_with_file, 0,$file_pos);
    ?>  
    <script language="javascript" type="text/javascript" src="<?php echo $url; ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $url; ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $url; ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $url; ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo $url; ?>/wp-includes/js/jquery/jquery.js"></script>
    
	<?php require_once('../../../../../../../../wp-load.php'); ?>
        
    <script language="javascript" type="text/javascript">

    var _self = tinyMCEPopup;
    function init () {
        updateCategories();
        changeCategory();
    }

    function insertTag () {
        var tag = '';
        if (parseInt(jQuery('#category-menu').val()) > 0)
            tag = '[category id="'+jQuery('#category-menu').val()+'"]';
        else if (jQuery('#category-menu').val() != '') tag = '[category slug="catalog"]';

        var productid = jQuery('#product-menu').val();
        if (productid != 0) tag = '[product id="'+productid+'"]';

        if(window.tinyMCE) {
            window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tag);
            tinyMCEPopup.editor.execCommand('mceRepaint');
            tinyMCEPopup.close();
        }

    }

    function closePopup () {
        tinyMCEPopup.close();
    }

    function updateCategories () {
        var menu = jQuery('#category-menu');
        jQuery.get("<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_category_menu'); ?>&action=ecart_category_menu",{},function (results) {
            menu.empty().html(results);
        },'string');
    }

    function changeCategory () {
        var menu = jQuery('#category-menu');
        var products = jQuery('#product-menu');
        jQuery.get("<?php echo wp_nonce_url(admin_url('admin-ajax.php'),'wp_ajax_ecart_category_products'); ?>&action=ecart_category_products",{category:menu.val()},function (results) {
            products.empty().html(results);
        },'string');
    }

    </script>

    <style type="text/css">
        table th { vertical-align: top; }
        .panel_wrapper { border-top: 1px solid #909B9C; }
        .panel_wrapper div.current { height:auto !important; }
        #product-menu { width: 180px; }
    </style>

</head>
<body onload="init()">



<div id="wpwrap">
<form onsubmit="insertTag();return false;" action="#">
    <div class="panel_wrapper">
    	
    	<p><?php _e('Through the use of this panel you can add one or more product(s) from your catalog, anywhere in any post or page.','ecart');?></p>
    
        <table border="0" cellpadding="4" cellspacing="0">
        <tr>
        <th nowrap="nowrap"><label for="category-menu"><?php _e("Select Category", 'Ecart'); ?></label></th>
        <td><select id="category-menu" name="category" onchange="changeCategory()"></select></td>
        </tr>
        <tr id="product-selector">
        <th nowrap="nowrap"><label for="product-menu"><?php _e("Product(s)", 'Ecart'); ?></label></th>
        <td><select id="product-menu" name="product" size="7"></select></td>
        </tr>
        </table>
    </div>

    <div class="mceActionPanel">
        <div style="float: left">
            <input type="button" id="cancel" name="cancel" value="Close" onclick="closePopup()"/>
        </div>

        <div style="float: right">
            <input type="button" id="insert" name="insert" value="Insert" onclick="insertTag()" />
        </div>
    </div>
</form>
</div>



</body>
</html>