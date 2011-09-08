<?php
/**
 * EcartCategorySectionWidget class
 * A WordPress widget that provides a navigation menu of a Ecart category section (branch)
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartCategorySectionWidget extends WP_Widget {

    function EcartCategorySectionWidget() {
        parent::WP_Widget(false,
			$name = __('Ecart Category Section','Ecart'),
			array('description' => __('A list or dropdown of store categories'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		extract($args);

		$title = $before_title.$options['title'].$after_title;
		unset($options['title']);
		if (empty($Ecart->Category->id)) return false;
		$menu = $Ecart->Category->tag('section-list',$options);
		echo $before_widget.$title.$menu.$after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($options) {
		global $Ecart;

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>

		<p>
		<input type="hidden" name="<?php echo $this->get_field_name('dropdown'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" value="on"<?php echo $options['dropdown'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('dropdown'); ?>"> <?php _e('Show as dropdown','Ecart'); ?></label><br />
		<input type="hidden" name="<?php echo $this->get_field_name('products'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('products'); ?>" name="<?php echo $this->get_field_name('products'); ?>" value="on"<?php echo $options['products'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('products'); ?>"> <?php _e('Show product counts','Ecart'); ?></label><br />
		<input type="hidden" name="<?php echo $this->get_field_name('hierarchy'); ?>" value="off" /><input type="checkbox" id="<?php echo $this->get_field_id('hierarchy'); ?>" name="<?php echo $this->get_field_name('hierarchy'); ?>" value="on"<?php echo $options['hierarchy'] == "on"?' checked="checked"':''; ?> /><label for="<?php echo $this->get_field_id('hierarchy'); ?>"> <?php _e('Show hierarchy','Ecart'); ?></label><br />
		</p>
		<?php
    }

} // class EcartCategorySectionWidget

register_widget('EcartCategorySectionWidget');

}
?>