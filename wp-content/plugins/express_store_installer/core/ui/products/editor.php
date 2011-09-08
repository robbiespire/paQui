<div class="wrap ecart">
	<?php if (!empty($Ecart->Notice)): ?><div id="message" class="updated fade"><p><?php echo $Ecart->Notice; ?></p></div><?php endif; ?>

	<div class="icon32"></div>
	<h2><?php _e('Add Or Edit Products','Ecart'); ?></h2>

	<div id="ajax-response"></div>
	<form name="product" id="product" action="<?php echo admin_url('admin.php'); ?>" method="post">
		<?php wp_nonce_field('ecart-save-product'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>

		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div id="side-info-column" class="inner-sidebar">
			<?php
			do_action('submitpage_box');
			$side_meta_boxes = do_meta_boxes('ecart_page_ecart-products', 'side', $Product);
			?>
			</div>

			<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : 'has-sidebar'; ?>">
			<div id="post-body-content" class="has-sidebar-content">

				<div id="titlediv">
					<div id="titlewrap">
						<label class="hide-if-no-js hidden" id="title-prompt-text" for="title"><?php _e('Enter product name','Ecart'); ?></label>
						<input name="name" id="title" type="text" value="<?php echo esc_attr($Product->name); ?>" size="30" tabindex="1" autocomplete="off" />
					</div>
					<div class="inside">
						<?php if (ECART_PRETTYURLS && !empty($Product->id)): ?>
							<div id="edit-slug-box"><strong><?php _e('Permalink','Ecart'); ?>:</strong>
							<span id="sample-permalink"><?php echo $permalink; ?><span id="editable-slug" title=<?php _jse('Click to edit this part of the permalink','Ecart'); ?>><?php echo esc_attr($Product->slug); ?></span><span id="editable-slug-full"><?php echo esc_attr($Product->slug); ?></span><?php echo user_trailingslashit(""); ?></span>
							<span id="edit-slug-buttons"><button type="button" class="edit-slug button"><?php _e('Edit','Ecart'); ?></button><?php if ($Product->status == "publish"): ?><button id="view-product" type="button" class="view button"><?php _e('View','Ecart'); ?></button><?php endif; ?></span>
							</div>
						<?php else: ?>
							<?php if (!empty($Product->id)): ?>
							<div id="edit-slug-box"><strong><?php _e('Product ID','Ecart'); ?>:</strong>
							<span id="editable-slug"><?php echo $Product->id; ?></span>
							</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
				<?php the_editor($Product->description,'content','Description', false); ?>
				</div>

			<?php
			do_meta_boxes('ecart_page_ecart-products', 'normal', $Product);
			do_meta_boxes('ecart_page_ecart-products', 'advanced', $Product);
			?>

			</div>
			</div>

		</div> <!-- #poststuff -->
	</form>
</div>

<div id="publish-calendar" class="calendar"></div>

<script type="text/javascript">
/* <![CDATA[ */
var flashuploader = <?php echo ($uploader == 'flash' && !(false !== strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac') && apache_mod_loaded('mod_security')))?'true':'false'; ?>,
	product = <?php echo (!empty($Product->id))?$Product->id:'false'; ?>,
	prices = <?php echo json_encode($Product->prices) ?>,
	specs = <?php echo json_encode($Product->specs) ?>,
	options = <?php echo json_encode($Product->options) ?>,
	priceTypes = <?php echo json_encode($priceTypes) ?>,
	shiprates = <?php echo json_encode($shiprates); ?>,
	buttonrsrc = '<?php echo includes_url('images/upload.png'); ?>',
	uidir = '<?php echo ECART_ADMIN_URI; ?>',
	siteurl = '<?php echo $Ecart->siteurl; ?>',
	canonurl = '<?php echo trailingslashit(ecarturl()); ?>',
	adminurl = '<?php echo $Ecart->wpadminurl; ?>',
	ajaxurl = adminurl+'admin-ajax.php',
	sugg_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_ecart_storage_suggestions"); ?>',
	spectemp_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_ecart_spec_template"); ?>',
	opttemp_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_ecart_options_template"); ?>',
	catmenu_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_ecart_category_menu"); ?>',
	addcategory_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_add_category"); ?>',
	editslug_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_edit_slug"); ?>',
	fileverify_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_verify_file"); ?>',
	fileimport_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_import_file"); ?>',
	fileimportp_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_import_file_progress"); ?>',
	imageul_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_upload_image"); ?>',
	adminpage = '<?php echo $this->Admin->pagename('products'); ?>',
	request = <?php echo json_encode(stripslashes_deep($_GET)); ?>,
	worklist = <?php echo json_encode($this->products(true)); ?>,
	filesizeLimit = <?php echo wp_max_upload_size(); ?>,
	weightUnit = '<?php echo $this->Settings->get('weight_unit'); ?>',
	dimensionUnit = '<?php echo $this->Settings->get('dimension_unit'); ?>',
	storage = '<?php echo $this->Settings->get('product_storage'); ?>',
	productspath = '<?php /* realpath needed for relative paths */ chdir(WP_CONTENT_DIR); echo addslashes(trailingslashit(sanitize_path(realpath($this->Settings->get('products_path'))))); ?>',
	imageupload_debug = <?php echo (defined('ECART_IMAGEUPLOAD_DEBUG') && ECART_IMAGEUPLOAD_DEBUG)?'true':'false'; ?>,
	fileupload_debug = <?php echo (defined('ECART_FILEUPLOAD_DEBUG') && ECART_FILEUPLOAD_DEBUG)?'true':'false'; ?>,
	dimensionsRequired = <?php echo $Ecart->Shipping->dimensions?'true':'false'; ?>,
	startWeekday = <?php echo get_option('start_of_week'); ?>,
	calendarTitle = '<?php $df = date_format_order(true); $format = $df["month"]." ".$df["year"]; echo $format; ?>',

	// Warning/Error Dialogs
	DELETE_IMAGE_WARNING = <?php _jse('Are you sure you want to delete this image?','Ecart'); ?>,
	SERVER_COMM_ERROR = <?php _jse('There was an error with the server.','Ecart'); ?>,

	// Dynamic interface label translations
	LINK_ALL_VARIATIONS = <?php _jse('Link All Variations','Ecart'); ?>,
	UNLINK_ALL_VARIATIONS = <?php _jse('Unlink All Variations','Ecart'); ?>,
	LINK_VARIATIONS = <?php _jse('Link Variations','Ecart'); ?>,
	UNLINK_VARIATIONS = <?php _jse('Unlink Variations','Ecart'); ?>,
	ADD_IMAGE_BUTTON_TEXT = <?php _jse('Upload New Image','Ecart'); ?>,
	UPLOAD_FILE_BUTTON_TEXT = <?php _jse('Upload&nbsp;File','Ecart'); ?>,
	SELECT_FILE_BUTTON_TEXT = <?php _jse('Upload A File For This Product','Ecart'); ?>,
	SAVE_BUTTON_TEXT = <?php _jse('Save','Ecart'); ?>,
	CANCEL_BUTTON_TEXT = <?php _jse('Cancel','Ecart'); ?>,
	TYPE_LABEL = <?php _jse('Type','Ecart'); ?>,
	PRICE_LABEL = <?php _jse('Default Product Price','Ecart'); ?>,
	AMOUNT_LABEL = <?php _jse('Amount','Ecart'); ?>,
	SALE_PRICE_LABEL = <?php _jse('Put It On Sale','Ecart'); ?>,
	NOT_ON_SALE_TEXT = <?php _jse('Check this option to put the product on sale','Ecart'); ?>,
	NOTAX_LABEL = <?php _jse('Don\'t tax this product','Ecart'); ?>,
	SHIPPING_LABEL = <?php _jse('Product Shipping Settings','Ecart'); ?>,
	FREE_SHIPPING_TEXT = <?php _jse('Free Shipping','Ecart'); ?>,
	WEIGHT_LABEL = <?php _jse('Weight','Ecart'); ?>,
	LENGTH_LABEL = <?php _jse('Length','Ecart'); ?>,
	WIDTH_LABEL = <?php _jse('Width','Ecart'); ?>,
	HEIGHT_LABEL = <?php _jse('Height','Ecart'); ?>,
	DIMENSIONAL_WEIGHT_LABEL = <?php _jse('3D Weight','Ecart'); ?>,
	SHIPFEE_LABEL = <?php _jse('Additional Shipping Fee','Ecart'); ?>,
	SHIPFEE_XTRA = <?php _jse('Amount added to shipping costs for each unit ordered (for handling costs, etc)','Ecart'); ?>,
	INVENTORY_LABEL = <?php _jse('Inventory','Ecart'); ?>,
	NOT_TRACKED_TEXT = <?php _jse('Add the product to the stock inventory manager','Ecart'); ?>,
	IN_STOCK_LABEL = <?php _jse('In Stock','Ecart'); ?>,
	OPTION_MENU_DEFAULT = <?php _jse('New Variation Group Created','Ecart'); ?>,
	NEW_OPTION_DEFAULT = <?php _jse('New Pricing Option Created','Ecart'); ?>,
	ADDON_GROUP_DEFAULT = <?php _jse('Add-on Group','Ecart'); ?>,
	SKU_LABEL = <?php _jse('SKU','Ecart'); ?>,
	SKU_LABEL_HELP = <?php _jse('Stock Keeping Unit','Ecart'); ?>,
	SKU_XTRA = <?php _jse('Enter a unique stock keeping unit identification code.','Ecart'); ?>,
	DONATIONS_VAR_LABEL = <?php _jse('Accept variable amounts','Ecart'); ?>,
	DONATIONS_MIN_LABEL = <?php _jse('Amount required as minimum','Ecart'); ?>,
	PRODUCT_DOWNLOAD_LABEL = <?php _jse('Digital Product Download','Ecart'); ?>,
	NO_PRODUCT_DOWNLOAD_TEXT = <?php _jse('No product added.','Ecart'); ?>,
	NO_DOWNLOAD = <?php _jse('Please upload a file using the button here (recommended .zip or .rar format).','Ecart'); ?>,
	UNKNOWN_UPLOAD_ERROR = <?php _jse('An unknown error occurred. The upload could not be saved.','Ecart'); ?>,
	DEFAULT_PRICELINE_LABEL = <?php _jse('Product Type : ','Ecart'); ?>,
	FILE_NOT_FOUND_TEXT = <?php _jse('The file you specified could not be found.','Ecart'); ?>,
	FILE_NOT_READ_TEXT = <?php _jse('The file you specified is not readable and cannot be used.','Ecart'); ?>,
	FILE_ISDIR_TEXT = <?php _jse('The file you specified is a directory and cannot be used.','Ecart'); ?>,
	FILE_UNKNOWN_IMPORT_ERROR = <?php _jse('An unknown error occured while attempting to attach the file.','Ecart'); ?>,
	IMAGE_DETAILS_TEXT = <?php _jse('Image Details','Ecart'); ?>,
	IMAGE_DETAILS_TITLE_LABEL = <?php _jse('Title','Ecart'); ?>,
	IMAGE_DETAILS_ALT_LABEL = <?php _jse('Alt','Ecart'); ?>,
	IMAGE_DETAILS_DONE = <?php _jse('OK','Ecart'); ?>,
	IMAGE_DETAILS_CROP_LABEL = <?php _jse('Cropped images','Ecart'); ?>;
/* ]]> */
</script>