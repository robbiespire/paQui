<?php
/**
 * EcartAccountWidget class
 * A WordPress widget to show the account login or account menu if logged in
 *
 * @version 1.0
 * @package ecart
 **/

if (class_exists('WP_Widget')) {

class EcartAccountWidget extends WP_Widget {

    function EcartAccountWidget() {
        parent::WP_Widget(false,
			$name = __('Customer Account','Ecart'),
			array('description' => __('Account login form &amp; and links to customer area','Ecart'))
		);
    }

    function widget($args, $options) {
		global $Ecart;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = __('Your Account','Ecart');
		$title = $before_title.$options['title'].$after_title;
		$request = $_GET;
		unset($_GET['acct']);
		unset($_GET['id']);
		remove_filter('ecart_account_template','ecartdiv');
		add_filter('ecart_show_account_errors',array(&$this,'showerrors'));
		$sidecart = $Ecart->Flow->Controller->account_page();

		echo $before_widget.$title.$sidecart.$after_widget;
		$_GET = array_merge($_GET,$request);
		remove_filter('ecart_show_account_errors',array(&$this,'showerrors'));
		add_filter('ecart_account_template','ecartdiv');
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

	function showerrors () {
		return false;
	}

    function form($options) {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>
		<?php
    }

} // END class EcartAccountWidget

global $Ecart;
if ($Ecart->Settings->get('account_system') == "none") return;

register_widget('EcartAccountWidget');

}
?>