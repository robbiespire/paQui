<?php
function save_meta_box ($Category) {
	global $Ecart;
	
	$workflows = array(
		"continue" => __('Continue Editing','Ecart'),
		"close" => __('Category Manager','Ecart'),
		"new" => __('New Category','Ecart'),
		"next" => __('Edit Next','Ecart'),
		"previous" => __('Edit Previous','Ecart')
		);
	
?>
	<div id="major-publishing-actions">
		<input type="hidden" name="id" value="<?php echo $Category->id; ?>" />
		<select name="settings[workflow]" id="workflow">
		<?php echo menuoptions($workflows,$Ecart->Settings->get('workflow'),true); ?>
		</select>
		<input type="submit" class="button-primary" name="save" value="<?php _e('Save Category','Ecart'); ?>" />
	</div>
<?php
}
add_meta_box('save-category', __('Save Category','Ecart').$Admin->boxhelp('category-editor-save'), 'save_meta_box', 'ecart_page_ecart-products', 'side', 'core');

function settings_meta_box ($Category) {
	global $Ecart;
	$categories_menu = $Ecart->Flow->Controller->menu($Category->parent,$Category->id);
	$categories_menu = '<option value="0" rel="-1,-1">'.__('Parent Category','Ecart').'&hellip;</option>'.$categories_menu;
?>
	<p><select name="parent" id="category_parent"><?php echo $categories_menu; ?></select><br /> 

	
	<div style="display:none !important;">
	<p><input type="hidden" name="spectemplate" value="off" /><input type="checkbox" name="spectemplate" value="on" id="spectemplates-setting" tabindex="11" <?php if ($Category->spectemplate == "on") echo ' checked="checked"'?> /><label for="spectemplates-setting"> <?php _e('Product Details Template','Ecart'); ?></label><br /><?php _e('Predefined details for products created in this category','Ecart'); ?></p>
	<p id="facetedmenus-setting"><input type="hidden" name="facetedmenus" value="off" /><input type="checkbox" name="facetedmenus" value="on" id="faceted-setting" tabindex="12" <?php if ($Category->facetedmenus == "on") echo ' checked="checked"'?> /><label for="faceted-setting"> <?php _e('Faceted Menus','Ecart'); ?></label><br /><?php _e('Build drill-down filter menus based on the details template of this category','Ecart'); ?></p>
	<p><input type="hidden" name="variations" value="off" /><input type="checkbox" name="variations" value="on" id="variations-setting" tabindex="13"<?php if ($Category->variations == "on") echo ' checked="checked"'?> /><label for="variations-setting"> <?php _e('Variations','Ecart'); ?></label><br /><?php _e('Predefined selectable product options for products created in this category','Ecart'); ?></p>
	<p><a href="<?php echo add_query_arg(array('page'=>'ecart-categories','id'=>$Category->id,'a'=>'products'),admin_url('admin.php')); ?>" class="button-secondary"><?php _e('Arrange Products','Ecart'); ?></a></p>
	</div>
	<?php
}
add_meta_box('category-settings', __('Category Settings','Ecart').$Admin->boxhelp('category-editor-settings'), 'settings_meta_box', 'ecart_page_ecart-products', 'side', 'core');

function images_meta_box ($Category) {
?>
	<ul id="lightbox">
		<?php foreach ($Category->images as $i => $Image): ?>
			<li id="image-<?php echo $Image->id; ?>"><input type="hidden" name="images[]" value="<?php echo $Image->id; ?>" />
			<div id="image-<?php echo $Image->id; ?>-details">
				<img src="?siid=<?php echo $Image->id; ?>&amp;<?php echo $Image->resizing(96,0,1); ?>" width="96" height="96" />
				<input type="hidden" name="imagedetails[<?php echo $i; ?>][id]" value="<?php echo $Image->id; ?>" />
				<input type="hidden" name="imagedetails[<?php echo $i; ?>][title]" value="<?php echo $Image->title; ?>" class="imagetitle" />
				<input type="hidden" name="imagedetails[<?php echo $i; ?>][alt]" value="<?php echo $Image->alt; ?>"  class="imagealt" />
				<?php 
					if (count($Image->cropped) > 0): 
						foreach ($Image->cropped as $cache): 
							$cropping = join(',',array($cache->settings['dx'],$cache->settings['dy'],$cache->settings['cropscale'])); 
							$c = "$cache->width:$cache->height"; ?>
					<input type="hidden" name="imagedetails[<?php echo $i; ?>][cropping][<?php echo $cache->id; ?>]" alt="<?php echo $c; ?>" value="<?php echo $cropping; ?>" class="imagecropped" />
				<?php endforeach; endif;?>
			</div>
				<button type="button" name="deleteImage" value="<?php echo $Image->id; ?>" title="Delete category image&hellip;" class="deleteButton"><input type="hidden" name="ieisstupid" value="<?php echo $Image->id; ?>" /><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/delete.png" alt="-" width="16" height="16" /></button></li>
		<?php endforeach; ?>
	</ul>
	<div class="clear"></div>
	<input type="hidden" name="category" value="<?php echo $_GET['id']; ?>" id="image-category-id" />
	<input type="hidden" name="deleteImages" id="deleteImages" value="" />
	<div id="swf-uploader-button"></div>
	<div id="swf-uploader">
	<button type="button" class="button-secondary" name="add-image" id="add-image" tabindex="10"><small><?php _e('Add New Image','Ecart'); ?></small></button></div>
	<div id="browser-uploader">
		<button type="button" name="image_upload" id="image-upload" class="button-secondary"><small><?php _e('Add New Image','Ecart'); ?></small></button><br class="clear"/>
	</div>
	
	<?php _e('Double-click images to edit their details. Save the product to confirm deleted images.','Ecart'); ?>
<?php
}
add_meta_box('category-images', __('Category Images','Ecart').$Admin->boxhelp('category-editor-images'), 'images_meta_box', 'ecart_page_ecart-products', 'normal', 'core');

function templates_meta_box ($Category) {
	$pricerange_menu = array(
		"disabled" => __('Price ranges disabled','Ecart'),
		"auto" => __('Build price ranges automatically','Ecart'),
		"custom" => __('Use custom price ranges','Ecart'),
	);

?>
<p><?php _e('Setup template values that will be copied into new products that are created and assigned this category.','Ecart'); ?></p>
<div id="templates"></div>

<div id="details-template" class="panel">
	<div class="pricing-label">
		<label><?php _e('Product Details','Ecart'); ?></label>
	</div>
	<div class="pricing-ui">

	<ul class="details multipane">
		<li><input type="hidden" name="deletedSpecs" id="deletedSpecs" value="" />
			<div id="details-menu" class="multiple-select options">
				<ul></ul>
			</div>
			<div class="controls">
			<button type="button" id="addDetail" class="button-secondary"><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/add.png" alt="+" width="16" height="16" /><small> <?php _e('Add Detail','Ecart'); ?></small></button>
			</div>
		</li>
		<li id="details-facetedmenu">
			<div id="details-list" class="multiple-select options">
				<ul></ul>
			</div>
			<div class="controls">
			<button type="button" id="addDetailOption" class="button-secondary"><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/add.png" alt="+" width="16" height="16" /><small> <?php _e('Add Option','Ecart'); ?></small></button>
			</div>
		</li>
	</ul>
	<div class="clear"></div>	
	</div>
</div>

<div id="price-ranges" class="panel">
	<div class="pricing-label">
		<label><?php _e('Price Range Search','Ecart'); ?></label>
	</div>
	<div class="pricing-ui">
	<select name="pricerange" id="pricerange-facetedmenu">
		<?php echo menuoptions($pricerange_menu,$Category->pricerange,true); ?>
	</select>
	<ul class="details multipane">
		<li><div id="pricerange-menu" class="multiple-select options"><ul class=""></ul></div>
			<div class="controls">
			<button type="button" id="addPriceLevel" class="button-secondary"><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/add.png" alt="-" width="16" height="16" /><small> <?php _e('Add Price Range','Ecart'); ?></small></button>
			</div>
		</li>
	</ul>
</div>
<div class="clear"></div>
<div id="pricerange"></div>
<p><?php _e('Configure how you want price range options in this category to appear.','Ecart'); ?></p>
</div>

<div id="variations-template">
	<div id="variations-menus" class="panel">
		<div class="pricing-label">
			<label><?php _e('Variation Option Menus','Ecart'); ?></label>
		</div>
		<div class="pricing-ui">
			<p><?php _e('Create a predefined set of variation options for products in this category.','Ecart'); ?></p>
			<ul class="multipane">
				<li><div id="variations-menu" class="multiple-select options menu"><ul></ul></div>
					<div class="controls">
						<button type="button" id="addVariationMenu" class="button-secondary"><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/add.png" alt="+" width="16" height="16" /><small> <?php _e('Add Option Menu','Ecart'); ?></small></button>
					</div>
				</li>
			
				<li>
					<div id="variations-list" class="multiple-select options"></div>
					<div class="controls">
					<button type="button" id="addVariationOption" class="button-secondary"><img src="<?php echo ECART_PLUGINURI; ?>/core/ui/icons/add.png" alt="+" width="16" height="16" /><small> <?php _e('Add Option','Ecart'); ?></small></button>
					</div>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
<br />
<div id="variations-pricing"></div>
</div>


<?php
}
add_meta_box('templates_menus', __('Product Templates &amp; Menus','Ecart').$Admin->boxhelp('category-editor-templates'), 'templates_meta_box', 'ecart_page_ecart-products', 'advanced', 'core');

?>