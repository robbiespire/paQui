<?php
/**
 * Error.php
 * Error management system for Ecart
 *
 * @version 1.1
 * @package ecart
 * @subpackage errors
 **/

define('ECART_ERR',1);			// Ecarter visible general Ecart/shopping errors
define('ECART_TRXN_ERR',2);		// Transaction errors (third-party service errors)
define('ECART_AUTH_ERR',4);		// Authorization errors (login, credential problems)
define('ECART_COMM_ERR',8);		// Communication errors (connectivity)
define('ECART_STOCK_ERR',16);	// Inventory-related warnings (low stock, out-of-stock)
define('ECART_ADDON_ERR',32);	// Ecart module errors (bad descriptors, core version requriements)
define('ECART_ADMIN_ERR',64);	// Admin errors (for logging)
define('ECART_DB_ERR',128);		// DB errors (for logging)
define('ECART_PHP_ERR',256);	// PHP errors (for logging)
define('ECART_ALL_ERR',1024);	// All errors (for logging)
define('ECART_DEBUG_ERR',2048);	// Debug-only (for logging)

/**
 * EcartErrors class
 *
 * The error message management class that allows other
 * systems to subscribe to notifications when errors are
 * triggered.
 *
 * @since 1.0
 * @package ecart
 * @subpackage errors
 **/
class EcartErrors {

	var $errors = array();				// Error message registry
	var $notifications;					// Notification subscription registry
	var $reporting = ECART_ALL_ERR;		// level of reporting

	/**
	 * Setup error system and PHP error capture
	 *
	 * @since 1.0
	 *
	 * @return void
	 **/
	function __construct ($level = ECART_ALL_ERR) {
		Ecart_buyObject::store('errors',$this->errors);

		if (defined('WP_DEBUG') && WP_DEBUG) $this->reporting = ECART_DEBUG_ERR;
		if ($level > $this->reporting) $this->reporting = $level;

		$this->notifications = new CallbackSubscription();

		$types = E_ALL ^ E_NOTICE;
		if (defined('WP_DEBUG') && WP_DEBUG) $types = E_ALL;
		// Handle PHP errors
		if ($this->reporting >= ECART_PHP_ERR)
			set_error_handler(array($this,'phperror'),$types);
	}

	/**
	 * Adds a EcartError to the registry
	 *
	 * @since 1.1
	 *
	 * @param EcartError $EcartError The EcartError object to add
	 * @return void
	 **/
	function add ($EcartError) {
		if (isset($EcartError->code)) $this->errors[$EcartError->code] = $EcartError;
		else $this->errors[] = $EcartError;
		$this->notifications->send($EcartError);
	}

	/**
	 * Gets all errors up to a specified error level
	 *
	 * @since 1.1
	 *
	 * @param int $level The maximum error level
	 * @return array A list of errors
	 **/
	function get ($level=ECART_DEBUG_ERR) {
		$errors = array();
		foreach ($this->errors as &$error)
			if ($error->level <= $level) $errors[] = &$error;
		return $errors;
	}

	/**
	 * Gets all errors of a specific error level
	 *
	 * @since 1.1
	 *
	 * @param int $level The level of errors to retrieve
	 * @return array A list of errors
	 **/
	function level ($level=ECART_ALL_ERR) {
		$errors = array();
		foreach ($this->errors as &$error)
			if ($error->level == $level) $errors[] = &$error;
		return $errors;
	}

	/**
	 * Gets an error message with a specific error code
	 *
	 * @since 1.1
	 *
	 * @param string $code The name of the error code
	 * @return EcartError The error object
	 **/
	function code ($code) {
		if (!empty($code) && isset($this->errors[$code]))
			return $this->errors[$code];
	}

	/**
	 * Gets all errors from a specified source (object)
	 *
	 * @since 1.1
	 *
	 * @param string $source The source object of errors
	 * @return array A list of errors
	 **/
	function source ($source) {
		if (empty($source)) return array();
		$errors = array();
		foreach ($this->errors as &$error)
			if ($error->source == $source) $errors[] = &$error;
		return $errors;
	}

	/**
	 * Determines if any errors exist up to the specified error level
	 *
	 * @since 1.0
	 *
	 * @param int $level The maximum level to look for errors in
	 * @return void Description...
	 **/
	function exist ($level=ECART_DEBUG_ERR) {
		$errors = array();
		foreach ($this->errors as &$error)
			if ($error->level <= $level) $errors[] = &$error;
		return (count($errors) > 0);
	}

	/**
	 * Removes an error from the registry
	 *
	 * @since 1.1
	 *
	 * @param EcartError $error The EcartError object to remove
	 * @return boolean True when removed, false if removal failed
	 **/
	function remove ($error) {
		if (!isset($this->errors[$error->code])) return false;
		unset($this->errors[$error->code]);
		return true;
	}

	/**
	 * Removes all errors from the error registry
	 *
	 * @since 1.0
	 *
	 * @return void
	 **/
	function reset () {
		$this->errors = array();
	}

	/**
	 * Reports PHP generated errors to the Ecart error system
	 *
	 * @since 1.0
	 *
	 * @param int $number The error type
	 * @param string $message The PHP error message
	 * @param string $file The file the error occurred in
	 * @param int $line The line number the error occurred at in the file
	 * @return void
	 **/
	function phperror ($number, $message, $file, $line) {
		if (strpos($file,ECART_PATH) !== false)
			new EcartError($message,'php_error',ECART_PHP_ERR,
				array('file'=>$file,'line'=>$line,'phperror'=>$number));
	}

	/**
	 * Provides functionality for the ecart('error') tags
	 *
	 * Support for triggering errors through the Template API.
	 *
	 * @since 1.0
	 *
	 * @param string $property The error property for the tag
	 * @param array $options Tag options
	 * @return void
	 **/
	function tag ($property,$options=array()) {
		global $Ecart;
		if (empty($options)) return false;
		switch ($property) {
			case "trxn": new EcartError(key($options),'template_error',ECART_TRXN_ERR); break;
			case "auth": new EcartError(key($options),'template_error',ECART_AUTH_ERR); break;
			case "addon": new EcartError(key($options),'template_error',ECART_ADDON_ERR); break;
			case "comm": new EcartError(key($options),'template_error',ECART_COMM_ERR); break;
			case "stock": new EcartError(key($options),'template_error',ECART_STOCK_ERR); break;
			case "admin": new EcartError(key($options),'template_error',ECART_ADMIN_ERR); break;
			case "db": new EcartError(key($options),'template_error',ECART_DB_ERR); break;
			case "debug": new EcartError(key($options),'template_error',ECART_DEBUG_ERR); break;
			default: new EcartError(key($options),'template_error',ECART_ERR); break;
		}
	}

}

/**
 * EcartError class
 *
 * Triggers an error that is handled by the Ecart error system.
 *
 * @since 1.0
 * @package ecart
 * @subpackage errors
 **/
class EcartError {

	var $code;
	var $source;
	var $messages;
	var $level;
	var $data = array();

	/**
	 * Creates and registers a new error
	 *
	 * @since 1.1
	 *
	 * @return void Description...
	 **/
	function EcartError($message='',$code='',$level=ECART_ERR,$data='') {
		$Errors = &EcartErrors();

		if (!is_a($Errors,'EcartErrors')) return;
		if ($level > $Errors->reporting) return;
		if (empty($message)) return;

		$php = array(
			1		=> 'ERROR',
			2		=> 'WARNING',
			4		=> 'PARSE ERROR',
			8		=> 'NOTICE',
			16		=> 'CORE ERROR',
			32		=> 'CORE WARNING',
			64		=> 'COMPILE ERROR',
			128		=> 'COMPILE WARNING',
			256		=> 'USER ERROR',
			512		=> 'USER WARNING',
			1024	=> 'USER NOTICE',
			2048	=> 'STRICT NOTICE',
			4096 	=> 'RECOVERABLE ERROR'
		);
		$debug = debug_backtrace();

		$this->code = $code;
		$this->messages[] = $message;
		$this->level = $level;
		$this->data = $data;
		$this->debug = $debug[1];

		// Handle template errors
		if (isset($this->debug['class']) && $this->debug['class'] == "EcartErrors")
			$this->debug = $debug[2];

		if (isset($data['file'])) $this->debug['file'] = $data['file'];
		if (isset($data['line'])) $this->debug['line'] = $data['line'];
		unset($this->debug['object'],$this->debug['args']);

		$this->source = "Ecart";
		if (isset($this->debug['class'])) $this->source = $this->debug['class'];
		if (isset($this->data['phperror']) && isset($php[$this->data['phperror']]))
			$this->source = "PHP ".$php[$this->data['phperror']];

		$Errors = &EcartErrors();
		if (!empty($Errors)) $Errors->add($this);
	}

	/**
	 * Prevent excess data from being stored in the session
	 *
	 * @since 1.1
	 *
	 * @return void
	 **/
	function __sleep () {
		return array('code','source','messages','level');
	}

	/**
	 * Tests if the error message is blank
	 *	 
	 * @since 1.1
	 *
	 * @return boolean True for blank error messages
	 **/
	function blank () {
		return (join('',$this->messages) == "");
	}

	/**
	 * Displays messages registered to a specific error code
	 *	 
	 * @since 1.1
	 *
	 * @param boolean $remove (optional) (Default true) Removes the error after retrieving it
	 * @param boolean $source Prefix the error with the source object where the error was triggered
	 * @param string $delimiter The delimeter used to join multiple error messages
	 * @return string A collection of concatenated error messages
	 **/
	function message ($remove=false,$source=false,$delimiter="\n") {
		$string = "";
		// Show source if debug is on, or not a general error message
		if (((defined('WP_DEBUG') && WP_DEBUG) || $this->level > ECART_ERR) &&
			!empty($this->source) && $source) $string .= "$this->source: ";
		$string .= join($delimiter,$this->messages);
		if ($remove) {
			$Errors = &EcartErrors();
			if (!empty($Errors->errors)) $Errors->remove($this);
		}
		return $string;
	}

}

/**
 * EcartErrorLogging class
 *
 * Subscribes to error notifications in order to log any errors
 * generated.
 *
 * @since 1.0
 * @package ecart
 * @subpackage errors
 **/
class EcartErrorLogging {
	var $dir;
	var $file = "ecart_debug.log";
	var $logfile;
	var $log;
	var $loglevel = 0;

	/**
	 * Setup for error logging
	 * 
	 * @since 1.0
	 *
	 * @return void
	 **/
	function __construct ($loglevel=0) {
		$this->loglevel = $loglevel;
		$this->dir = defined('ECART_LOG_PATH') ? ECART_LOG_PATH : sys_get_temp_dir();
		$this->dir = sanitize_path($this->dir); // Windows path sanitiation

		$siteurl = parse_url(get_bloginfo('siteurl'));
		$sub = (!empty($path)?"_".sanitize_title_with_dashes($path):'');
		$this->logfile = trailingslashit($this->dir).$siteurl['host'].$sub."_".$this->file;

		$Errors = &EcartErrors();
		$Errors->notifications->subscribe($this,'log');
	}

	/**
	 * Logs an error to the error log file
	 * 
	 * @since 1.0
	 *
	 * @param EcartError $error The error object to log
	 * @return void
	 **/
	function log ($error) {
		if ($error->level > $this->loglevel) return;
		$debug = "";
		if (isset($error->debug['file'])) $debug = " [".basename($error->debug['file']).", line ".$error->debug['line']."]";
		$message = date("Y-m-d H:i:s",mktime())." - ".$error->message(false,true).$debug."\n";
		if (($this->log = @fopen($this->logfile,'at')) !== false) {
			fwrite($this->log,$message);
			fclose($this->log);
		} else error_log($message);
	}

	/**
	 * Empties the error log file
	 *
	 * @since 1.0
	 *
	 * @return void
	 **/
	function reset () {
		$this->log = fopen($this->logfile,'w');
		fwrite($this->log,'');
		fclose($this->log);
	}

	/**
	 * Gets the end of the log file
	 *
	 * @since 1.0
	 *
	 * @param int $lines The number of lines to get from the end of the log file
	 * @return array List of log file lines
	 **/
	function tail($lines=100) {
		if (!file_exists($this->logfile)) return;
		$f = fopen($this->logfile, "r");
		$c = $lines;
		$pos = -2;
		$beginning = false;
		$text = array();
		while ($c > 0) {
			$t = "";
			while ($t != "\n") {
				if(fseek($f, $pos, SEEK_END) == -1) { $beginning = true; break; }
				$t = fgetc($f);
				$pos--;
			}
			$c--;
			if($beginning) rewind($f);
			$text[$lines-$c-1] = fgets($f);
			if($beginning) break;
		}
		fclose($f);
		return array_reverse($text);
	}

}

/**
 * EcartErrorNotification class
 *
 * Sends error notification emails when errors are triggered
 * to specified recipients
 *
 * @since 1.0
 * @package ecart
 * @subpackage errors
 **/
class EcartErrorNotification {

	var $recipients;	// Recipient addresses to send to
	var $types=0;		// Error types to send

	/**
	 * Relays triggered errors to email messages
	 *
	 * @since 1.0
	 *
	 * @param string $recipients List of email addresses
	 * @param array $types The types of errors to report
	 * @return void
	 **/
	function __construct ($recipients='',$types=array()) {
		if (empty($recipients)) return;
		$this->recipients = $recipients;
		foreach ((array)$types as $type) $this->types += $type;
		$Errors = &EcartErrors();
		$Errors->notifications->subscribe($this,'notify');
	}

	/**
	 * Generates and sends an email of an error to the recipient list
	 *
	 * @since 1.0
	 *
	 * @param EcartError $error The error object
	 * @return void
	 **/
	function notify ($error) {
		if (!($error->level & $this->types)) return;
		$url = parse_url(get_bloginfo('url'));
		$_ = array();
		$_[] = 'From: "'.get_bloginfo('sitename').'" <ecart@'.$url['host'].'>';
		$_[] = 'To: '.$this->recipients;
		$_[] = 'Subject: '.__('Ecart Notification','Ecart');
		$_[] = '';
		$_[] = __('This is an automated message notification generated when the Ecart installation at '.get_bloginfo('url').' encountered the following:','Ecart');
		$_[] = '';
		$_[] = $error->message();
		$_[] = '';
		if (isset($error->debug['file']) && defined('WP_DEBUG'))
			$_[] = 'DEBUG: '.basename($error->debug['file']).', line '.$error->debug['line'].'';

		ecart_email(join("\r\n",$_));
	}

}

class CallbackSubscription {

	var $subscribers = array();

	function subscribe ($target,$method) {
		if (!isset($this->subscribers[get_class($target)]))
			$this->subscribers[get_class($target)] = array(&$target,$method);
	}

	function send () {
		$args = func_get_args();
		foreach ($this->subscribers as $callback) {
			call_user_func_array($callback,$args);
		}
	}

}

/**
 * Helper to access the error system
 *
 * @since 1.0
 *
 * @return void Description...
 **/
function &EcartErrors () {
	global $Ecart;
	return $Ecart->Errors;
}

/**
 * Detects EcartError objects
 *
 * @since 1.0
 *
 * @param object $e The object to test
 * @return boolean True if the object is a EcartError
 **/
function is_ecarterror ($e) {
	return (get_class($e) == "EcartError");
}

?>