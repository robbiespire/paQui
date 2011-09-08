<?php
/**
 * EcartProductWidget class
 * A WordPress widget that shows a selection of products
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartProductWidget extends WP_Widget {

    function EcartProductWidget() {
        parent::WP_Widget(false,
			$name = __('Ecart Product','Ecart'),
			array('description' => __('Highlight specific store products','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		extract($args);

		$title = $before_title.$options['title'].$after_title;
		unset($options['title']);

		$content = $Ecart->Catalog->tag('sideproduct',$options);
		echo $before_widget.$title.$content.$after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($options) {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>

		<p><select id="<?php echo $this->get_field_id('source'); ?>" name="<?php echo $this->get_field_name('source'); ?>" class="widefat"><option value="category"<?php echo $options['source'] == "category"?' selected="selected"':''; ?>><?php _e('From a category','Ecart'); ?></option><option value="product"<?php echo $options['source'] == "product"?' selected="selected"':''; ?>><?php _e('By product','Ecart'); ?></option></select></p>

		<?php
			if (ECART_PRETTYURLS) $label = __('Category Slug/ID','Ecart');
			else $label = __('Category ID','Ecart');
		 ?>
		<p id="<?php echo $this->get_field_id('category-fields'); ?>" class="hidden">
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php echo $label; ?></label>
			<input type="text" name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" value="<?php echo $options['category']; ?>">
			<br /><br />
			<select id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>">
				<?php $limits = array(1,2,3,4,5,6,7,8,9,10,15,20,25);
					echo menuoptions($limits,$options['limit']); ?>
			</select>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<?php
					$sortoptions = array(
						"bestselling" => __('Bestselling','Ecart'),
						"highprice" => __('Highest Price','Ecart'),
						"lowprice" => __('Lowest Price','Ecart'),
						"newest" => __('Newest','Ecart'),
						"oldest" => __('Oldest','Ecart'),
						"random" => __('Random','Ecart')
					);
					echo menuoptions($sortoptions,$options['order'],true);
				?>
			</select>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('products','Ecart'); ?></label>
		</p>

		<?php
			if (ECART_PRETTYURLS) $label = __('Product Slug/ID(s)','Ecart');
			else $label = __('Product ID(s)','Ecart');
		 ?>
		<p id="<?php echo $this->get_field_id('product-fields'); ?>" class="hidden">
			<label for="<?php echo $this->get_field_id('product'); ?>"><?php echo $label; ?></label>
			<input type="text" name="<?php echo $this->get_field_name('product'); ?>" id="<?php echo $this->get_field_id('product'); ?>" class="widefat" value="<?php echo $options['product']; ?>">
			<small><?php _e('Use commas to specify multiple products','Ecart')?></small></p>

		<script type="text/javascript">
		(function($) {
			$(window).ready(function () {
				var categoryui = $('#<?php echo $this->get_field_id("category-fields"); ?>');
				var productui = $('#<?php echo $this->get_field_id("product-fields"); ?>');
				$('#<?php echo $this->get_field_id("source"); ?>').change(function () {
					if ($(this).val() == "category") {
						productui.hide();
						categoryui.show();
					}
					if ($(this).val() == "product") {
						categoryui.hide();
						productui.show();
					}
				}).change();
			});
		})(jQuery)
		</script>
		<?php
    }

} // class EcartProductWidget

register_widget('EcartProductWidget');

}
?>