<?php
/**
 * EcartCartWidget class
 * A WordPress widget to show the contents of the ecarting cart
 * 
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartCartWidget extends WP_Widget {

    function EcartCartWidget() {
        parent::WP_Widget(false,
			$name = __('Customer Shopping Cart','Ecart'),
			array('description' => __('The customer\'s shopping cart','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = "Your Cart";
		$title = $before_title.$options['title'].$after_title;

		$sidecart = $Ecart->Order->Cart->tag('sidecart',$options);
		echo $before_widget.$title.$sidecart.$after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($options) {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>
		<?php
    }

} // class EcartCartWidget

register_widget('EcartCartWidget');

}
?>