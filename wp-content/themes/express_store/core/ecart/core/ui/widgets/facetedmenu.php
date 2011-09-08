<?php
/**
 * EcartFacetedMenuWidget class
 * A WordPress widget for showing a drilldown search menu for category products
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartFacetedMenuWidget extends WP_Widget {

    function EcartFacetedMenuWidget() {
        parent::WP_Widget(false,
			$name = __('Ecart Faceted Menu','Ecart'),
			array('description' => __('Category products drill-down search menu','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = __('Product Filters','Ecart');
		$title = $before_title.$options['title'].$after_title;

		if (!empty($Ecart->Category->id) && $Ecart->Category->facetedmenus == "on") {
			$menu = $Ecart->Category->tag('faceted-menu',$options);
			echo $before_widget.$title.$menu.$after_widget;
		}
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

} // class EcartFacetedMenuWidget

register_widget('EcartFacetedMenuWidget');

}
?>