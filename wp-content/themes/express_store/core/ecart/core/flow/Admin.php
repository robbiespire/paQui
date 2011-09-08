<?php
/**
 * AdminFlow
 *
 * @version 1.0
 * @package ecart
 * @subpackage admin
 **/

/**
 * AdminFlow
 *
 * @package admin
 * @since 1.1
 **/
class AdminFlow extends FlowController {

	var $Pages = array();	// List of admin pages
	var $Menus = array();	// List of initialized WordPress menus
	var $Ajax = array();	// List of AJAX controllers
	var $MainMenu = false;
	var $Page = false;
	var $Menu = false;

	/**
	 * Initialize the capabilities, mapping to pages
	 *
	 * Capabilities						Role
	 * _______________________________________________
	 *
	 * ecart_settings					administrator
	 * ecart_settings_checkout
	 * ecart_settings_payments
	 * ecart_settings_shipping
	 * ecart_settings_taxes
	 * ecart_settings_presentation
	 * ecart_settings_system
	 * ecart_settings_update
	 * ecart_financials					ecart-merchant
	 * ecart_promotions
	 * ecart_products
	 * ecart_categories
	 * ecart_orders						ecart-csr
	 * ecart_customers
	 * ecart_menu
	 *
	 * @var $caps
	 **/
	var $caps = array(
		'main'=>'ecart_menu',
		'orders'=>'ecart_orders',
		'customers'=>'ecart_customers',
		'products'=>'ecart_products',
		'categories'=>'ecart_categories',
		'promotions'=>'ecart_promotions',
		'settings'=>'ecart_settings',
		'settings-checkout'=>'ecart_settings_checkout',
		'settings-payments'=>'ecart_settings_payments',
		'settings-shipping'=>'ecart_settings_shipping',
		'settings-taxes'=>'ecart_settings_taxes',
		'settings-presentation'=>'ecart_settings_presentation',
		'settings-system'=>'ecart_settings_system',
		'checkout_set' => 'ecart_menu',
		'payment_set' => 'ecart_menu',
		'shipping_set' => 'ecart_menu',
		'tax_set' => 'ecart_menu',
		'products_set' => 'ecart_menu',
		'storage_set' => 'ecart_menu',
		'inventory' => 'ecart_menu',
		'add_cat' => 'ecart_menu'
	);

	/**
	 * Admin constructor
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __construct () {
		parent::__construct();

		$this->legacyupdate();
		// Add Dashboard Widgets
		add_action('wp_dashboard_setup', array(&$this, 'dashboard'));
		add_action('admin_print_styles-index.php', array(&$this, 'dashboard_css'));
		add_action('admin_init', array(&$this, 'tinymce'));
		add_action('load-plugins.php',array(&$this, 'pluginspage'));
		add_action('switch_theme',array(&$this, 'themepath'));
		add_filter('favorite_actions', array(&$this, 'favorites'));
		add_filter('ecart_admin_boxhelp', array(&$this, 'keystatus'));
		add_action("load-update.php", array(&$this, 'admin_css'));

		// Add the default Ecart pages
		$this->addpage('orders',__('Orders History','Ecart'),'Service','Managing Orders');
		$this->addpage('customers',__('Registered Customers','Ecart'),'Account','Managing Customers');
		$this->addpage('products',__('Products Management','Ecart'),'Warehouse','Editing a Product');
		$this->addpage('categories',__('Categories Setup','Ecart'),'Categorize','Editing a Category');
		$this->addpage('promotions',__('Coupons Setup','Ecart'),'Promote','Running Sales & Promotions');
		$this->addpage('inventory',__('Stock Inventory ','Ecart'),'inventory','Managing Stock');
		$this->addpage('settings',__('Main Settings','Ecart'),'Setup','General Settings');
		$this->addpage('settings-checkout',__('Checkout Settings','Ecart'),'Setup','Checkout Settings',"settings");
		$this->addpage('settings-payments',__('Payments Setup','Ecart'),'Setup','Payments Settings',"settings");
		$this->addpage('settings-shipping',__('Shipping Management','Ecart'),'Setup','Shipping Settings',"settings");
		$this->addpage('settings-taxes',__('Taxes Configuration','Ecart'),'Setup','Taxes Settings',"settings");
		$this->addpage('settings-presentation',__('Catalog Layout','Ecart'),'Setup','Presentation Settings',"settings");
		$this->addpage('settings-system',__('Storage Settings','Ecart'),'Setup','System Settings',"settings");
		$this->addpage('checkout_set',__('Checkout Setup','Ecart'),'checkout_set','Checkout Setup');
		$this->addpage('payment_set',__('Payments Settings','Ecart'),'payment_set','Payments Settings');
		$this->addpage('shipping_set',__('Shipping Management','Ecart'),'shipping_set','Shipping Management');
		$this->addpage('tax_set',__('Taxes Configuration','Ecart'),'tax_set','Editing Taxes');
		$this->addpage('products_set',__('Catalog Layout Settings','Ecart'),'catalog_set','Catalog Management');
		$this->addpage('storage_set',__('Storage Settings','Ecart'),'storage_set','Storage Setup');

		// if ($Ecart->Settings->get('display_welcome') == "on" && empty($_POST['setup']))
			// $this->addpage('welcome',__('Welcome','Ecart'),'Admin',$base);
			
		
		// Action hook for adding custom third-party pages
		do_action('ecart_admin_menu');

		reset($this->Pages);
		$this->MainMenu = key($this->Pages);

		// Set the currently requested page and menu
		if (isset($_GET['page'])) $page = strtolower($_GET['page']);
		else return;
		if (isset($this->Pages[$page])) $this->Page = $this->Pages[$page];
		if (isset($this->Menus[$page])) $this->Menu = $this->Menus[$page];

	}

	/**
	 * Generates the Ecart admin menu
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function menus () {
		global $Ecart;


		$access = defined('ECART_USERLEVEL') ?
			ECART_USERLEVEL:$this->caps['main'];

		if ($this->maintenance()) $access = 'manage_options';

		// Add the main Ecart menu
		$this->Menus['main'] = add_menu_page(
			
			'Ecart',									// Page title
			'Ecart',									// Menu title
			$access,									// Access level
			$this->MainMenu,							// Page
			array(&$Ecart->Flow,'parse'),				// Handler
			ECART_ADMIN_URI.'/icons/ecart.png',			// Icon
			'58.998'
		
		);

		if ($this->maintenance()) {
			add_action("admin_enqueue_scripts", array(&$this, 'behaviors'));
			return add_action('toplevel_page_ecart-orders',array(&$this,'reactivate'));
		}

		// Add menus to WordPress admin
		foreach ($this->Pages as $page) $this->addmenu($page);

		// Add admin JavaScript & CSS
		foreach ($this->Menus as $menu) add_action("admin_enqueue_scripts", array(&$this, 'behaviors'),50);

		// Add contextual help menus
		foreach ($this->Menus as $pagename => $menu) $this->help($pagename,$menu);

	}

	/**
	 * Registers a new page to the Ecart admin pages
	 *	 
	 * @since 1.1
	 *
	 * @param string $name The internal reference name for the page
	 * @param string $label The label displayed in the WordPress admin menu
	 * @param string $controller The name of the controller to use for the page
	 * @param string $doc The title of the documentation article on docs.ecartlugin.net
	 * @param string $parent The internal reference for the parent page
	 * @return void
	 **/
	function addpage ($name,$label,$controller,$doc=false,$parent=false) {
		$page = basename(ECART_PATH)."-$name";
		if (!empty($parent)) $parent = basename(ECART_PATH)."-$parent";
		$this->Pages[$page] = new EcartAdminPage($name,$page,$label,$controller,$doc,$parent);
	}

	/**
	 * Adds a EcartAdminPage entry to the Ecart admin menu
	 * 
	 * @since 1.1
	 *
	 * @return void
	 * @param mixed $page EcartAdminPage object
	 **/
	function addmenu ($page) {
		global $Ecart;
		$name = $page->page;

		$this->Menus[$page->page] = add_submenu_page(($page->parent)?$page->parent:$this->MainMenu, $page->label, $page->label, defined('ECART_USERLEVEL')?ECART_USERLEVEL:$this->caps[$page->name],$name,
			($Ecart->Settings->get('display_welcome') == "off" &&  empty($_POST['setup']))?
				array(&$this,'welcome'):array(&$Ecart->Flow,'admin')
		);
	}

	/**
	 * Takes an internal page name reference and builds the full path name
	 *	 
	 * @since 1.1
	 *
	 * @param string $page The internal reference name for the page
	 * @return string The fully qualified resource name for the admin page
	 **/
	function pagename ($page) {
		return basename(ECART_PATH)."-$page";
	}

	/**
	 * Gets the name of the controller for the current request or the specified page resource
	 *	 
	 * @since 1.1
	 *
	 * @param string $page (optional) The fully qualified reference name for the page
	 * @return string|boolean The name of the controller or false if not available
	 **/
	function controller ($page=false) {
		if (!$page && isset($this->Page->controller)) return $this->Page->controller;
		if (isset($this->Pages[$page])) return $this->Pages[$page]->controller;
		return false;
	}

	/**
	 * Dynamically includes necessary JavaScript and stylesheets for the admin
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function behaviors () {
		global $Ecart,$wp_version,$hook_suffix;
		if (!in_array($hook_suffix,$this->Menus)) return;

		$this->admin_css();

		ecart_enqueue_script('ecart');
		add_action('ecart_print_scripts',array(&$Ecart,'settingsjs'),100);

		$settings = array_filter(array_keys($this->Pages),array(&$this,'get_settings_pages'));
		if (in_array($this->Page->page,$settings)) ecart_enqueue_script('settings');

	}

	/**
	 * Queues the admin stylesheets
	 *	 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function admin_css () {
		wp_enqueue_style('ecart.colorbox',ECART_ADMIN_URI.'/styles/colorbox.css',array(),ECART_VERSION,'screen');
		wp_enqueue_style('ecart.admin',ECART_ADMIN_URI.'/styles/admin.css',array(),ECART_VERSION,'screen');
	}

	/**
	 * Determines if a database schema upgrade is required
	 *	 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function maintenance () {
		$Settings = &EcartSettings();
		$db_version = intval($Settings->get('db_version'));
		if ($db_version != DB::$version) return true;
		return false;
	}

	/**
	 * Adds contextually appropriate help information to interfaces
	 * 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function help ($pagename,$menu) {
		global $Ecart;
		if (!isset($this->Pages[$pagename])) return;
		$page = $this->Pages[$pagename];
		$url = ECART_DOCS.str_replace("+","_",urlencode($page->doc));
		$link = htmlspecialchars($page->doc);
		$content = '<a href="'.$url.'" target="_blank">'.$link.'</a>';

		$target = substr($menu,strrpos($menu,'-')+1);
		if ($target == "orders" || $target == "customers") {
			ob_start();
			include("{$Ecart->path}/core/ui/help/$target.php");
			$help = ob_get_contents();
			ob_end_clean();
			$content .= $help;
		}

		add_contextual_help($menu,$content);
	}

	/**
	 * Returns a postbox help link to launch help screencasts
	 *	 
	 * @since 1.1
	 *
	 * @param string $id The ID of the help resource
	 * @return string The anchor tag for the help link
	 **/
	function boxhelp ($id) {
		$helpurl = add_query_arg(array('src'=>'help','id'=>$id),admin_url('admin.php'));
		return apply_filters('ecart_admin_boxhelp','<a href="'.esc_url($helpurl).'" class="help"></a>');
	}

	/**
	 * Displays the welcome screen
	 *
	 * @return boolean 
	 **/
	function welcome () {
		global $Ecart;
		if ($Ecart->Settings->get('display_welcome') == "on" && empty($_POST['setup'])) {
			include(ECART_ADMIN_PATH."/help/welcome.php");
			return true;
		}
		return false;
	}

	/**
	 * Displays the re-activate screen
	 *
	 * @return boolean	 
	 **/
	function reactivate () {
		global $Ecart;
		include(ECART_ADMIN_PATH."/help/reactivate.php");
	}

	/**
	 * Adds a 'New Product' shortcut to the WordPress admin favorites menu
	 *	 
	 * @since 1.1
	 *
	 * @param array $actions List of actions in the menu
	 * @return array Modified actions list
	 **/
	function favorites ($actions) {
		$key = esc_url(add_query_arg(array('page'=>$this->pagename('products'),'id'=>'new'),'admin.php'));
	    $actions[$key] = array(__('New Product','Ecart'),8);
		return $actions;
	}

	/**
	 * Initializes the Ecart dashboard widgets
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function dashboard () {
		$dashboard = $this->Settings->get('dashboard');
		if (!((is_ecart_userlevel() || current_user_can('ecart_financials')) && $dashboard == "on")) return false;

		wp_add_dashboard_widget('dashboard_ecart_orders', __('Shopping Cart Orders','Ecart'), array(&$this,'orders_widget'),
			array('all_link' => 'admin.php?page='.$this->pagename('orders'),'feed_link' => '','width' => 'half','height' => 'single')
		);

	}

	/**
	 * Loads the Ecart admin CSS on the WordPress dashboard for widget styles
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function dashboard_css () {
		global $Ecart;
		echo "<link rel='stylesheet' href='$Ecart->uri/core/ui/styles/admin.css?ver=".urlencode(ECART_VERSION)."' type='text/css' />\n";
	}

	/**
	 * Dashboard Widgets
	 */
	/**
	 * Renders the order stats widget
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function stats_widget ($args=null) {
		global $Ecart;
		$db = DB::get();
		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);
		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$purchasetable = DatabaseObject::tablename(Purchase::$table);

		$results = $db->query("SELECT count(id) AS orders, SUM(total) AS sales, AVG(total) AS average,
		 						SUM(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),1,0)) AS wkorders,
								SUM(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),total,0)) AS wksales,
								AVG(IF(UNIX_TIMESTAMP(created) > UNIX_TIMESTAMP()-(86400*30),total,null)) AS wkavg
		 						FROM $purchasetable WHERE txnstatus='CHARGED'");

		$orderscreen = add_query_arg('page',$this->pagename('orders'),admin_url('admin.php'));
		echo '<div class="table"><table><tbody>';
		echo '<tr><th colspan="2">'.__('Last 30 Days','Ecart').'</th><th colspan="2">'.__('Lifetime','Ecart').'</th></tr>';

		echo '<tr><td class="amount"><a href="'.$orderscreen.'">'.(int)$results->wkorders.'</a></td><td>'.__('Orders','Ecart').'</td>';
		echo '<td class="amount"><a href="'.$orderscreen.'">'.(int)$results->orders.'</a></td><td>'.__('Orders','Ecart').'</td></tr>';

		echo '<tr><td class="amount"><a href="'.$orderscreen.'">'.money($results->wksales).'</a></td><td>'.__('Sales','Ecart').'</td>';
		echo '<td class="amount"><a href="'.$orderscreen.'">'.money($results->sales).'</a></td><td>'.__('Sales','Ecart').'</td></tr>';

		echo '<tr><td class="amount"><a href="'.$orderscreen.'">'.money($results->wkavg).'</a></td><td>'.__('Average Order','Ecart').'</td>';
		echo '<td class="amount"><a href="'.$orderscreen.'">'.money($results->average).'</a></td><td>'.__('Average Order','Ecart').'</td></tr>';

		echo '</tbody></table></div>';

		echo $after_widget;

	}

	/**
	 * Renders the recent orders dashboard widget
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function orders_widget ($args=null) {
		global $Ecart;
		$db = DB::get();
		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);
		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );
		$statusLabels = $this->Settings->get('order_status');

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$purchasetable = DatabaseObject::tablename(Purchase::$table);
		$purchasedtable = DatabaseObject::tablename(Purchased::$table);

		$Orders = $db->query("SELECT p.*,count(i.id) as items FROM $purchasetable AS p LEFT JOIN $purchasedtable AS i ON i.purchase=p.id GROUP BY i.purchase ORDER BY created DESC LIMIT 6",AS_ARRAY);

		if (!empty($Orders)) {
		echo '<table class="widefat">';
		echo '<tr><th scope="col">'.__('Name','Ecart').'</th><th scope="col">'.__('Date','Ecart').'</th><th scope="col" class="num">'.__('Items','Ecart').'</th><th scope="col" class="num">'.__('Total','Ecart').'</th><th scope="col" class="num">'.__('Status','Ecart').'</th></tr>';
		echo '<tbody id="orders" class="list orders">';
		$even = false;
		foreach ($Orders as $Order) {
			echo '<tr'.((!$even)?' class="alternate"':'').'>';
			$even = !$even;
			echo '<td><a class="row-title" href="'.add_query_arg(array('page'=>$this->pagename('orders'),'id'=>$Order->id),admin_url('admin.php')).'" title="View &quot;Order '.$Order->id.'&quot;">'.((empty($Order->firstname) && empty($Order->lastname))?'(no contact name)':$Order->firstname.' '.$Order->lastname).'</a></td>';
			echo '<td>'.date("Y/m/d",mktimestamp($Order->created)).'</td>';
			echo '<td class="num">'.$Order->items.'</td>';
			echo '<td class="num">'.money($Order->total).'</td>';
			echo '<td class="num">'.$statusLabels[$Order->status].'</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
		} else {
			echo '<p>'.__('No orders, yet.','Ecart').'</p>';
		}

		echo $after_widget;

	}

	/**
	 * Renders the bestselling products dashboard widget
	 *	 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function products_widget ($args=null) {
		global $Ecart;
		$db = DB::get();
		$defaults = array(
			'before_widget' => '',
			'before_title' => '',
			'widget_name' => '',
			'after_title' => '',
			'after_widget' => ''
		);

		if (!$args) $args = array();
		$args = array_merge($defaults,$args);
		if (!empty($args)) extract( $args, EXTR_SKIP );

		echo $before_widget;

		echo $before_title;
		echo $widget_name;
		echo $after_title;

		$RecentBestsellers = new BestsellerProducts(array('where'=>'UNIX_TIMESTAMP(pur.created) > UNIX_TIMESTAMP()-(86400*30)','show'=>3));
		$RecentBestsellers->load_products();

		echo '<table><tbody><tr>';
		echo '<td><h4>'.__('Recent Bestsellers','Ecart').'</h4>';
		echo '<ul>';
		if (empty($RecentBestsellers->products)) echo '<li>'.__('Nothing has been sold, yet.','Ecart').'</li>';
		foreach ($RecentBestsellers->products as $product)
			echo '<li><a href="'.add_query_arg(array('page'=>$this->pagename('products'),'id'=>$product->id),admin_url('admin.php')).'">'.$product->name.'</a> ('.$product->sold.')</li>';
		echo '</ul></td>';


		$LifetimeBestsellers = new BestsellerProducts(array('show'=>3));
		$LifetimeBestsellers->load_products();
		echo '<td><h4>'.__('Lifetime Bestsellers','Ecart').'</h4>';
		echo '<ul>';
		if (empty($LifetimeBestsellers->products)) echo '<li>'.__('Nothing has been sold, yet.','Ecart').'</li>';
		foreach ($LifetimeBestsellers->products as $product)
			echo '<li><a href="'.add_query_arg(array('page'=>$this->pagename('products'),'id'=>$product->id),admin_url('admin.php')).'">'.$product->name.'</a>'.(isset($product->sold)?' ('.$product->sold.')':' (0)').'</li>';
		echo '</ul></td>';
		echo '</tr></tbody></table>';
		echo $after_widget;

	}

	/**
	 * Update the stored path to the activated theme
	 *
	 * Automatically updates the Ecart theme path setting when the
	 * a new theme is activated.
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function themepath () {
		global $Ecart;
		$Ecart->Settings->save('theme_templates',addslashes(sanitize_path(STYLESHEETPATH.'/'."ecart")));
	}

	/**
	 * Report the current status of the update key
	 *	 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function keystatus ($_=true) {
		$Settings =& EcartSettings();
		$status = $Settings->get('updatekey');
		if ($status[0] != "1") return false;
		return $_;
	}

	/**
	 * Helper callback filter to identify editor-related pages in the pages list
	 *	 
	 * @since 1.1
	 *
	 * @param string $pagename The full page reference name
	 * @return boolean True if the page is identified as an editor-related page
	 **/
	function get_editor_pages ($pagenames) {
		$filter = '-edit';
		if (substr($pagenames,strlen($filter)*-1) == $filter) return true;
		else return false;
	}

	/**
	 * Helper callback filter to identify settings pages in the pages list
	 * 
	 * @since 1.1
	 *
	 * @param string $pagename The page's full reference name
	 * @return boolean True if the page is identified as a settings page
	 **/
	function get_settings_pages ($pagenames) {
		$filter = '-settings';
		if (strpos($pagenames,$filter) !== false) return true;
		else return false;
	}

	/**
	 * Initializes the Ecart TinyMCE plugin
	 *	 
	 * @since 1.0
	 *
	 * @return void Description...
	 **/
	function tinymce () {
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;

		// Add TinyMCE buttons when using rich editor
		if (get_user_option('rich_editing') == 'true') {
			global $pagenow,$plugin_page;
			$pages = array('post.php', 'post-new.php', 'page.php', 'page-new.php');
			$editors = array('ecart-products','ecart-categories');
			if(!(in_array($pagenow, $pages) || (in_array($plugin_page, $editors) && !empty($_GET['id']))))
				return false;

			wp_enqueue_script('ecart-tinymce',admin_url('admin-ajax.php').'?action=ecart_tinymce',array());
			wp_localize_script('ecart-tinymce', 'EcartDialog', array(
				'title' => __('Insert from Ecart...', 'Ecart'),
				'desc' => __('Insert a product or category from Ecart...', 'Ecart')
			));

			add_filter('mce_external_plugins', array(&$this,'mceplugin'),5);
			add_filter('mce_buttons', array(&$this,'mcebutton'),5);
		}
	}

	/**
	 * Adds the Ecart TinyMCE plugin to the list of loaded plugins
	 *	 
	 * @since 1.0
	 *
	 * @param array $plugins The current list of plugins to load
	 * @return array The updated list of plugins to laod
	 **/
	function mceplugin ($plugins) {
		// Add a changing query string to keep the TinyMCE plugin from being cached & breaking TinyMCE in Safari/Chrome
		$plugins['Ecart'] = ECART_ADMIN_URI.'/behaviors/tinymce/editor_plugin.js?ver='.mktime();
		return $plugins;
	}

	/**
	 * Adds the Ecart button to the TinyMCE editor
	 * 
	 * @since 1.0
	 *
	 * @param array $buttons The current list of buttons in the editor
	 * @return array The updated list of buttons in the editor
	 **/
	function mcebutton ($buttons) {
		array_push($buttons, "|", "Ecart");
		return $buttons;
	}

	/**
	 * Handle auto-updates from Ecart 1.0
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function legacyupdate () {
		global $plugin_page;

		if ($plugin_page == 'ecart-settings-update'
			&& isset($_GET['updated']) && $_GET['updated'] == 'true') {
				wp_redirect(add_query_arg('page',$this->pagename('orders'),admin_url('admin.php')));
				exit();
		}
	}

	/**
	 * Suppress the standard WordPress plugin update message for the Ecart listing on the plugin page
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function pluginspage () {
		remove_action('after_plugin_row_'.ECART_PLUGINFILE,'wp_plugin_update_row');
	}


} // END class AdminFlow

/**
 * EcartAdminPage class
 *
 * A property container for Ecart's admin page meta
 *
 * @since 1.1
 * @package admin
 **/
class EcartAdminPage {
	var $label = "";
	var $controller = "";
	var $doc = false;
	var $parent = false;

	function __construct ($name,$page,$label,$controller,$doc=false,$parent=false) {
		$this->name = $name;
		$this->page = $page;
		$this->label = $label;
		$this->controller = $controller;
		$this->doc = $doc;
		$this->parent = $parent;
	}

} // END class EcartAdminPage

?>