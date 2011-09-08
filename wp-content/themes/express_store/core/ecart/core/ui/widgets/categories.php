<?php
/**
 * EcartCategoriesWidget class
 * A WordPress widget that provides a navigation menu of Ecart categories
 * 
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartCategoriesWidget extends WP_Widget {

    function EcartCategoriesWidget() {
        parent::WP_Widget(false,
			$name = __('Store Categories','Ecart'),
			array('description' => __('Create a list or dropdown of store categories','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		extract($args);

		$title = $before_title.$options['title'].$after_title;
		unset($options['title']);
		$menu = $Ecart->Catalog->tag('category-list',$options);
		echo $before_widget.$title.$menu.$after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($options) {
		global $Ecart;

		// if (isset($_POST['categories_widget_options'])) {
		// 	$options = $_POST['ecart_categories_options'];
		// 	$Ecart->Settings->save('categories_widget_options',$options);
		// }
		//
		// $options = $Ecart->Settings->get('categories_widget_options');

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>

		<p>
		<input type="hidden" name="<?php echo $this->get_field_name('dropdown'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" value="on"<?php echo $options['dropdown'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('dropdown'); ?>"> <?php _e('Show as dropdown','Ecart'); ?></label><br />
		<input type="hidden" name="<?php echo $this->get_field_name('products'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('products'); ?>" name="<?php echo $this->get_field_name('products'); ?>" value="on"<?php echo $options['products'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('products'); ?>"> <?php _e('Show product counts','Ecart'); ?></label><br />
		<input type="hidden" name="<?php echo $this->get_field_name('hierarchy'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('hierarchy'); ?>" name="<?php echo $this->get_field_name('hierarchy'); ?>" value="on"<?php echo $options['hierarchy'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('hierarchy'); ?>"> <?php _e('Show hierarchy','Ecart'); ?></label><br />
		</p>
		<p><label for="<?php echo $this->get_field_id('showsmart'); ?>">Smart Categories:
			<select id="<?php echo $this->get_field_id('showsmart'); ?>" name="<?php echo $this->get_field_name('showsmart'); ?>" class="widefat"><option value="false">Hide</option><option value="before"<?php echo $options['showsmart'] == "before"?' selected="selected"':''; ?>><?php _e('Include before custom categories','Ecart'); ?></option><option value="after"<?php echo $options['showsmart'] == "after"?' selected="selected"':''; ?>><?php _e('Include after custom categories','Ecart'); ?></option></select></label></p>
		<?php
    }

} // class EcartCategoriesWidget

register_widget('EcartCategoriesWidget');

}
?>