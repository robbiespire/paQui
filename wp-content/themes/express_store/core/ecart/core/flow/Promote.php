<?php
/**
 * Promote
 *
 * Flow controller for promotion management interfaces
 * 
 * @version 1.0
 * @package ecart
 * @subpackage promotions
 **/

/**
 * Promote
 *
 * @package promotions
 **/
class Promote extends AdminController {
	var $Notice = false;

	/**
	 * Promote constructor
	 *
	 * @return void	 
	 **/
	function __construct () {
		parent::__construct();
		if (!empty($_GET['id'])) {
			wp_enqueue_script('postbox');
			ecart_enqueue_script('colorbox');
			ecart_enqueue_script('calendar');
			do_action('ecart_promo_editor_scripts');
			add_action('admin_head',array(&$this,'layout'));
		} else add_action('admin_print_scripts',array(&$this,'columns'));
		do_action('ecart_promo_admin_scripts');
	}

	/**
	 * Parses admin requests to determine which interface to render
	 *
	 * @return void	 
	 **/
	function admin () {
		global $Ecart;
		if (!empty($_GET['id'])) $this->editor();
		else $this->promotions();
	}

	/**
	 * Interface processor for the promotions list manager
	 *	 
	 * @return void
	 **/
	function promotions () {
		global $Ecart;
		$db = DB::get();

		if ( !(is_ecart_userlevel() || current_user_can('ecart_promotions')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		require_once("{$Ecart->path}/core/model/Promotion.php");

		$defaults = array(
			'page' => false,
			'deleting' => false,
			'delete' => false,
			'pagenum' => 1,
			'per_page' => 20,
			's' => ''
			);

		$args = array_merge($defaults,$_GET);
		extract($args,EXTR_SKIP);

		if ($page == "ecart-promotions"
				&& !empty($deleting)
				&& !empty($delete)
				&& is_array($delete)) {
			foreach($delete as $deletion) {
				$Promotion = new Promotion($deletion);
				$Promotion->delete();
			}
		}

		if (!empty($_POST['save'])) {
			check_admin_referer('ecart-save-promotion');

			if ($_POST['id'] != "new") {
				$Promotion = new Promotion($_POST['id']);
			} else $Promotion = new Promotion();

			if (!empty($_POST['starts']['month']) && !empty($_POST['starts']['date']) && !empty($_POST['starts']['year']))
				$_POST['starts'] = mktime(0,0,0,$_POST['starts']['month'],$_POST['starts']['date'],$_POST['starts']['year']);
			else $_POST['starts'] = 1;

			if (!empty($_POST['ends']['month']) && !empty($_POST['ends']['date']) && !empty($_POST['ends']['year']))
				$_POST['ends'] = mktime(23,59,59,$_POST['ends']['month'],$_POST['ends']['date'],$_POST['ends']['year']);
			else $_POST['ends'] = 1;
			if (isset($_POST['rules'])) $_POST['rules'] = stripslashes_deep($_POST['rules']);

			$Promotion->updates($_POST);
			$Promotion->save();

			do_action_ref_array('ecart_promo_saved',array(&$Promotion));

			$Promotion->reset_discounts();
			if ($Promotion->target == "Catalog")
				$Promotion->build_discounts();

			// Force reload of the session promotions to include any updates
			$Ecart->Promotions->reload();

		}

		$pagenum = absint( $pagenum );
		if ( empty($pagenum) )
			$pagenum = 1;
		if( !$per_page || $per_page < 0 )
			$per_page = 20;
		$start = ($per_page * ($pagenum-1));


		$where = "";
		if (!empty($s)) $where = "WHERE name LIKE '%$s%'";

		$table = DatabaseObject::tablename(Promotion::$table);
		$promocount = $db->query("SELECT count(*) as total FROM $table $where");
		$Promotions = $db->query("SELECT * FROM $table $where",AS_ARRAY);

		$status = array(
			'enabled' => __('Enabled','Ecart'),
			'disabled' => __('Disabled','Ecart')
		);

		$num_pages = ceil($promocount->total / $per_page);
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'total' => $num_pages,
			'current' => $pagenum
		));

		include("{$Ecart->path}/core/ui/promotions/promotions.php");
	}

	/**
	 * Registers the column headers for the promotions list interface
	 *	 
	 * @return void
	 **/
	function columns () {
		register_column_headers('ecart_page_ecart-promotions', array(
			'cb'=>'<input type="checkbox" />',
			'name'=>__('Name','Ecart'),
			'discount'=>__('Discount','Ecart'),
			'applied'=>__('Applied To','Ecart'),
			'eff'=>__('Status','Ecart'))
		);
	}

	/**
	 * Generates the layout for the promotion editor
	 *	 
	 * @return void
	 **/
	function layout () {
		global $Ecart;
		$Admin =& $Ecart->Flow->Admin;
		include(ECART_PATH."/core/ui/promotions/ui.php");
	}

	/**
	 * Interface processor for the promotion editor
	 *
	 *
	 *	 
	 * @return void
	 **/
	function editor () {
		global $Ecart;

		if ( !(is_ecart_userlevel() || current_user_can('ecart_promotions')) )
			wp_die(__('You do not have sufficient permissions to access this page.'));

		require_once(ECART_PATH."/core/model/Promotion.php");

		if ($_GET['id'] != "new") {
			$Promotion = new Promotion($_GET['id']);
		} else $Promotion = new Promotion();

		include(ECART_PATH."/core/ui/promotions/editor.php");
	}


} // end Promote class

?>