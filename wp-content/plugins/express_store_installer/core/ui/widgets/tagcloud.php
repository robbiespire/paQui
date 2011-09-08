<?php
/**
 * EcartTagCloudWidget class
 * A WordPress widget that shows a cloud of the most popular product tags
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartTagCloudWidget extends WP_Widget {

    function EcartTagCloudWidget() {
        parent::WP_Widget(false,
			$name = __('Ecart Tag Cloud','Ecart'),
			array('description' => __('Popular product tags in a cloud format','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = "Product Tags";
		$title = $before_title.$options['title'].$after_title;

		$tagcloud = $Ecart->Catalog->tag('tagcloud',$options);
		echo $before_widget.$title.$tagcloud.$after_widget;
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

} // class EcartTagCloudWidget

register_widget('EcartTagCloudWidget');

}
?>