<?php
/**
 * EcartSearchWidget class
 * A WordPress widget for showing a storefront-enabled search form
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartSearchWidget extends WP_Widget {

    function EcartSearchWidget() {
        parent::WP_Widget(false,
			$name = __('Shopping Search','Ecart'),
			array('description' => __('A search widget for your store','Ecart'))
		);
    }

    function widget($args, $options) {
		require_once(ECART_MODEL_PATH."/XML.php");
		global $Ecart;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = __('Shop Search','Ecart');
		$title = $before_title.$options['title'].$after_title;

		$content = $Ecart->Catalog->tag('searchform');
		echo $before_widget.$title.$content.$after_widget;
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

} // END class EcartSearchWidget

register_widget('EcartSearchWidget');

}
?>