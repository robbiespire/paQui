<div class="wrap ecart">
	<?php if (!empty($this->Notice)): ?><div id="message" class="updated fade"><p><?php echo $this->Notice; ?></p></div><?php endif; ?>

	<div class="icon32"></div>
	<h2><?php _e('Add or Edit Category','Ecart'); ?></h2>

	<div id="ajax-response"></div>
	<form name="category" id="category" action="<?php echo admin_url('admin.php'); ?>" method="post">
		<?php wp_nonce_field('ecart-save-category'); ?>

		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div id="side-info-column" class="inner-sidebar">

			<?php
			do_action('submitpage_box');
			$side_meta_boxes = do_meta_boxes('ecart_page_ecart-products', 'side', $Category);
			?>
			</div>

			<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : 'has-sidebar'; ?>">
			<div id="post-body-content" class="has-sidebar-content">

				<div id="titlediv">
					<div id="titlewrap">
						<small><?php _e('Category Name','Ecart');?></small>
						<input name="name" id="title" type="text" value="<?php echo esc_attr($Category->name); ?>" size="30" tabindex="1" autocomplete="off" />
					</div>
					<div class="inside">
						<?php if (ECART_PRETTYURLS && !empty($Category->id)): ?>
						<div id="edit-slug-box"><strong><?php _e('Permalink','Ecart'); ?>:</strong>
						<span id="sample-permalink"><?php echo $permalink; ?><span id="editable-slug" title="<?php _e('Click to edit this part of the permalink','Ecart'); ?>"><?php echo esc_attr($Category->slug); ?></span><span id="editable-slug-full"><?php echo esc_attr($Category->slug); ?></span>/</span>
						<span id="edit-slug-buttons"><button type="button" class="edit-slug button">Edit</button></span>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
				<?php the_editor($Category->description,'content','Description', false); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				</div>

			<?php
			do_meta_boxes('ecart_page_ecart-products', 'normal', $Category);
			do_meta_boxes('ecart_page_ecart-products', 'advanced', $Category);
			?>

			</div>
			</div>

		</div> <!-- #poststuff -->
	</form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var flashuploader = <?php echo ($uploader == 'flash' && !(false !== strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac') && apache_mod_loaded('mod_security')))?'true':'false'; ?>,
	category = <?php echo (!empty($Category->id))?$Category->id:'false'; ?>,
	product = false,
	details = <?php echo json_encode($Category->specs) ?>,
	priceranges = <?php echo json_encode($Category->priceranges) ?>,
	options = <?php echo json_encode($Category->options) ?>,
	prices = <?php echo json_encode($Category->prices) ?>,
	uidir = '<?php echo ECART_ADMIN_URI; ?>',
	siteurl = '<?php echo $Ecart->siteurl; ?>',
	adminurl = '<?php echo $Ecart->wpadminurl; ?>',
	ajaxurl = adminurl+'admin-ajax.php',
	addcategory_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "ecart-ajax_add_category"); ?>',
	editslug_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "wp_ajax_ecart_edit_slug"); ?>',
	fileverify_url = '<?php echo wp_nonce_url($Ecart->wpadminurl."admin-ajax.php", "ecart-ajax_verify_file"); ?>',
	adminpage = '<?php echo $this->Admin->pagename('categories'); ?>',
	request = <?php echo json_encode(stripslashes_deep($_GET)); ?>,
	worklist = <?php echo json_encode($this->categories(true)); ?>,
	filesizeLimit = <?php echo wp_max_upload_size(); ?>,
	priceTypes = <?php echo json_encode($priceTypes) ?>,
	weightUnit = '<?php echo $this->Settings->get('weight_unit'); ?>',
	dimensionsRequired = <?php echo $Ecart->Shipping->dimensions?'true':'false'; ?>,
	storage = '<?php echo $this->Settings->get('product_storage'); ?>',
	productspath = '<?php /* realpath needed for relative paths */ chdir(WP_CONTENT_DIR); echo addslashes(trailingslashit(sanitize_path(realpath($this->Settings->get('products_path'))))); ?>',
	imageupload_debug = <?php echo (defined('ECART_IMAGEUPLOAD_DEBUG') && ECART_IMAGEUPLOAD_DEBUG)?'true':'false'; ?>,
	fileupload_debug = <?php echo (defined('ECART_FILEUPLOAD_DEBUG') && ECART_FILEUPLOAD_DEBUG)?'true':'false'; ?>,

	// Warning/Error Dialogs
	DELETE_IMAGE_WARNING = "<?php _e('Are you sure you want to delete this category image?','Ecart'); ?>",
	SERVER_COMM_ERROR = "<?php _e('There was an error communicating with the server.','Ecart'); ?>",

	// Translatable dynamic interface labels
	NEW_DETAIL_DEFAULT = "<?php _e('Detail Name','Ecart'); ?>",
	NEW_OPTION_DEFAULT = "<?php _e('New Option','Ecart'); ?>",
	FACETED_DISABLED = "<?php _e('Faceted menu disabled','Ecart'); ?>",
	FACETED_AUTO = "<?php _e('Build faceted menu automatically','Ecart'); ?>",
	FACETED_RANGES = "<?php _e('Build as custom number ranges','Ecart'); ?>",
	FACETED_CUSTOM = "<?php _e('Build from preset options','Ecart'); ?>",
	ADD_IMAGE_BUTTON_TEXT = "<?php _e('Add New Image','Ecart'); ?>",
	SAVE_BUTTON_TEXT = "<?php _e('Save','Ecart'); ?>",
	CANCEL_BUTTON_TEXT = "<?php _e('Cancel','Ecart'); ?>",
	OPTION_MENU_DEFAULT = "<?php _e('Option Menu','Ecart'); ?>",
	NEW_OPTION_DEFAULT = "<?php _e('New Option','Ecart'); ?>",

	UPLOAD_FILE_BUTTON_TEXT = "<?php _e('Upload&nbsp;File','Ecart'); ?>",
	TYPE_LABEL = "<?php _e('Type','Ecart'); ?>",
	PRICE_LABEL = "<?php _e('Price','Ecart'); ?>",
	AMOUNT_LABEL = "<?php _e('Amount','Ecart'); ?>",
	SALE_PRICE_LABEL = "<?php _e('Sale Price','Ecart'); ?>",
	NOT_ON_SALE_TEXT = "<?php _e('Not on Sale','Ecart'); ?>",
	NOTAX_LABEL = "<?php _e('Not Taxed','Ecart'); ?>",
	SHIPPING_LABEL = "<?php _e('Shipping','Ecart'); ?>",
	FREE_SHIPPING_TEXT = "<?php _e('Free Shipping','Ecart'); ?>",
	WEIGHT_LABEL = "<?php _e('Weight','Ecart'); ?>",
	SHIPFEE_LABEL = "<?php _e('Handling Fee','Ecart'); ?>",
	SHIPFEE_XTRA = "<?php _e('Amount added to shipping costs for each unit ordered (for handling costs, etc)','Ecart'); ?>",
	INVENTORY_LABEL = "<?php _e('Inventory','Ecart'); ?>",
	NOT_TRACKED_TEXT = "<?php _e('Not Tracked','Ecart'); ?>",
	IN_STOCK_LABEL = "<?php _e('In Stock','Ecart'); ?>",
	SKU_LABEL = "<?php _e('SKU','Ecart'); ?>",
	SKU_LABEL_HELP = "<?php _e('Stock Keeping Unit','Ecart'); ?>",
	SKU_XTRA = "<?php _e('Enter a unique stock keeping unit identification code.','Ecart'); ?>",
	DONATIONS_VAR_LABEL = "<?php _e('Accept variable amounts','Ecart'); ?>",
	DONATIONS_MIN_LABEL = "<?php _e('Amount required as minimum','Ecart'); ?>",
	PRODUCT_DOWNLOAD_LABEL = "<?php _e('Product Download','Ecart'); ?>",
	NO_PRODUCT_DOWNLOAD_TEXT = "<?php _e('No product download.','Ecart'); ?>",
	NO_DOWNLOAD = "<?php _e('No download file.','Ecart'); ?>",
	UNKNOWN_UPLOAD_ERROR = "<?php _e('An unknown error occurred. The upload could not be saved.','Ecart'); ?>",
	DEFAULT_PRICELINE_LABEL = "<?php _e('Price & Delivery','Ecart'); ?>",
	FILE_NOT_FOUND_TEXT = "<?php _e('The file you specified could not be found.','Ecart'); ?>",
	FILE_NOT_READ_TEXT = "<?php _e('The file you specified is not readable and cannot be used.','Ecart'); ?>",
	FILE_ISDIR_TEXT = "<?php _e('The file you specified is a directory and cannot be used.','Ecart'); ?>",
	IMAGE_DETAILS_TEXT = "<?php _e('Image Details','Ecart'); ?>",
	IMAGE_DETAILS_TITLE_LABEL = "<?php _e('Title','Ecart'); ?>",
	IMAGE_DETAILS_ALT_LABEL = "<?php _e('Alt','Ecart'); ?>",
	IMAGE_DETAILS_DONE = "<?php _e('OK','Ecart'); ?>",
	IMAGE_DETAILS_CROP_LABEL = "<?php _e('Cropped images','Ecart'); ?>";
/* ]]> */
</script>