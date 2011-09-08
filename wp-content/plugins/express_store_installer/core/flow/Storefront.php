<?php
/**
 * Storefront
 *
 * Flow controller for the front-end shopping interfaces
 *
 * @version 1.0
 * @package ecart
 * @subpackage storefront
 **/

/**
 * Storefront
 *
 * @since 1.1
 * @package ecart
 * @subpackage storefront
 **/
class Storefront extends FlowController {

	static $_pages = array(
		
	);

	var $Settings = false;
	var $Page = false;
	var $Catalog = false;
	var $Category = false;
	var $Product = false;
	var $breadcrumb = false;
	var $referrer = false;
	var $search = false;		// The search query string
	var $searching = false;		// Flags if a search request has been made
	var $checkout = false;		// Flags when the checkout form is being processed
	var $pages = array();
	var $browsing = array();
	var $behaviors = array();	// Runtime JavaScript behaviors

	function __construct () {
		global $Ecart;
		parent::__construct();

		$this->Settings = &$Ecart->Settings;
		$this->Catalog = &$Ecart->Catalog;
		$this->Category = &$Ecart->Category;
		$this->Product = &$Ecart->Product;

		$pages = $this->Settings->get('pages');
		if (!empty($pages)) $this->pages = $pages;

		Ecart_buyObject::store('search',$this->search);
		Ecart_buyObject::store('browsing',$this->browsing);
		Ecart_buyObject::store('breadcrumb',$this->breadcrumb);
		Ecart_buyObject::store('referrer',$this->referrer);

		add_action('wp', array(&$this, 'pageid'));
		add_action('wp', array(&$this, 'security'));
		add_action('wp', array(&$this, 'cart'));
		add_action('wp', array(&$this, 'catalog'));
		add_action('wp', array(&$this, 'shortcodes'));
		add_action('wp', array(&$this, 'behaviors'));

		add_filter('the_title', array(&$this,'pagetitle'), 10, 2);

		// Ecart product text filters
		add_filter('ecart_product_name','convert_chars');
		add_filter('ecart_product_summary','convert_chars');

		add_filter('ecart_product_description', 'wptexturize');
		add_filter('ecart_product_description', 'convert_chars');
		add_filter('ecart_product_description', 'wpautop');
		add_filter('ecart_product_description', 'do_shortcode', 11); // AFTER wpautop()

		add_filter('ecart_product_spec', 'wptexturize');
		add_filter('ecart_product_spec', 'convert_chars');
		add_filter('ecart_product_spec', 'do_shortcode', 11); // AFTER wpautop()


		add_filter('ecart_order_lookup','ecartdiv');
		add_filter('ecart_order_confirmation','ecartdiv');
		add_filter('ecart_errors_page','ecartdiv');
		add_filter('ecart_cart_template','ecartdiv');
		add_filter('ecart_checkout_page','ecartdiv');
		add_filter('ecart_account_template','ecartdiv');
		add_filter('ecart_order_receipt','ecartdiv');
		add_filter('ecart_account_manager','ecartdiv');
		add_filter('ecart_account_vieworder','ecartdiv');

		add_filter('aioseop_canonical_url', array(&$this,'canonurls'));
		add_action('wp_enqueue_scripts', 'ecart_dependencies');

		$this->smartcategories();
		$this->searching();
		$this->account();
	}

	/**
	 * Identifies the currently loaded Ecart storefront page
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function pageid () {
		global $wp;
		if (empty($wp->query_vars) || !isset($wp->query_vars['pagename'])) return false;

		// Identify the current page
		foreach ($this->pages as &$page) {
			if ($page['uri'] == $wp->query_vars['pagename']) {
				$this->Page = $page; break;
			}
		}
	}

	/**
	 * Forces SSL on pages when required and available
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function security () {
		global $Ecart;
		if (is_ecart_secure() || !$Ecart->Gateways->secure) return;

		switch ($this->Page['name']) {
			case "checkout": ecart_redirect(ecarturl($_GET,get_query_var('ecart_proc'),true)); break;
			case "account":	 ecart_redirect(ecarturl($_GET,'account',true)); break;
		}
	}

	/**
	 * Adds nocache headers on sensitive account pages
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function account () {
		global $wp;
		if (isset($wp->query_vars['acct']))
			add_filter('wp_headers',array(&$this,'nocache'));

	}

	/**
	 * Adds nocache headers to WP page headers
	 *
	 * @since 1.1
	 *
	 * @param array $headers The current WP HTTP headers
	 * @return array Modified headers
	 **/
	function nocache ($headers) {
		$headers = array_merge($headers, wp_get_nocache_headers());
		return $headers;
	}

	/**
	 * Queues Ecart storefront javascript and styles as needed
	 * 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function behaviors () {
		global $Ecart;

		global $wp_query;
		$object = $wp_query->get_queried_object();

		if(is_ecart_secure()) {
			add_filter('option_siteurl', 'force_ssl');
			add_filter('option_home', 'force_ssl');
			add_filter('option_url', 'force_ssl');
			add_filter('option_wpurl', 'force_ssl');
			add_filter('option_stylesheet_url', 'force_ssl');
			add_filter('option_template_url', 'force_ssl');
			add_filter('script_loader_src', 'force_ssl');
		}

		// Determine which tag is getting used in the current post/page
		$tag = false;
		$tagregexp = join( '|', array_keys($this->shortcodes) );
		foreach ($wp_query->posts as $post) {
			if (preg_match('/\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\1\])?/',$post->post_content,$matches))
				$tag = $matches[1];
		}

		// Include stylesheets and javascript based on whether ecart shortcodes are used
		add_action('wp_print_styles',array(&$this, 'catalogcss'));

		// Replace the WordPress canonical link
		remove_action('wp_head','rel_canonical');

		add_action('wp_head', array(&$this, 'header'));
		add_action('wp_footer', array(&$this, 'footer'));
		wp_enqueue_style('ecart.catalog',ECART_ADMIN_URI.'/styles/catalog.css',array(),ECART_VERSION,'screen');
		wp_enqueue_style('ecart',ECART_TEMPLATES_URI.'/ecart.css',array(),ECART_VERSION,'screen');
		wp_enqueue_style('ecart.colorbox',ECART_ADMIN_URI.'/styles/colorbox.css',array(),ECART_VERSION,'screen');
		if (is_ecart_page('account') || (isset($wp->query_vars['ecart_proc']) && $wp->query_vars['ecart_proc'] == "sold"))
			wp_enqueue_style('ecart.printable',ECART_ADMIN_URI.'/styles/printable.css',array(),ECART_VERSION,'print');

		$loading = $this->Settings->get('script_loading');
		if (!$loading || $loading == "global" || $tag !== false) {
			ecart_enqueue_script("colorbox");
			ecart_enqueue_script("ecart");
			ecart_enqueue_script("catalog");
			ecart_enqueue_script("cart");
			if (is_ecart_page('catalog'))
				ecart_custom_script('catalog',"var pricetags = {};\n");

			add_action('wp_head', array(&$Ecart, 'settingsjs'));

		}

		if ($tag == "checkout")	ecart_enqueue_script('checkout');

	}

	/**
	 * Sets handlers for Ecart shortcodes
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function shortcodes () {

		$this->shortcodes = array();
		// Gateway page shortcodes
		$this->shortcodes['catalog'] = array(&$this,'catalog_page');
		$this->shortcodes['cart'] = array(&$this,'cart_page');
		$this->shortcodes['checkout'] = array(&$this,'checkout_page');
		$this->shortcodes['account'] = array(&$this,'account_page');

		// Additional shortcode functionality
		$this->shortcodes['product'] = array(&$this,'product_shortcode');
		$this->shortcodes['buynow'] = array(&$this,'buynow_shortcode');
		$this->shortcodes['category'] = array(&$this,'category_shortcode');

		foreach ($this->shortcodes as $name => &$callback)
			if ($this->Settings->get("maintenance") == "on" || !$this->Settings->available || $this->maintenance())
				add_shortcode($name,array(&$this,'maintenance_shortcode'));
			else add_shortcode($name,$callback);

	}

	/**
	 * Detects if maintenance mode is necessary
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function maintenance () {
		$Settings = &EcartSettings();
		$db_version = intval($Settings->get('db_version'));
		if ($db_version != DB::$version) return true;
		return false;
	}

	/**
	 * Modifies the WP page title to include product/category names (when available)
	 *
	 * @since 1.1
	 *
	 * @param string $title The current WP page title
	 * @param string $sep (optional) The page title separator to include between page titles
	 * @param string $placement (optional) The placement of the separator (defaults 'left')
	 * @return string The modified page title
	 **/
	function titles ($title,$sep="&mdash;",$placement="left") {
		global $wp;

		if (!isset($wp->query_vars['ecart_category'])
			&& !isset($wp->query_vars['ecart_tag'])
			&& !isset($wp->query_vars['ecart_product'])
			&& !isset($wp->query_vars['ecart_pid'])) return $title;
		if (empty($this->Product->name) && empty($this->Category->name)) return $title;

		$_ = array();
		if (!empty($title))					$_[] = $title;
		if (!empty($this->Category->name))	$_[] = $this->Category->name;
		if (!empty($this->Product->name))	$_[] = $this->Product->name;

		if ("right" == $placement) $_ = array_reverse($_);

		$_ = apply_filters('ecart_document_titles',$_);
		$sep = trim($sep);
		if (empty($sep)) $sep = "&mdash;";
		return join(" $sep ",$_);
	}

	/**
	 * Override the WP page title for the extra checkout process pages
	 *
	 * @since 1.1
	 *
	 * @param string $title The current WP page title
	 * @param int $post_id (optional) The post id
	 * @return string The modified title
	 **/
	function pagetitle ($title,$post_id=false) {
		if (!$post_id) return $title;
		global $wp;

		$pages = $this->Settings->get('pages');

		if (isset($wp->query_vars['ecart_proc']) &&
			$post_id == $pages['checkout']['id']) {

			switch(strtolower($wp->query_vars['ecart_proc'])) {
				case "thanks": $title = apply_filters('ecart_thanks_pagetitle',__('Thank You!','Ecart')); break;
				case "confirm-order": $title = apply_filters('ecart_confirmorder_pagetitle',__('Confirm Order','Ecart')); break;
			}
		}
		return $title;
	}

	/**
	 * Renders RSS feed link tags for category product feeds
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function feeds () {
		global $Ecart;
		if (empty($this->Category->name)):?>

<link rel='alternate' type="application/rss+xml" title="<?php htmlentities(bloginfo('name')); ?> New Products RSS Feed" href="<?php echo esc_attr(ecarturl(ECART_PRETTYURLS?'feed':array('ecart_lookup'=>'newproducts-rss'))); ?>" />
<?php else: ?>

<link rel='alternate' type="application/rss+xml" title="<?php esc_attr_e(bloginfo('name')); ?> <?php esc_attr_e($this->Category->name); ?> RSS Feed" href="<?php esc_attr_e($this->Category->tag('feed-url')); ?>" />
<?php
		endif;
	}

	/**
	 * Updates the WP search query with the Ecart search in progress
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function updatesearch () {
		global $wp_query;
		$wp_query->query_vars['s'] = esc_attr(stripslashes($this->search));
	}

	/**
	 * Adds 'keyword' and 'description' <meta> tags into the page markup
	 *
	 * The 'keyword' tag is a list of tags applied to a product.  No default 'keyword' meta
	 * is generated for categories, however, the 'ecart_meta_keywords' filter hook can be
	 * used to generate a custom list.
	 *
	 * The 'description' tag is generated from the product summary or category description.
	 * It can also be customized with the 'ecart_meta_description' filter hook.
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function metadata () {
		$keywords = false;
		$description = false;
		if (!empty($this->Product->id)) {
			if (empty($this->Product->tags)) $this->Product->load_data(array('tags'));
			foreach($this->Product->tags as $tag)
				$keywords .= (!empty($keywords))?", {$tag->name}":$tag->name;
			$description = $this->Product->summary;
		} elseif (!empty($this->Category->id)) {
			$description = $this->Category->description;
		}
		$keywords = esc_attr(apply_filters('ecart_meta_keywords',$keywords));
		$description = esc_attr(apply_filters('ecart_meta_description',$description));
		?>
		<?php if ($keywords): ?><meta name="keywords" content="<?php echo $keywords; ?>" />
		<?php endif; ?>
<?php if ($description): ?><meta name="description" content="<?php echo $description; ?>" />
		<?php endif;
	}

	/**
	 * Returns canonical product and category URLs
	 *
	 * @since 1.1
	 *
	 * @param string $url The current url
	 * @return string The canonical url
	 **/
	function canonurls ($url) {
		global $Ecart;

		// Catalog landing as site landing, use site home URL
		if (is_front_page() && isset($Ecart->Catalog) && $Ecart->Catalog->tag('is-landing','return=1'))
			return user_trailingslashit(get_bloginfo('home'));

		// Catalog landing page URL
		if (is_ecart_page('catalog') && $Ecart->Catalog->tag('is-landing','return=1'))
			return $Ecart->Catalog->tag('url','echo=0');

		// Specific product/category URLs
		if (!empty($Ecart->Product->slug)) return $Ecart->Product->tag('url','echo=0');
		if (!empty($Ecart->Category->slug)) return $Ecart->Category->tag('url','echo=0');
		return $url;
	}

	/**
	 * Registers available smart categories
	 *
	 * New smart categories can be added by creating a new smart category class
	 * in a custom plugin or the theme functions file.
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function smartcategories () {
		Ecart::add_smartcategory('CatalogProducts');
		Ecart::add_smartcategory('NewProducts');
		Ecart::add_smartcategory('FeaturedProducts');
		Ecart::add_smartcategory('OnSaleProducts');
		Ecart::add_smartcategory('BestsellerProducts');
		Ecart::add_smartcategory('SearchResults');
		Ecart::add_smartcategory('TagProducts');
		Ecart::add_smartcategory('RelatedProducts');
		Ecart::add_smartcategory('RandomProducts');

		do_action('ecart_register_smartcategories');
	}

	/**
	 * Includes a canonical reference <link> tag for the catalog page
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function header () {
		global $wp;
		$canonurl = $this->canonurls(false);
		if (is_ecart_page('catalog') && !empty($canonurl)): ?><link rel='canonical' href='<?php echo $canonurl ?>' /><?php
		endif;
	}

	/**
	 * Adds a dynamic style declaration for the category grid view
	 *
	 * Ties the presentation setting to the grid view category rendering
	 * in the storefront.
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function catalogcss () {
		$Settings = &EcartSettings();
		if (!isset($row_products)) $row_products = 3;
		$row_products = $Settings->get('row_products');
		$products_per_row = floor((100/$row_products));
?>
	<!-- Ecart dynamic catalog styles -->
	<style type="text/css">
	#ecart ul.products li.product { width: <?php echo $products_per_row; ?>%; } /* For grid view */
	</style>
	<!-- END Ecart dynamic catalog styles -->
<?php
	}

	/**
	 * Renders footer content and extra scripting as needed
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function footer () {
		$db = DB::get();
		global $wpdb;

		if (WP_DEBUG && current_user_can('manage_options')) {
			if (function_exists('memory_get_peak_usage'))
				$this->_debug->memory .= "End: ".number_format(memory_get_peak_usage(true)/1024/1024, 2, '.', ',') . " MB<br />";
			elseif (function_exists('memory_get_usage'))
				$this->_debug->memory .= "End: ".number_format(memory_get_usage(true)/1024/1024, 2, '.', ',') . " MB";

			add_storefrontjs("var memory_profile = '{$this->_debug->memory}',wpquerytotal = {$wpdb->num_queries},ecartquerytotal = ".count($db->queries).";",true);
		}

		$globals = false;
		if (isset($this->behaviors['global'])) {
			$globals = $this->behaviors['global'];
			unset($this->behaviors['global']);
		}

		$script = '';
		if (!empty($globals)) $script .= "\t".join("\n\t",$globals)."\n";
		if (!empty($this->behaviors)) {
			$script .= 'jQuery(window).ready(function(){ var $ = jqnc(); '."\n";
			$script .= "\t".join("\t\n",$this->behaviors)."\n";
			$script .= '});'."\n";
		}
		ecart_custom_script('catalog',$script);
	}

	/**
	 * Determines when to search the Ecart catalog instead of WP content
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function searching () {
		global $Ecart,$wp;

		$this->searching = false;

		if (!isset($_GET['s']) 								// No search query
			|| !isset($wp->query_vars['catalog']) 			// No catalog flag
			|| (isset($wp->query_vars['catalog']) 			// Catalog flag exists &
				&& $wp->query_vars['catalog'] == 'false'))	// explicitly turned off
					return false;							// ...not searching Ecart

		$this->search = $wp->query_vars['s'];
		$this->searching = true;
		unset($wp->query_vars['s']); // Not needed any longer
		$wp->query_vars['pagename'] = $this->pages['catalog']['uri'];
		$wp->query_vars['ecart_category'] = "search-results";
		add_action('wp_head', array(&$this, 'updatesearch'));

	}

	/**
	 * Parses catalog page requests
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function catalog () {
		global $Ecart,$wp;

		$options = array();

		add_filter('redirect_canonical', array(&$this,'canonical_home'));

		$type = "catalog";
		if (isset($wp->query_vars['ecart_category']) &&
			$category = urldecode($wp->query_vars['ecart_category'])) $type = "category";
		elseif (isset($wp->query_vars['ecart_pid']) &&
			$productid = $wp->query_vars['ecart_pid']) $type = "product";
		elseif (isset($wp->query_vars['ecart_product']) &&
			$productname = urldecode($wp->query_vars['ecart_product'])) $type = "product";

		if (isset($wp->query_vars['ecart_tag']) &&
			$tag = $wp->query_vars['ecart_tag']) {
			$type = "category";
			$category = "tag";
		}

		// If a search query is stored, and this request is a product or the
		// search results category repopulate the search box and set the
		// category for the breadcrumb
		if (!empty($this->search)
				&& ($type == "product"
				|| ($type == "category" && $category == "search-results"))) {
			add_action('wp_head', array(&$this, 'updatesearch'));
			$category = "search-results";
		} else $this->search = $this->searching = false;

		// If a search request is being made, set the type to category
		if ($this->searching) {
			if ($type != "product") $type = "category";
			$category = "search-results";
		}

		// Load a category/tag
		if (!empty($category) || !empty($tag)) {
			if (isset($this->search)) $options = array('search'=>$this->search);
			if (isset($tag)) $options = array('tag'=>$tag);

			// Split for encoding multi-byte slugs
			$slugs = explode("/",$category);
			$category = join("/",array_map('urlencode',$slugs));

			// Load the category
			$Ecart->Category = Catalog::load_category($category,$options);
			$this->breadcrumb = (isset($tag)?"tag/":"").$Ecart->Category->uri;

			if (!empty($this->searching)) {
				$Ecart->Category->load_products(array('load'=>array('images','prices')));
				if (count($Ecart->Category->products) == 1) {
					reset($Ecart->Category->products);
					$type = 'product';
					$BestBet = current($Ecart->Category->products);
					ecart_redirect($BestBet->tag('url',array('return'=>true)));
				}
			}
		}

		if (empty($category) && empty($tag) &&
			empty($productid) && empty($productname))
			$this->breadcrumb = "";

		// Category Filters
		if (!empty($Ecart->Category->slug)) {
			if (empty($this->browsing[$Ecart->Category->slug]))
				$this->browsing[$Ecart->Category->slug] = array();
			$CategoryFilters =& $this->browsing[$Ecart->Category->slug];

			// Add new filters
			if (isset($_GET['ecart_catfilters'])) {
				if (is_array($_GET['ecart_catfilters'])) {
					$CategoryFilters = array_filter(array_merge($CategoryFilters,$_GET['ecart_catfilters']));
					$CategoryFilters = stripslashes_deep($CategoryFilters);
					if (isset($wp->query_vars['paged'])) $wp->query_vars['paged'] = 1; // Force back to page 1
				} else unset($this->browsing[$Ecart->Category->slug]);
			}

		}

		// Catalog sort order setting
		if (isset($_GET['ecart_orderby']))
			$this->browsing['orderby'] = $_GET['ecart_orderby'];

		// Set the category context by following the breadcrumb
		if (empty($Ecart->Category->slug)) $Ecart->Category = Catalog::load_category($this->breadcrumb,$options);

		// No category context, use the CatalogProducts smart category
		if (empty($Ecart->Category->slug)) $Ecart->Category = Catalog::load_category('catalog',$options);

		// Find product by given ID
		if (!empty($productid) && empty($Ecart->Product->id))
			$Ecart->Product = new Product($productid);

		// Find product by product slug
		if (!empty($productname) && empty($Ecart->Product->id))
			$Ecart->Product = new Product(urlencode($productname),"slug");

		// Product must be published
		if ((!empty($Ecart->Product->id) && !$Ecart->Product->published()) || empty($Ecart->Product->id))
			$Ecart->Product = new Product(); // blank product displays "no product found" in storefront

		// No product found, try to load a page instead
		if ($type == "product" && !$Ecart->Product)
			$wp->query_vars['pagename'] = $wp->request;

		$Ecart->Catalog = new Catalog($type);

		if ($type == "category") $Ecart->Requested = $Ecart->Category;
		else $Ecart->Requested = $Ecart->Product;

		add_filter('wp_title', array(&$this, 'titles'),10,3);
		add_action('wp_head', array(&$this, 'metadata'));
		add_action('wp_head', array(&$this, 'feeds'));
	}

	/**
	 * Cancels canonical redirects when the catalog is Set as the front page
	 *
	 * Added for WordPress 3.0 compatibility {@see wp-includes/canonical.php line 111}
	 *
	 * @since 1.1
	 *
	 * @param string $redirect The redirected URL
	 * @return mixed False when the Ecart catalog is set as the front page
	 **/
	function canonical_home ($redirect) {
		$pages = $this->Settings->get('pages');
		if (!function_exists('home_url')) return $redirect;
		list($url,) = explode("?",$redirect);
		if ($url == home_url('/') && $pages['catalog']['id'] == get_option('page_on_front'))
			return false;
		// Cancel WP pagination redirects for Ecart category pages
		if ( get_query_var('ecart_category') && get_query_var('paged') > 0 )
			return false;
		return $redirect;
	}

	function catalog_page () {
		global $Ecart,$wp;
		if (ECART_DEBUG) new EcartError('Displaying catalog page request: '.$_SERVER['REQUEST_URI'],'ecart_catalog',ECART_DEBUG_ERR);

		$referrer = get_bloginfo('url')."/".$wp->request;
		if (!empty($wp->query_vars)) $referrer = add_query_arg($wp->query_vars,$referrer);
		$this->referrer = $referrer;

		ob_start();
		switch ($Ecart->Catalog->type) {
			case "product":
				if (file_exists(ECART_TEMPLATES."/product-{$Ecart->Product->id}.php"))
					include(ECART_TEMPLATES."/product-{$Ecart->Product->id}.php");
				else include(ECART_TEMPLATES."/product.php"); break;

			case "category":
				if (isset($Ecart->Category->slug) &&
					file_exists(ECART_TEMPLATES."/category-{$Ecart->Category->slug}.php"))
					include(ECART_TEMPLATES."/category-{$Ecart->Category->slug}.php");
				elseif (isset($Ecart->Category->id) &&
					file_exists(ECART_TEMPLATES."/category-{$Ecart->Category->id}.php"))
					include(ECART_TEMPLATES."/category-{$Ecart->Category->id}.php");
				else include(ECART_TEMPLATES."/category.php"); break;

			default: include(ECART_TEMPLATES."/catalog.php"); break;
		}
		$content = ob_get_contents();
		ob_end_clean();

		$classes = $Ecart->Catalog->type;
		if (!isset($_COOKIE['ecart_catalog_view'])) {
			// No cookie preference exists, use ecart default setting
			$view = $Ecart->Settings->get('default_catalog_view');
			if ($view == "list") $classes .= " list";
			if ($view == "grid") $classes .= " grid";
		} else {
			if ($_COOKIE['ecart_catalog_view'] == "list") $classes .= " list";
			if ($_COOKIE['ecart_catalog_view'] == "grid") $classes .= " grid";
		}

		return apply_filters('ecart_catalog','<div id="ecart" class="'.$classes.'">'.$content.'</div>');
	}

	/**
	 * Handles shopping cart requests
	 * 
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function cart () {
		global $Ecart;
		$Cart = $Ecart->Order->Cart;
		if (isset($_REQUEST['shopping']) && strtolower($_REQUEST['shopping']) == "reset") {
			$Ecart->Shopping->reset();
			ecart_redirect(ecarturl());
		}

		if (empty($_REQUEST['cart'])) return true;

		do_action('ecart_cart_request');

		if (isset($_REQUEST['ajax'])) {
			$Cart->totals();
			$Cart->ajax();
		}
		$redirect = false;
		if (isset($_REQUEST['redirect'])) $redirect = $_REQUEST['redirect'];
		switch ($redirect) {
			case "checkout": ecart_redirect(ecarturl(false,$redirect,$Ecart->Order->security())); break;
			default:
				if (!empty($_REQUEST['redirect']))
					ecart_safe_redirect($_REQUEST['redirect']);
				else ecart_redirect(ecarturl(false,'cart'));
		}
	}

	/**
	 * Displays the cart template
	 *
	 * Replaces the [cart] shortcode on the Cart page with
	 * the processed template contents.
	 *
	 * @since 1.1
	 *
	 * @param array $attrs Shortcode attributes
	 * @return string The cart template content
	 **/
	function cart_page ($attrs=array()) {
		global $Ecart;
		$Order = &EcartOrder();
		$Cart = $Order->Cart;

		ob_start();
		include(ECART_TEMPLATES."/cart.php");
		$content = ob_get_contents();
		ob_end_clean();

		return apply_filters('ecart_cart_template',$content);
	}

	/**
	 * Displays the appropriate checkout template
	 *
	 * Replaces the [checkout] shortcode on the Checkout page with
	 * the processed template contents.
	 *
	 * @since 1.1
	 *
	 * @param array $attrs Shortcode attributes
	 * @return string The processed template content
	 **/
	function checkout_page () {
		$Errors =& EcartErrors();
		$Order =& EcartOrder();
		$Cart =& $Order->Cart;
		$process = get_query_var('ecart_proc');

		do_action('ecart_init_checkout');
		switch ($process) {
			case "confirm-order":
				do_action('ecart_init_confirmation');
				$Order->validated = $Order->isvalid();
				$errors = "";
				if ($Errors->exist(ECART_STOCK_ERR)) {
					ob_start();
					include(ECART_TEMPLATES."/errors.php");
					$errors = ob_get_contents();
					ob_end_clean();
				}
				$content = $errors.$this->order_confirmation();
				break;
			case "thanks":
			case "receipt":
				$content = $this->thanks();
				break;
			default:
				ob_start();
				if ($Errors->exist(ECART_COMM_ERR)) include(ECART_TEMPLATES."/errors.php");
				$this->checkout = true;
				include(ECART_TEMPLATES."/checkout.php");
				$content = ob_get_contents();
				ob_end_clean();
		}

		return apply_filters('ecart_checkout_page',$content);
	}

	/**
	 * Displays the appropriate account page template
	 *
	 * Replaces the [account] shortcode on the Account page with
	 * the processed template contents.
	 *
	 * @since 1.1
	 *
	 * @param array $attrs Shortcode attributes
	 * @return string The cart template content
	 **/
	function account_page ($menuonly=false) {
		global $wp;
		$Order =& EcartOrder();
		$Customer =& $Order->Customer;

		if (isset($Customer->login) && $Customer->login) do_action('ecart_account_management');

		ob_start();
		if (isset($wp->query_vars['ecart_download'])) include(ECART_TEMPLATES."/errors.php");
		elseif ($Customer->login) include(ECART_TEMPLATES."/account.php");
		else include(ECART_TEMPLATES."/login.php");
		$content = ob_get_contents();
		ob_end_clean();

		return apply_filters('ecart_account_template',$content);

	}

	/**
	 * Renders the order confirmation template
	 *
	 * @since 1.1
	 *
	 * @return string The processed confirm.php template file
	 **/
	function order_confirmation () {
		global $Ecart;
		$Cart = $Ecart->Order->Cart;

		ob_start();
		include(ECART_TEMPLATES."/confirm.php");
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters('ecart_order_confirmation',$content);
	}

	/**
	 * Renders the thanks template
	 *
	 * @since 1.1
	 *
	 * @return string The processed thanks.php template file
	 **/
	function thanks ($template="thanks.php") {
		global $Ecart;
		$Purchase = $Ecart->Purchase;

		ob_start();
		include(ECART_TEMPLATES."/$template");
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters('ecart_thanks',$content);
	}

	/**
	 * Renders the errors template
	 *
	 * @since 1.1
	 *
	 * @return string The processed errors.php template file
	 **/
	function error_page ($template="errors.php") {
		global $Ecart;
		$Cart = $Ecart->Cart;

		ob_start();
		include(ECART_TEMPLATES."/$template");
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters('ecart_errors_page',$content);
	}

	/**
	 * Handles rendering the [product] shortcode
	 *
	 * @since 1.1
	 *
	 * @param array $attrs The parsed shortcode attributes
	 * @return string The processed content
	 **/
	function product_shortcode ($atts) {
		global $Ecart;

		if (isset($atts['name'])) {
			$Ecart->Product = new Product($atts['name'],'name');
		} elseif (isset($atts['slug'])) {
			$Ecart->Product = new Product($atts['slug'],'slug');
		} elseif (isset($atts['id'])) {
			$Ecart->Product = new Product($atts['id']);
		} else return "";

		return apply_filters('ecart_product_shortcode',$Ecart->Catalog->tag('product',$atts).'');
	}

	/**
	 * Handles rendering the [category] shortcode
	 *
	 * @since 1.1
	 *
	 * @param array $attrs The parsed shortcode attributes
	 * @return string The processed content
	 **/
	function category_shortcode ($atts) {
		global $Ecart;

		$tag = 'category';
		if (isset($atts['name'])) {
			$Ecart->Category = new Category($atts['name'],'name');
			unset($atts['name']);
		} elseif (isset($atts['slug'])) {
			foreach ($Ecart->SmartCategories as $SmartCategory) {
				$SmartCategory_slug = get_class_property($SmartCategory,'_slug');
				if ($atts['slug'] == $SmartCategory_slug) {
					$tag = "$SmartCategory_slug-products";
					if ($tag == "search-results-products") $tag = "search-products";
					unset($atts['slug']);
				}
			}
		} elseif (isset($atts['id'])) {
			$Ecart->Category = new Category($atts['id']);
			unset($atts['id']);
		} else return "";

		return apply_filters('ecart_category_shortcode',$Ecart->Catalog->tag($tag,$atts).'');

	}


	/**
	 * Handles rendering the maintenance message in place of all shortcodes
	 *
	 * @since 1.1
	 *
	 * @param array $attrs The parsed shortcode attributes
	 * @return string The processed content
	 **/
	function maintenance_shortcode ($atts) {
		if (file_exists(ECART_TEMPLATES."/maintenance.php")) {
			ob_start();
			include(ECART_TEMPLATES."/maintenance.php");
			$content = ob_get_contents();
			ob_end_clean();
		} else $content = '<div id="ecart" class="update"><p>'.__("The store is currently down for maintenance.  We'll be back soon!","Ecart").'</p><div class="clear"></div></div>';

		return $content;
	}

	/**
	 * Handles rendering the [buynow] shortcode
	 *
	 * @since 1.1
	 *
	 * @param array $attrs The parsed shortcode attributes
	 * @return string The processed content
	 **/
	function buynow_shortcode ($atts) {
		global $Ecart;

		if (empty($Ecart->Product->id)) {
			if (isset($atts['name'])) {
				$Ecart->Product = new Product($atts['name'],'name');
			} elseif (isset($atts['slug'])) {
				$Ecart->Product = new Product($atts['slug'],'slug');
			} elseif (isset($atts['id'])) {
				$Ecart->Product = new Product($atts['id']);
			} else return "";
		}
		if (empty($Ecart->Product->id)) return "";

		ob_start();
		?>
		<form action="<?php ecart('cart','url'); ?>" method="post" class="ecart product">
			<input type="hidden" name="redirect" value="checkout" />
			<?php if (isset($atts['variations'])): $variations = empty($atts['variations'])?'mode=multiple&label=true&defaults='.__('Select an option','Ecart').'&before_menu=<li>&after_menu=</li>':$atts['variations']; ?>
				<?php if(ecart('product','has-variations')): ?>
				<ul class="variations">
					<?php ecart('product','variations',$variations); ?>
				</ul>
				<?php endif; ?>
			<?php endif; ?>
			<?php if (isset($atts['addons'])): $addons = empty($atts['addons'])?'mode=menu&label=true&defaults='.__('Select an add-on','Ecart').'&before_menu=<li>&after_menu=</li>':$atts['addons']; ?>
				<?php if(ecart('product','has-addons')): ?>
					<ul class="addons">
						<?php ecart('product','addons','mode=menu&label=true&defaults='.__('Select an add-on','Ecart').'&before_menu=<li>&after_menu=</li>'); ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
			<p><?php if (isset($atts['quantity'])): $quantity = (empty($atts['quantity']))?'class=selectall&input=menu':$atts['quantity']; ?>
				<?php ecart('product','quantity',$quantity); ?>
			<?php endif; ?>
			<?php $button = empty($atts['button'])?'label='.__('Buy Now','Ecart'):$atts['button']; ?>
			<?php ecart('product','addtocart',$button); ?></p>
		</form>
		<?php
		$markup = ob_get_contents();
		ob_end_clean();

		return $markup;
	}

} // END class Storefront

?>