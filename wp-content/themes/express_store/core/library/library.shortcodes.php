<?php
if(__FILE__ == $_SERVER['SCRIPT_FILENAME'])
{
    header('HTTP/1.0 404 Not Found');
      exit;
}

/*-----------------------------------------------------------------------------------*/
/* Options Framework Shortcode Library - new component under development
/*-----------------------------------------------------------------------------------*/

$plugin_dir = '';

define('ECARTREGCUST_DIR', CORE_LIB . '/' . $plugin_dir);
define('ECARTREGCUST_URL', CORE_LIB . '/' . $plugin_dir);


/**
 * Ecartregcust Class
 * 
 * @package ecartregcust
 * @author 
 */
class ecartregcust {
    public $user = array();
    
    private $views_path     = '/';
    private $table_prefix = '';
    private $ecart_version = array('1.1.5', '1.1.6', '1.1.7');
    private $error_message = '';
    private $ecart_account_type = '';
    private $plugin_name = 'Ecart Reg';
    private $plugin_tag = 'ecartreg';
    private $options = array();
    private $pluginAdminMenu;
    /**
     * ecartregcust::__construct()
     * 
     * @return
     */
    public function __construct()
    {
        global $Ecart;
        $no_errors = true;

        if (isset($Ecart))
        {
            if (!in_array($Ecart->Settings->registry['version'], $this->ecart_version))
            {
                $no_errors = true;
                $this->error_message = 'Ecart Customer Register has only been tested on the following Ecart versions: ' . implode(', ', $this->ecart_version);
            }
            if ($Ecart->Settings->registry['account_system'] == 'none') 
            {
                $no_errors = true;
                $this->error_message = 'Ecart \'Customer Accounts\' is set to \'No Accounts\', so user registrations is disabled.';
            }
        }
        else
        {
            $no_errors = false;
            $this->error_message = 'The Ecart plugin needs to be activated for Ecart Customer Register to work.';
        }
        if ($no_errors)
        {
            $this->setup_options();
            add_shortcode('ecart_regform', array(&$this, 'shortcode'));
            global $table_prefix;
            $this->ecart_account_type = $Ecart->Settings->registry['account_system'];
            $this->table_prefix = $table_prefix;
        }
        else
        {
            add_action('admin_notices', array(&$this, 'admin_notices'));
        }
    }
    /**
     * ecartregcust::setup_options()
     * 
     * @return void
     */
    function setup_options()
    {
        $plugin_icon = ECARTREGCUST_URL . 'plugin_icon.png';
        
       // $this->pluginAdminMenu = new pluginAdminMenu($this->plugin_tag, $this->plugin_name, $plugin_icon);
        
        /*
        $this->options = $this->pluginAdminMenu->set_options($html_options = array (
            
            array( "name" => "General Settings",
            	   "type" => "section"),
                   
            array( "name" => "Show Billing",
            	   "desc" => "Check this to show billing form.",
            	   "id" => $this->plugin_tag."_show_billing",
            	   "type" => "checkbox"),                                          
            array('type' => 'close'),
        ));*/
    } 
    /**
     * ecartregcust::admin_notices()
     * 
     * @return
     */
    public function admin_notices()
    {
        ?><div id="message" class="updated"><p><?php echo $this->error_message; ?></p></div><?php
    }    
    /**
     * ecartregcust::shortcode()
     * 
     * @return
     */
    public function shortcode()
    {
        $data = array();
        $data['show_form'] = true;
        $data['ecart_account_type'] = $this->ecart_account_type;
        $this->show_billing = (isset($this->options['ecartreg_show_billing']) && $this->options['ecartreg_show_billing'] == 1) ? true : false;
        
            global $Ecart;
            $base = $Ecart->Settings->get('base_operations');
            $countries = $Ecart->Settings->get('target_markets');
            $selected_country = (isset($_POST['billing']['country'])) ? $_POST['billing']['country'] : $base['country'];
            $data['countries_select_html'] = menuoptions($countries,$selected_country,true);
            $data['show_billing'] = $this->show_billing;
       
        if (isset($_POST['customer']))
        {
            $user = $this->add_user();
                
            if (!$user)
            {
                $data['form_error'] = $this->form_error;
            }
            else
            {
                $this->user = $user;
                $data['show_form'] = false;
                $data['form_success'] = 'Thank you for registering.';
                do_action('scr_registration_success');
            }
        }
        $html = $this->view('library.register.form.php', $data);
        return ($html !== false) ? $html : '';
    }   
    /**
     * ecartregcust::add_user()
     * 
     * @param mixed $data
     * @return
     */
    private function add_user()
    {
        require_once(ABSPATH."/wp-includes/registration.php");
        
        $Errors =& EcartErrors();
        $Errors->reset();
        if (empty($_POST['customer']['email'])) 
        {
            $this->form_error = 'Email address is required.';
            return false;
        }
        if ($this->email_exists($_POST['customer']['email'])) 
        {
            $this->form_error = 'Email address is already registered with another customer.';
            return false;
        }
        if (empty($_POST['customer']['password'])) 
        {
            $this->form_error = 'Password is required.';
            return false;
        }
        if ($_POST['customer']['password'] !== $_POST['customer']['confirm-password']) 
        {
            $this->form_error = 'Passwords do not match.';
            return false;
        } 
        if ($this->ecart_account_type == 'wordpress')
        {
            if (empty($_POST['customer']['loginname'])) 
            {
                $this->form_error = 'Username is already registered.';
                return false;                
            }
            if (email_exists($_POST['customer']['email']))
            {
                $this->form_error = 'Email address is already registered with another user.';
                return false;                 
            }
        }
        if ($this->show_billing)
        {
            if (empty($_POST['billing']['address']))
            {
                $this->form_error = 'Street address is required.';
                return false;                
            }
            if (empty($_POST['billing']['city']))
            {
                $this->form_error = 'City is required.';
                return false;                
            }
            if (empty($_POST['billing']['state']))
            {
                $this->form_error = 'State is required.';
                return false;                
            }
            if (empty($_POST['billing']['postcode']))
            {
                $this->form_error = 'Postcode is required.';
                return false;                
            }
        }
        
        $customer_data = $_POST['customer'];
        
        $ecart_customer = new Customer();
        $ecart_customer->updates($customer_data);
        
        if ($this->ecart_account_type == 'wordpress') 
        {
            $ecart_customer->create_wpuser(); // not logged in, create new account
            $customer_data['wpuser'] = $ecart_customer->wpuser;
            unset($ecart_customer->password);
            if ($Errors->exist(ECART_ERR)) 
            {
                $ecart_error = $Errors->get(ECART_ERR);
                $this->form_error = implode(', ', $ecart_error[0]->messages);
                return false;
            }
        }
        else
        {       
            $ecart_customer->password = wp_hash_password($data['password']);
        }        
        $ecart_customer->save();
        
        if ($Errors->exist(ECART_ERR)) 
        {
            $ecart_error = $Errors->get(ECART_ERR);
            $this->form_error = implode(', ', $ecart_error[0]->messages);
            return false;
        }                
        if ($this->show_billing)
        {
            $billing_data = $_POST['billing'];
            $ecart_billing = new Billing();  
            $ecart_billing->updates($billing_data);
            $ecart_billing->customer = $ecart_customer->id;
            $ecart_billing->save();
        }        
        
        return $customer_data;
    }
    /**
     * ecartregcust::email_exists()
     * 
     * @param mixed $email
     * @return
     */
    private function email_exists($email)
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT `email` FROM `' . $this->table_prefix . ECART_DBPREFIX . 'customer` WHERE `email` = "' . $email . '"');
        return (count($rows) > 0) ? true : false;        
    } 
    /**
     * ecartregcust::view()
     * 
     * @param string $file
     * @param mixed $data
     * @param bool $return_html
     * @return
     */
    private function view($file = 'none', $data = array(), $return_html = true)
    {
        $view_file = ECARTREGCUST_DIR . $this->views_path . $file;
        if (file_exists($view_file))
        {
            if (is_array($data) && count($data) > 0) { 
                foreach ($data as $key => $value) { 
                    $key = str_replace('-', '', $key); $$key = $value;  
                } 
            }
            if ($return_html == true) { 
                ob_start(); 
                include $view_file; 
                $contents = ob_get_contents(); 
                ob_end_clean(); 
                return $contents; 
            } 
            else { 
                include $view_file; 
            }
        }
        else
        {
            return false;
        }
    }       
}

/**
 * ecartregcust()
 * 
 * @return
 */
function ecartregcust() { 
	global $ecartregcust; 
	$ecartregcust = new ecartregcust(); 
}
add_action('init', 'ecartregcust');

/*
+------------------------------------------------------+
+++++++++++++ FRAMEWORK SPECIFIC SHORTCODES +++++++++++
+------------------------------------------------------+
*/

function ep_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_third', 'ep_one_third');

function ep_one_third_last( $atts, $content = null ) {
   return '<div class="one_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_third_last', 'ep_one_third_last');

function ep_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_third', 'ep_two_third');

function ep_two_third_last( $atts, $content = null ) {
   return '<div class="two_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_third_last', 'ep_two_third_last');

function ep_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_half', 'ep_one_half');

function ep_one_half_last( $atts, $content = null ) {
   return '<div class="one_half last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_half_last', 'ep_one_half_last');

function ep_one_fourth( $atts, $content = null ) {
   return '<div class="one_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fourth', 'ep_one_fourth');

function ep_one_fourth_last( $atts, $content = null ) {
   return '<div class="one_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fourth_last', 'ep_one_fourth_last');

function ep_three_fourth( $atts, $content = null ) {
   return '<div class="three_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fourth', 'ep_three_fourth');

function ep_three_fourth_last( $atts, $content = null ) {
   return '<div class="three_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fourth_last', 'ep_three_fourth_last');

function ep_one_fifth( $atts, $content = null ) {
   return '<div class="one_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fifth', 'ep_one_fifth');

function ep_one_fifth_last( $atts, $content = null ) {
   return '<div class="one_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fifth_last', 'ep_one_fifth_last');

function ep_two_fifth( $atts, $content = null ) {
   return '<div class="two_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_fifth', 'ep_two_fifth');

function ep_two_fifth_last( $atts, $content = null ) {
   return '<div class="two_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_fifth_last', 'ep_two_fifth_last');

function ep_three_fifth( $atts, $content = null ) {
   return '<div class="three_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fifth', 'ep_three_fifth');

function ep_three_fifth_last( $atts, $content = null ) {
   return '<div class="three_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fifth_last', 'ep_three_fifth_last');

function ep_four_fifth( $atts, $content = null ) {
   return '<div class="four_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('four_fifth', 'ep_four_fifth');

function ep_four_fifth_last( $atts, $content = null ) {
   return '<div class="four_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('four_fifth_last', 'ep_four_fifth_last');

function ep_one_sixth( $atts, $content = null ) {
   return '<div class="one_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_sixth', 'ep_one_sixth');

function ep_one_sixth_last( $atts, $content = null ) {
   return '<div class="one_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_sixth_last', 'ep_one_sixth_last');

function ep_five_sixth( $atts, $content = null ) {
   return '<div class="five_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('five_sixth', 'ep_five_sixth');

function ep_five_sixth_last( $atts, $content = null ) {
   return '<div class="five_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('five_sixth_last', 'ep_five_sixth_last');


/*
+------------------------------------------------------+
+++++++++++++ FRAMEWORK SPECIFIC SHORTCODES +++++++++++
+------------------------------------------------------+
*/

/**
 * icon:zoom, doc, play
 */
function ep_lightbox($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'href' => '#',
		'title' => '',
		'group' => '',
		'width' => false,
		'height' => false,
		'iframe' => 'false',
		'inline' => 'false',
		'photo' => 'false',
		'close' => 'true',
	), $atts));
	
	if($width && is_numeric($width)){
		$width = ' data-width="'.$width.'"';
	}else{
		$width = '';
	}
	if($height && is_numeric($height)){
		$height = ' data-height="'.$height.'"';
	}else{
		$height = '';
	}
	
	if($iframe != 'false'){
		$iframe = ' data-iframe="true"';
	}else{
		$iframe = ' data-iframe="false"';
	}
	if($inline != 'false'){
		$inline = ' data-inline="true" data-href="'.$href.'"';
		$href = '#';
	}else{
		$inline = ' data-inline="false"';
	}
	if($photo != 'false'){
		$photo = ' data-photo="true"';
	}else{
		$photo = ' data-photo="false"';
	}
	if($close != 'false'){
		$close = ' data-close="true"';
	}else{
		$close = ' data-close="false"';
	}
	
	$content = do_shortcode(str_replace('[button','[button button="true"',$content));
	
	return '<a title="'.$title.'" href="'.$href.'"'.($group?' rel="'.$group.'"':'').' class="colorbox"'.$width.$height.$iframe.$inline.$photo.$close.'>'.$content.'</a>';
}

add_shortcode('lightbox', 'ep_lightbox');

function ep_video_shortcode($atts){
	if(isset($atts['type'])){
		switch($atts['type']){
			case 'html5':
				return theme_video_html5($atts);
				break;
			case 'flash':
				return theme_video_flash($atts);
				break;
			case 'youtube':
				return theme_video_youtube($atts);
				break;
			case 'vimeo':
				return theme_video_vimeo($atts);
				break;
			case 'dailymotion':
				return theme_video_dailymotion($atts);
				break;
		}
	}
	return '';
}
add_shortcode('video', 'ep_video_shortcode');

function theme_video_html5($atts){
	extract(shortcode_atts(array(
		'mp4' => '',
		'webm' => '',
		'ogg' => '',
		'poster' => '',
		'width' => false,
		'height' => false,
		'preload' => false,
		'autoplay' => false,
	), $atts));

	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		$height = get_option('video','html5_height');
		$width = get_option('video','html5_width');
	}

	// MP4 Source Supplied
	if ($mp4) {
		$mp4_source = '<source src="'.$mp4.'" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>';
		$mp4_link = '<a href="'.$mp4.'">MP4</a>';
	}

	// WebM Source Supplied
	if ($webm) {
		$webm_source = '<source src="'.$webm.'" type=\'video/webm; codecs="vp8, vorbis"\'>';
		$webm_link = '<a href="'.$webm.'">WebM</a>';
	}

	// Ogg source supplied
	if ($ogg) {
		$ogg_source = '<source src="'.$ogg.'" type=\'video/ogg; codecs="theora, vorbis"\'>';
		$ogg_link = '<a href="'.$ogg.'">Ogg</a>';
	}

	if ($poster) {
		$poster_attribute = 'poster="'.$poster.'"';
		$image_fallback = <<<_end_
			<img src="$poster" width="$width" height="$height" alt="Poster Image" title="No video playback capabilities." />
_end_;
	}

	if ($preload) {
		$preload_attribute = 'preload="auto"';
		$flow_player_preload = ',"autoBuffering":true';
	} else {
		$preload_attribute = 'preload="none"';
		$flow_player_preload = ',"autoBuffering":false';
	}

	if ($autoplay) {
		$autoplay_attribute = "autoplay";
		$flow_player_autoplay = ',"autoPlay":true';
	} else {
		$autoplay_attribute = "";
		$flow_player_autoplay = ',"autoPlay":false';
	}

	$uri = THEME_URI;

	$output = <<<HTML
<div class="video_frame video-js-box hu-css">
	<video class="video-js hu-css" width="{$width}" height="{$height}" {$poster_attribute} controls {$preload_attribute} {$autoplay_attribute}>
		{$mp4_source}
		{$webm_source}
		{$ogg_source}
		<object class="vjs-flash-fallback hu-css" width="{$width}" height="{$height}" type="application/x-shockwave-flash"
			data="http://releases.flowplayer.org/swf/flowplayer-3.2.5.swf">
			<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.5.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="wmode" value="opaque" />
			<param name="flashvars" value='config={"clip":{"url":"$mp4" $flow_player_autoplay $flow_player_preload ,"wmode":"opaque"}}' />
			{$image_fallback}
		</object>
	</video>
	<p class="vjs-no-video"><strong>Download Video:</strong>
		{$mp4_link}
		{$webm_link}
		{$ogg_link}
	</p>
</div>

HTML;

	return ''.$output.'';

}

function theme_video_flash($atts) {
	extract(shortcode_atts(array(
		'src' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
		'play'			=> 'false',
		'flashvars' => '',
	), $atts));
	
	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		$height = '350';
		$width = '530';
	}

	$uri = THEME_URI;
	if (!empty($src)){
		return <<<HTML
<div class="video_frame">
<object width="{$width}" height="{$height}" type="application/x-shockwave-flash" data="{$src}">
	<param name="movie" value="{$src}" />
	<param name="allowFullScreen" value="true" />
	<param name="allowscriptaccess" value="always" />
	<param name="expressInstaller" value="{$uri}/swf/expressInstall.swf"/>
	<param name="play" value="{$play}"/>
	<param name="wmode" value="opaque" />
	<embed src="$src" type="application/x-shockwave-flash" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" width="{$width}" height="{$height}" />
</object>
</div>
HTML;
	}
}

function theme_video_vimeo($atts) {
	extract(shortcode_atts(array(
		'clip_id' 	=> '',
		'width' => false,
		'height' => false,
		'title' => 'false',
	), $atts));

	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		$height = '350';
		$width = '530';
	}
	if($title!='false'){
		$title = 1;
	}else{
		$title = 0;
	}

	if (!empty($clip_id) && is_numeric($clip_id)){
		return "<div class='video_frame'><iframe src='http://player.vimeo.com/video/$clip_id?title={$title}&amp;byline=0&amp;portrait=0' width='$width' height='$height' frameborder='0'></iframe></div>";
	}
}

function theme_video_youtube($atts, $content=null) {
	extract(shortcode_atts(array(
		'clip_id' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
	), $atts));
	
	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16) + 25;
	if (!$height && !$width){
		$height = '350';
		$width = '530';
	}

	if (!empty($clip_id)){
		return "<div class='video_frame'><iframe src='http://www.youtube.com/embed/$clip_id' width='$width' height='$height' frameborder='0'></iframe></div>";
	}
}

function theme_video_dailymotion($atts, $content=null) {

	extract(shortcode_atts(array(
		'clip_id' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
	), $atts));
	
	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		$height = '350';
		$width = '530';
	}

	if (!empty($clip_id)){
		return "<div class='video_frame'><iframe src=http://www.dailymotion.com/embed/video/$clip_id?width=$width&theme=none&foreground=%23F7FFFD&highlight=%23FFC300&background=%23171D1B&start=&animatedTitle=&iframe=1&additionalInfos=0&autoPlay=0&hideInfos=0' width='$width' height='$height' frameborder='0'></iframe></div>";
	}
}

/* Typography shortcodes */

function ep_paragraph($atts, $content = null) {
	return'<p>' . do_shortcode($content) . '</p>';
}
add_shortcode('p', 'ep_paragraph');

function ep_highlight_yellow($atts, $content = null) {
	return'<span class="highlight_yellow">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_yellow', 'ep_highlight_yellow');

function ep_highlight_green($atts, $content = null) {
	return'<span class="highlight_green">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_green', 'ep_highlight_green');

function ep_highlight_blue($atts, $content = null) {
	return'<span class="highlight_blue">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_blue', 'ep_highlight_blue');

function ep_highlight_gray($atts, $content = null) {
	return'<span class="highlight_gray">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_gray', 'ep_highlight_gray');

function ep_highlight_black($atts, $content = null) {
	return'<span class="highlight_black">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_black', 'ep_highlight_black');

function ep_highlight_red($atts, $content = null) {
	return'<span class="highlight_red">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight_red', 'ep_highlight_red');

function ep_dropcapcircle($atts, $content = null) {
	return'<span class="dropcapcircle">' . do_shortcode($content) . '</span>';
}
add_shortcode('dropcapcircle', 'ep_dropcapcircle');

function ep_dropcap($atts, $content = null) {
	return'<span class="dropcap">' . do_shortcode($content) . '</span>';
}
add_shortcode('dropcap', 'ep_dropcap');

function ep_check_list($atts, $content = null) {
	return'<ul class="check-list">' . do_shortcode($content) . '</ul>';
}
add_shortcode('checklist', 'ep_check_list');

function ep_next_list($atts, $content = null) {
	return'<ul class="nextlist">' . do_shortcode($content) . '</ul>';
}
add_shortcode('nextlist', 'ep_next_list');

function ep_wrong_list($atts, $content = null) {
	return'<ul class="nolist">' . do_shortcode($content) . '</ul>';
}
add_shortcode('wronglist', 'ep_wrong_list');

function ep_info_list($atts, $content = null) {
	return'<ul class="infolist">' . do_shortcode($content) . '</ul>';
}
add_shortcode('infolist', 'ep_info_list');

function ep_doc_list($atts, $content = null) {
	return'<ul class="documentlist">' . do_shortcode($content) . '</ul>';
}
add_shortcode('doclist', 'ep_doc_list');

function ep_dot_list($atts, $content = null) {
	return'<ul class="dot-list">' . do_shortcode($content) . '</ul>';
}
add_shortcode('dotlist', 'ep_dot_list');

function ep_pull_right($atts, $content = null) {
	return'<span class="pullright">' . $content . '</span>';
}
add_shortcode('pullright', 'ep_pull_right');

function ep_pull_left($atts, $content = null) {
	return'<span class="pullleft">' . $content . '</span>';
}
add_shortcode('pullleft', 'ep_pull_left');

function ep_image_right($atts, $content = null) {
	return '<img class="imageright" src="' . $content . '" alt="image right" />';
}
add_shortcode('imageright', 'ep_image_right');

function ep_image_left($atts, $content = null) {
	return '<img class="imageleft" src="' . $content . '" alt="image left" />';
}
add_shortcode('imageleft', 'ep_image_left');

function ep_caption_left($atts, $content = null) {
	extract(shortcode_atts(array(
		"caption" => ''
	), $atts));
	return '<div class="blockleft"><img src="' . $content . '" alt="image with caption" /><p class="caption">' . $caption . '</p></div>';
}
add_shortcode('captionleft', 'ep_caption_left');

function ep_caption_left_link($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => '',
		"caption" => ''
	), $atts));
	return '<div class="blockleft"><a href="' . $href . '" rel="example1" title="image"><img src="' . $content . '" alt="image with caption" /></a><p class="caption">' . $caption . '</p></div>';
}
add_shortcode('captionleftlink', 'ep_caption_left_link');

function ep_caption_right($atts, $content = null) {
	extract(shortcode_atts(array(
		"caption" => ''
	), $atts));
	return '<div class="blockright"><img src="' . $content . '" alt="image with caption" /><p class="caption">' . $caption . '</p></div>';
}
add_shortcode('captionright', 'ep_caption_right');

function ep_caption_right_link($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => '',
		"caption" => ''
	), $atts));
	return '<div class="blockright"><a href="' . $href . '" rel="example1" title="image"><img src="' . $content . '" alt="image with caption" /></a><p class="caption">' . $caption . '</p></div>';
}
add_shortcode('captionrightlink', 'ep_caption_right_link');


function ep_alert_green($atts, $content = null) {
	return'<div class="alert_green">' . $content . '</div>';
}
add_shortcode('alert_green', 'ep_alert_green');

function ep_alert_blue($atts, $content = null) {
	return'<div class="alert_blue">' . $content . '</div>';
}
add_shortcode('alert_blue', 'ep_alert_blue');

function ep_alert_yellow($atts, $content = null) {
	return'<div class="alert_yellow">' . $content . '</div>';
}
add_shortcode('alert_yellow', 'ep_alert_yellow');

function ep_alert_red($atts, $content = null) {
	return'<div class="alert_red">' . $content . '</div>';
}
add_shortcode('alert_red', 'ep_alert_red');



function ep_toggle( $atts, $content = null)
{
 extract(shortcode_atts(array(
        'title'      => '',
        ), $atts));
   return '<h4 class="toggle"><a href="#">'.$title.'</a></h4><div class="toggle_body"><div class="block">'. do_shortcode($content) . '</div></div>';
}
add_shortcode('toggle', 'ep_toggle');


/**
 * Tab Shortcode
 */
function ep_tabs($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'style' => false
	), $atts));
	
	if (!preg_match_all("/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches)) {
		return do_shortcode($content);
	} else {
		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
		}
		$output = '<ul class="'.$code.'">';
		
		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= '<li><a href="#">' . $matches[3][$i]['title'] . '</a></li>';
		}
		$output .= '</ul>';
		$output .= '<div class="panes">';
		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= '<div class="pane">' . do_shortcode(trim($matches[5][$i])) . '</div>';
		}
		$output .= '</div>';
		
		return '<div class="'.$code.'_container">' . $output . '</div>';
	}
}
add_shortcode('tabs', 'ep_tabs');


function ep_accordions($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'style' => false
	), $atts));
	
	if (!preg_match_all("/(.?)\[(accordion)\b(.*?)(?:(\/))?\](?:(.+?)\[\/accordion\])?(.?)/s", $content, $matches)) {
		return do_shortcode($content);
	} else {
		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
		}
		$output = '';
		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= '<div class="tab">' . $matches[3][$i]['title'] . '</div>';
			$output .= '<div class="pane">' . do_shortcode(trim($matches[5][$i])) . '</div>';
		}

		return '<div class="accordion">' . $output . '</div>';
	}
}
add_shortcode('accordions', 'ep_accordions');


function ep_small_button_black($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallblack"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-black', 'ep_small_button_black');

function ep_small_button_grey($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallgrey"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-grey', 'ep_small_button_grey');

function ep_small_button_red($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallred"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-red', 'ep_small_button_red');

function ep_small_button_orange($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallorange"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-orange', 'ep_small_button_orange');

function ep_small_button_blue($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallblue"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-blue', 'ep_small_button_blue');

function ep_small_button_brown($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallbrown"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-brown', 'ep_small_button_brown');

function ep_small_button_violet($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallviolet"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-violet', 'ep_small_button_violet');

function ep_small_button_green($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="small-button smallgreen"><span>' . $content . '</span></a>';
}
add_shortcode('small-button-green', 'ep_small_button_green');

function ep_big_button_black($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="big-button bigblack"><span>' . $content . '</span></a>';
}
add_shortcode('big-button-black', 'ep_big_button_black');

function ep_big_button_blue($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="big-button bigblue"><span>' . $content . '</span></a>';
}
add_shortcode('big-button-blue', 'ep_big_button_blue');

function ep_big_button_red($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => ''
	), $atts));
	return '<a href="' . $href . '" class="big-button bigred"><span>' . $content . '</span></a>';
}
add_shortcode('big-button-red', 'ep_big_button_red');

function ep_clear_fix($atts, $content = null) {
	return'<div class="clearfix">'.'</div>';
}
add_shortcode('clearfix', 'ep_clear_fix');




function ep_shortcode_framed_box($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'width' => '',
		'height' => '',
		'bgcolor' => '',
		'textcolor' => '',
		'rounded' => 'false',
		'title' => 'Plan',
		'price' => '20$',
		'fee' => '/mo',
	), $atts));
	
	$width = $width?'width:'.$width.'px;':'';
	$height = $height?'height:'.$height.'px;':'';

	if(!empty($width)){
		$style = ' style="'.$width.'"';
	}else{
		$style = '';
	}

	$bgcolor = $bgcolor?'background-color:'.$bgcolor.';':'';
	$textcolor = $textcolor?'color:'.$textcolor:'';
	$rounded = ($rounded == 'true')?' rounded':'';
	if( !empty($height) || !empty($bgcolor) || !empty($textcolor)){
		$content_style = ' style="'.$height.$bgcolor.$textcolor.'"';
	}else{
		$content_style = '';
	}
	
	return '<div class="' . $code .$rounded. '"'.$style.'><div class="framed_box_content"'.$content_style.'>' . '<h4>' . $title . '</h4>' . '<h1>' . $price . '<span>' . $fee . '</span></h1>' . do_shortcode($content) . '<div class="clearboth"></div></div></div>';
}
add_shortcode('framed-box','ep_shortcode_framed_box');


function ep_html5_video($atts, $content = null) {
	extract(shortcode_atts(array(
		"href" => '',
	), $atts));
	return '<a href="' . $href . '"  style="display:block;width:520px;height:330px"  class="video_player"></a>';
}
add_shortcode('html5-video', 'ep_html5_video');


/* Disabilitazione filtri wp auto e texturize */

/*
function my_wpautop_correction() {	
	

		function my_wpautop( $pee ) {

			return wpautop($pee, 0);

		}		

		remove_filter( 'the_content', 'wpautop' );

		remove_filter( 'the_excerpt', 'wpautop' );		

		add_filter( 'the_content', 'my_wpautop' );		

		add_filter( 'the_excerpt', 'my_wpautop' );		


}

add_action('init', 'my_wpautop_correction');*/

function my_formatter($content) {
	$new_content = '';
	$pattern_full = '{(\[raw\].*?\[/raw\])}is';
	$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
	$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($pieces as $piece) {
		if (preg_match($pattern_contents, $piece, $matches)) {
			$new_content .= $matches[1];
		} else {
			$new_content .= wptexturize(wpautop($piece));
		}
	}

	return $new_content;
}

remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'my_formatter', 99);

@ini_set('pcre.backtrack_limit', 500000);

?>