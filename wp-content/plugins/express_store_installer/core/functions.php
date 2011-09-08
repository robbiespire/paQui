<?php
/**
 * functions.php
 * A library of global utility functions for Ecart
 *
 * @version 1.0
 * @package ecart
 **/

/**
 * Converts timestamps to formatted localized date/time strings
 *
 * @since 1.0
 *
 * @param string $format A date() format string
 * @param int $timestamp (optional) The timestamp to be formatted (defaults to current timestamp)
 * @return string The formatted localized date/time
 **/
function _d($format,$timestamp=false) {
	$tokens = array(
		'D' => array('Mon' => __('Mon','Ecart'),'Tue' => __('Tue','Ecart'),
					'Wed' => __('Wed','Ecart'),'Thu' => __('Thu','Ecart'),
					'Fri' => __('Fri','Ecart'),'Sat' => __('Sat','Ecart'),
					'Sun' => __('Sun','Ecart')),
		'l' => array('Monday' => __('Monday','Ecart'),'Tuesday' => __('Tuesday','Ecart'),
					'Wednesday' => __('Wednesday','Ecart'),'Thursday' => __('Thursday','Ecart'),
					'Friday' => __('Friday','Ecart'),'Saturday' => __('Saturday','Ecart'),
					'Sunday' => __('Sunday','Ecart')),
		'F' => array('January' => __('January','Ecart'),'February' => __('February','Ecart'),
					'March' => __('March','Ecart'),'April' => __('April','Ecart'),
					'May' => __('May','Ecart'),'June' => __('June','Ecart'),
					'July' => __('July','Ecart'),'August' => __('August','Ecart'),
					'September' => __('September','Ecart'),'October' => __('October','Ecart'),
					'November' => __('November','Ecart'),'December' => __('December','Ecart')),
		'M' => array('Jan' => __('Jan','Ecart'),'Feb' => __('Feb','Ecart'),
					'Mar' => __('Mar','Ecart'),'Apr' => __('Apr','Ecart'),
					'May' => __('May','Ecart'),'Jun' => __('Jun','Ecart'),
					'Jul' => __('Jul','Ecart'),'Aug' => __('Aug','Ecart'),
					'Sep' => __('Sep','Ecart'),'Oct' => __('Oct','Ecart'),
					'Nov' => __('Nov','Ecart'),'Dec' => __('Dec','Ecart'))
	);

	if (!$timestamp) $date = date($format);
	else $date = date($format,$timestamp);

	foreach ($tokens as $token => $strings) {
		if ($pos = strpos($format,$token) === false) continue;
		$string = (!$timestamp)?date($token):date($token,$timestamp);
		$date = str_replace($string,$strings[$string],$date);
	}
	return $date;
}

/**
 * JavaScript encodes translation strings
 *
 * @since 1.1.7
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return void
 **/
function _jse ( $text, $domain = 'default' ) {
	echo json_encode(translate( $text, $domain ));
}

/**
 * Generates a representation of the current state of an object structure
 *
 * @since 1.0
 *
 * @param object $object The object to display
 * @return string The object structure
 **/
function _object_r ($object) {
	global $Ecart;
	ob_start();
	print_r($object);
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

/**
 * Appends a string to the end of URL as a query string
 *
 * @since 1.1
 *
 * @param string $string The string to add
 * @param string $url The url to append to
 * @return string
 **/
function add_query_string ($string,$url) {
	if(strpos($url,'?') !== false) return "$url&$string";
	else return "$url?$string";
}

/**
 * Adds JavaScript to be included in the footer on ecarting pages
 *
 * @since 1.1
 *
 * @param string $script JavaScript fragment
 * @param boolean $global (optional) Include the script in the global namespace
 * @return void
 **/
function add_storefrontjs ($script,$global=false) {
	$Storefront =& EcartStorefront();
	if ($Storefront === false) return;
	if ($global) {
		if (!isset($Storefront->behaviors['global'])) $Storefront->behaviors['global'] = array();
		$Storefront->behaviors['global'][] = trim($script);
	} else $Storefront->behaviors[] = $script;
}

/**
 * Automatically generates a list of number ranges distributed across a number set
 *
 * @since 1.0
 *
 * @param int $avg Mean average number in the distribution
 * @param int $max The max number in the distribution
 * @param int $min The minimum in the distribution
 * @return array A list of number ranges
 **/
function auto_ranges ($avg,$max,$min) {
	$ranges = array();
	if ($avg == 0 || $max == 0) return $ranges;
	$power = floor(log10($avg));
	$scale = pow(10,$power);
	$median = round($avg/$scale)*$scale;
	$range = $max-$min;

	if ($range == 0) return $ranges;
	$steps = floor($range/$scale);
	if ($steps > 7) $steps = 7;

	elseif ($steps < 2) {
		$scale = $scale/2;
		$steps = ceil($range/$scale);
		if ($steps > 7) $steps = 7;
		elseif ($steps < 2) $steps = 2;
	}

	$base = max($median-($scale*floor(($steps-1)/2)),$scale);

	for ($i = 0; $i < $steps; $i++) {
		$range = array("min" => 0,"max" => 0);
		if ($i == 0) $range['max'] = $base;
		else if ($i+1 >= $steps) $range['min'] = $base;
		else $range = array("min" => $base, "max" => $base+$scale);
		$ranges[] = $range;
		if ($i > 0) $base += $scale;
	}
	return $ranges;
}

/**
 * Converts weight units from base setting to needed unit value
 *
 * @since 1.1
 * @version 1.1
 *
 * @param float $value The value that needs converted
 * @param string $unit The unit that we are converting to
 * @param string $from (optional) The unit that we are converting from - defaults to system settings
 * @return float|boolean The converted value, false on error
 **/
function convert_unit ($value = 0, $unit, $from=false) {
	if ($unit == $from || $value == 0) return $value;

	if (!$from) {
		// If no originating unit specified, use correlating system preferences
		$Settings =& EcartSettings();
		$defaults = array(
			'mass' => $Settings->get('weight_unit'),
			'dimension' => $Settings->get('dimension_unit')
		);
	}

	// Conversion table to International System of Units (SI)
	$table = array(
		'mass' => array(		// SI base unit "grams"
			'lb' => 453.59237, 'oz' => 28.349523125, 'g' => 1, 'kg' => 1000
		),
		'dimension' => array(	// SI base unit "meters"
			'ft' => 0.3048, 'in' => 0.0254, 'mm' => 0.001, 'cm' => 0.01, 'm' => 1
		)
	);

	$table = apply_filters('ecart_unit_conversion_table',$table);

	// Determine which chart to use
	foreach ($table as $attr => $c) {
		if (isset($c[$unit])) { $chart = $attr; $from = $defaults[$chart]; break; }
	}

	if ($unit == $from) return $value;

	$siv = $value * $table[$chart][$from];	// Convert to SI unit value
	return $siv/$table[$chart][$unit];		// Return target units
}

/**
 * Copies the builtin template files to the active WordPress theme
 *
 * Handles copying the builting template files to the ecart/ directory of
 * the currently active WordPress theme.  Strips out the header comment
 * block which includes a warning about editing the builtin templates.
 *
 * @since 1.0
 *
 * @param string $src The source directory for the builtin template files
 * @param string $target The target directory in the active theme
 * @return void
 **/
function copy_ecart_templates ($src,$target) {
	$builtin = array_filter(scandir($src),"filter_dotfiles");
	foreach ($builtin as $template) {
		$target_file = $target.'/'.$template;
		if (!file_exists($target_file)) {
			$src_file = file_get_contents($src.'/'.$template);
			$file = fopen($target_file,'w');
			$src_file = preg_replace('/^<\?php\s\/\*\*\s+(.*?\s)*?\*\*\/\s\?>\s/','',$src_file); // strip warning comments

			/* Translate Strings @since 1.1 */
			$src_file = preg_replace_callback('/\<\?php _(e)\(\'(.*?)\',\'Ecart\'\); \?\>/','preg_e_callback',$src_file);
			$src_file = preg_replace_callback('/_(_)\(\'(.*?)\',\'Ecart\'\)/','preg_e_callback',$src_file);
			$src_file = preg_replace('/\'\.\'/','',$src_file);

			fwrite($file,$src_file);
			fclose($file);
			chmod($target_file,0666);
		}
	}
}

/**
 * Calculates a cyclic redundancy checksum polynomial of 16-bit lengths of the data
 *
 * @since 1.1
 * @todo team consult
 *
 * @return int The checksum polynomial
 **/
function crc16 ($data) {
	$crc = 0xFFFF;
	for ($i = 0; $i < strlen($data); $i++) {
		$x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
		$x ^= $x >> 4;
		$crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
	}
	return $crc;
}

/**
 * remove_class_actions
 *
 * Removes all WordPress actions/filters registered by a particular class or its children.
 *
 * @since 1.1.4.1
 *
 * @param array/string $tags the action/filter name(s) to be removed
 * @param string $class the classname of the objects you wish to remove actions from
 * @param int $priority
 * @return void
 **/
function remove_class_actions ($tags = false, $class = 'stdCLass', $priority = false ) {
	global $wp_filter;

	if ($tags === false) return;
	foreach ((array) $tags as $tag) {
		foreach ($wp_filter[$tag] as $pri_index => $callbacks) {
			if ($priority !== $pri_index && $priority !== false) continue;
			foreach($callbacks as $idx => $callback) {
				if ( $tag == $idx ) continue; // idx will be the same as tag for non-object function callbacks
				if ( is_subclass_of($callback['function'][0], $class) || is_a($callback['function'][0], $class) ) {
					remove_filter($tag,$callback['function'],$pri_index,$callback['accepted_args']);
				}
			}
		}
	}
	return;
}


/**
 * Determines the currency format for the store
 *
 * If a format is provided, it is passed through. Otherwise the locale-based
 * currency format is used or if no locale setting is available
 * a default of $#,###.## is returned.  Any formatting settings that are
 * missing will use settings from the default.
 *
 * The currency format settings consist of a named array with the following:
 * cpos boolean The position of the currency symbol: true to prefix the number, false for suffix
 * currency string The currency symbol
 * precision int The decimal precision
 * decimals string The decimal delimiter
 * thousands string The thousands separator
 *
 * @since 1.1
 *
 * @param array $format (optional) A currency format settings array
 * @return array Format settings array
 **/
function currency_format ($format=false) {
	$default = array("cpos"=>true,"currency"=>"$","precision"=>2,"decimals"=>".","thousands" => ",","grouping"=>3);
	if ($format !== false) return array_merge($default,$format);
	$Settings = &EcartSettings();
	$locale = $Settings->get('base_operations');
	if (empty($locale['currency']['format']['currency'])) return $default;
	return array_merge($default,$locale['currency']['format']);
}

/**
 * Calculates the timestamp of a day based on a repeating interval (Fourth Thursday in November (Thanksgiving))
 *
 * @since 1.0
 *
 * @param int|string $week The week of the month (1-4, -1 or first-fourth, last)
 * @param int|string $dayOfWeek The day of the week (0-6 or Sunday-Saturday)
 * @param int $month The month, uses current month if none provided
 * @param int $year The year, uses current year if none provided
 * @return void
 **/
function datecalc($week=-1,$dayOfWeek=-1,$month=-1,$year=-1) {
	$weekdays = array("sunday" => 0, "monday" => 1, "tuesday" => 2, "wednesday" => 3, "thursday" => 4, "friday" => 5, "saturday" => 6);
	$weeks = array("first" => 1, "second" => 2, "third" => 3, "fourth" => 4, "last" => -1);

	if ($month == -1) $month = date ("n");	// No month provided, use current month
	if ($year == -1) $year = date("Y");   	// No year provided, use current year

	// Day of week is a string, look it up in the weekdays list
	if (!is_numeric($dayOfWeek)) {
		foreach ($weekdays as $dayName => $dayNum) {
			if (strtolower($dayOfWeek) == substr($dayName,0,strlen($dayOfWeek))) {
				$dayOfWeek = $dayNum;
				break;
			}
		}
	}
	if ($dayOfWeek < 0 || $dayOfWeek > 6) return false;

	if (!is_numeric($week)) $week = $weeks[$week];

	if ($week == -1) {
		$lastday = date("t", mktime(0,0,0,$month,1,$year));
		$tmp = (date("w",mktime(0,0,0,$month,$lastday,$year)) - $dayOfWeek) % 7;
		if ($tmp < 0) $tmp += 7;
		$day = $lastday - $tmp;
	} else {
		$tmp = ($dayOfWeek - date("w",mktime(0,0,0,$month,1,$year))) % 7;
		if ($tmp < 0) $tmp += 7;
		$day = (7 * $week) - 6 + $tmp;
	}

	return mktime(0,0,0,$month,$day,$year);
}

/**
 * Builds an array of the current WP date_format setting
 *
 * @since 1.1
 * @version 1.1
 *
 * @param boolean $fields Ensure all date elements are present for field order (+1.1.6)
 * @return array The list version of date_format
 **/
function date_format_order ($fields=false) {
	$format = get_option('date_format');

	$default = array('month' => 'F','day' => 'j','year' => 'Y');

	$tokens = array(
		'day' => 'dDjl',
		'month' => 'FmMn',
		'year' => 'yY'
	);

	$dt = join('',$tokens);
	$_ = array(); $s = 0;
	preg_match_all("/(.{1})/",$format,$matches);
	foreach ($matches[1] as $i => $token) {
		foreach ($tokens as $type => $pattern) {
			if (preg_match("/[$pattern]/",$token)) {
				$_[$type] = $token;
				break;
			} elseif (preg_match("/[^$dt]/",$token)) {
				$_['s'.$s++] = $token;
				break;
			}
		}
	}

	if ($fields) $_ = array_merge($_,$default,$_);

	return $_;
}

/**
 * Returns the duration (in days) between two timestamps
 *
 * @since 1.0
 *
 * @param int $start The starting timestamp
 * @param int $end The ending timestamp
 * @return int	Number of days between the start and end
 **/
function duration ($start,$end) {
	return ceil(($end - $start) / 86400);
}

/**
 * Escapes nested data structure values for safe output to the browser
 *
 * @since 1.1
 *
 * @param mixed $value The data to escape
 * @return mixed
 **/
function esc_attrs ($value) {
	 $value = is_array($value)?array_map('esc_attrs', $value):esc_attr($value);
	 return $value;
}

/**
 * Callback to filter out files beginning with a dot
 *
 * @since 1.0
 *
 * @param string $name The filename to check
 * @return boolean
 **/
function filter_dotfiles ($name) {
	return (substr($name,0,1) != ".");
}

/**
 * Find a target file starting at a given directory
 *
 * @since 1.1
 * @param string $filename The target file to find
 * @param string $directory The starting directory
 * @param string $root The original starting directory
 * @param array $found Result array that matching files are added to
 **/
function find_filepath ($filename, $directory, $root, &$found) {
	if (is_dir($directory)) {
		$Directory = @dir($directory);
		if ($Directory) {
			while (( $file = $Directory->read() ) !== false) {
				if (substr($file,0,1) == "." || substr($file,0,1) == "_") continue;				// Ignore .dot files and _directories
				if (is_dir($directory.'/'.$file) && $directory == $root)		// Scan one deep more than root
					find_filepath($filename,$directory.'/'.$file,$root, $found);	// but avoid recursive scans
				elseif ($file == $filename)
					$found[] = substr($directory,strlen($root)).'/'.$file;		// Add the file to the found list
			}
			return true;
		}
	}
	return false;
}

/**
 * Finds files of a specific extension
 *
 * Recursively searches directories and one-level deep of sub-directories for
 * files with a specific extension
 *
 * NOTE: Files are saved to the $found parameter, an array passed by
 * reference, not a returned value
 *
 * @since 1.0
 *
 * @param string $extension File extension to search for
 * @param string $directory Starting directory
 * @param string $root Starting directory reference
 * @param string &$found List of files found
 * @return boolean Returns true if files are found
 **/
function find_files ($extension, $directory, $root, &$found) {
	if (is_dir($directory)) {

		$Directory = @dir($directory);
		if ($Directory) {
			while (( $file = $Directory->read() ) !== false) {
				if (substr($file,0,1) == "." || substr($file,0,1) == "_") continue;				// Ignore .dot files and _directories
				if (is_dir($directory.DIRECTORY_SEPARATOR.$file) && $directory == $root)		// Scan one deep more than root
					find_files($extension,$directory.DIRECTORY_SEPARATOR.$file,$root, $found);	// but avoid recursive scans
				if (substr($file,strlen($extension)*-1) == $extension)
					$found[] = substr($directory,strlen($root)).DIRECTORY_SEPARATOR.$file;		// Add the file to the found list
			}
			return true;
		}
	}
	return false;
}

/**
 * Determines the mimetype of a file
 *
 * @since 1.0
 *
 * @param string $file The path to the file
 * @param string $name (optional) The name of the file
 * @return string The mimetype of the file
 **/
function file_mimetype ($file,$name=false) {
	if (!$name) $name = basename($file);
	if (file_exists($file)) {
		if (function_exists('finfo_open')) {
			// Try using PECL module
			$f = finfo_open(FILEINFO_MIME);
			list($mime,$charset) = explode(";",finfo_file($f, $file));
			finfo_close($f);
			new EcartError('File mimetype detection (finfo_open): '.$mime,false,ECART_DEBUG_ERR);
			if (!empty($mime)) return $mime;
		} elseif (class_exists('finfo')) {
			// Or class
			$f = new finfo(FILEINFO_MIME);
			new EcartError('File mimetype detection (finfo class): '.$f->file($file),false,ECART_DEBUG_ERR);
			return $f->file($file);
		} elseif (strlen($mime=trim(@shell_exec('file -bI "'.escapeshellarg($file).'"')))!=0) {
			new EcartError('File mimetype detection (shell file command): '.$mime,false,ECART_DEBUG_ERR);
			// Use shell if allowed
			return trim($mime);
		} elseif (strlen($mime=trim(@shell_exec('file -bi "'.escapeshellarg($file).'"')))!=0) {
			new EcartError('File mimetype detection (shell file command, alt options): '.$mime,false,ECART_DEBUG_ERR);
			// Use shell if allowed
			return trim($mime);
		} elseif (function_exists('mime_content_type') && $mime = mime_content_type($file)) {
			// Try with magic-mime if available
			new EcartError('File mimetype detection (mime_content_type()): '.$mime,false,ECART_DEBUG_ERR);
			return $mime;
		}
	}

	if (!preg_match('/\.([a-z0-9]{2,4})$/i', $name, $extension)) return false;

	switch (strtolower($extension[1])) {
		// misc files
		case 'txt':	return 'text/plain';
		case 'htm': case 'html': case 'php': return 'text/html';
		case 'css': return 'text/css';
		case 'js': return 'application/javascript';
		case 'json': return 'application/json';
		case 'xml': return 'application/xml';
		case 'swf':	return 'application/x-shockwave-flash';

		// images
		case 'jpg': case 'jpeg': case 'jpe': return 'image/jpg';
		case 'png': case 'gif': case 'bmp': case 'tiff': return 'image/'.strtolower($matches[1]);
		case 'tif': return 'image/tif';
		case 'svg': case 'svgz': return 'image/svg+xml';

		// archives
		case 'zip':	return 'application/zip';
		case 'rar':	return 'application/x-rar-compressed';
		case 'exe':	case 'msi':	return 'application/x-msdownload';
		case 'tar':	return 'application/x-tar';
		case 'cab': return 'application/vnd.ms-cab-compressed';

		// audio/video
		case 'flv':	return 'video/x-flv';
		case 'mpeg': case 'mpg':	case 'mpe': return 'video/mpeg';
		case 'mp4s': return 'application/mp4';
		case 'm4a': return 'audio/mp4';
		case 'mp3': return 'audio/mpeg3';
		case 'wav':	return 'audio/wav';
		case 'aiff': case 'aif': return 'audio/aiff';
		case 'avi':	return 'video/msvideo';
		case 'wmv':	return 'video/x-ms-wmv';
		case 'mov':	case 'qt': return 'video/quicktime';

		// ms office
		case 'doc':	case 'docx': return 'application/msword';
		case 'xls':	case 'xlt':	case 'xlm':	case 'xld':	case 'xla':	case 'xlc':	case 'xlw':	case 'xll':	return 'application/vnd.ms-excel';
		case 'ppt':	case 'pps':	return 'application/vnd.ms-powerpoint';
		case 'rtf':	return 'application/rtf';

		// adobe
		case 'pdf':	return 'application/pdf';
		case 'psd': return 'image/vnd.adobe.photoshop';
	    case 'ai': case 'eps': case 'ps': return 'application/postscript';

		// open office
	    case 'odt': return 'application/vnd.oasis.opendocument.text';
	    case 'ods': return 'application/vnd.oasis.opendocument.spreadsheet';
	}

	return false;
}

/**
 * Converts a numeric string to a floating point number
 *
 * @since 1.0
 *
 * @param string $value Numeric string to be converted
 * @param boolean $round (optional) Whether to round the value (default true for to round)
 * @param array $format (optional) The currency format to use for precision (defaults to the current base of operations)
 * @return float
 **/
function floatvalue ($value, $round=true, $format=false) {
	$format = currency_format($format);
	extract($format,EXTR_SKIP);

	$v = (float)$value; // Try interpretting as a float and see if we have a valid value

	// If a valid float already, pass the value through
	// The variety of currency formats makes determining a valid float very difficult
	if (is_float($value) || (			// Original $value is a float, passthru
		is_float($v) 					// $v correctly casts to a float
		&& $v > 0 && (					// The casted float value is not 0
				$decimals == '.' || 	// not a normalized float if the decimal separator is in the value
				!empty($decimals) && $decimals != '.' && strpos($value,$decimals) === false	// and is not a period-character
			) && (						// Not a valid float if the thousands separator is present at all
				strpos($value,$thousands) === false
			)
		)) return floatval($round?round($value,$precision):$value);

	$value = preg_replace("/(\D\.|[^\d\,\.])/","",$value); // Remove any non-numeric string data
	$value = preg_replace("/^\./","",$value); // Remove any decimals at the beginning of the string
	$value = preg_replace("/\\".$thousands."/","",$value); // Remove thousands

	if ($precision > 0) // Don't convert decimals if not required
		$value = preg_replace("/\\".$decimals."/",".",$value); // Convert decimal delimter

	return $round?round(floatval($value),$precision):floatval($value);
}

/**
 * Modifies URLs to use SSL connections
 *
 * @since 1.0
 *
 * @param string $url Source URL to rewrite
 * @return string $url The secure URL
 **/
function force_ssl ($url,$rewrite=false) {
	if(is_ecart_secure() || $rewrite)
		$url = str_replace('http://', 'https://', $url);
	return $url;
}


/**
 * Determines the gateway path to a gateway file
 *
 * @since 1.0
 *
 * @param string $file The target gateway file
 * @return string The path fragment for the gateway file
 **/
function gateway_path ($file) {
	return basename(dirname($file)).'/'.basename($file);
}

/**
 * Handles sanitizing URLs for use in markup HREF attributes
 *
 * Wrapper for securing URLs generated with the WordPress
 * add_query_arg() function
 *
 * @since 1.0
 *
 * @param mixed $param1 Either newkey or an associative_array
 * @param mixed $param2 Either newvalue or oldquery or uri
 * @param mixed $param3 Optional. Old query or uri
 * @return string New URL query string.
 **/
if (!function_exists('href_add_query_arg')) {
	function href_add_query_arg () {
		$args = func_get_args();
		$url = call_user_func_array('add_query_arg',$args);
		list($uri,$query) = explode("?",$url);
		return $uri.'?'.htmlspecialchars($query);
	}
}

/**
 * Generates attribute markup for HTML inputs based on specified options
 *
 * @since 1.0
 *
 * @param array $options An associative array of options
 * @param array $allowed (optional) Allowable attribute options for the element
 * @return string Attribute markup fragment
 **/
function inputattrs ($options,$allowed=array()) {
	if (!is_array($options)) return "";
	if (empty($allowed)) {
		$allowed = array("autocomplete","accesskey","alt","checked","class","disabled","format",
			"minlength","maxlength","readonly","required","size","src","tabindex",
			"title","value");
	}
	$string = "";
	$classes = "";
	if (isset($options['label']) && !isset($options['value'])) $options['value'] = $options['label'];
	foreach ($options as $key => $value) {
		if (!in_array($key,$allowed)) continue;
		switch($key) {
			case "class": $classes .= " $value"; break;
			case "disabled":
				if (value_is_true($value)) {
					$classes .= " disabled";
					$string .= ' disabled="disabled"';
				}
				break;
			case "readonly":
				if (value_is_true($value)) {
					$classes .= " readonly";
					$string .= ' readonly="readonly"';
				}
				break;
			case "required": if (value_is_true($value)) $classes .= " required"; break;
			case "minlength": $classes .= " min$value"; break;
			case "format": $classes .= " $value"; break;
			default:
				$string .= ' '.$key.'="'.esc_attr($value).'"';
		}
	}
	if (!empty($classes)) $string .= ' class="'.trim($classes).'"';
 	return $string;
}

/**
 * Determines if the current client is a known web crawler bot
 *
 * @since 1.0
 *
 * @return boolean Returns true if a bot user agent is detected
 **/
function is_robot() {
	$bots = array("Googlebot","TeomaAgent","Zyborg","Gulliver","Architext spider","FAST-WebCrawler","Slurp","Ask Jeeves","ia_archiver","Scooter","Mercator","crawler@fast","Crawler","InfoSeek sidewinder","Lycos_Spider_(T-Rex)","Fluffy the Spider","Ultraseek","MantraAgent","Moget","MuscatFerret","VoilaBot","Sleek Spider","KIT_Fireball","WebCrawler");
	foreach($bots as $bot)
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),strtolower($bot))) return true;
	return false;
}

/**
 * Used to test user level for (to be deprecated) ECART_USERLEVEL macro
 *
 * Utility function for checking to see if ECART_USERLEVEL is defined and whether current user has
 * that level of access.
 *
 * @since 1.1
 * @deprecated
 *
 * @return bool ECART_USERLEVEL is defined and the user has privs at that level
 **/
function is_ecart_userlevel () {
	return defined('ECART_USERLEVEL') && current_user_can('ECART_USERLEVEL');
}

/**
 * Determines if the requested page is a Ecart page or if it matches a given Ecart page
 *
 * @since 1.0
 *
 * @param string $page (optional) Page name to look for in Ecart's page registry
 * @return boolean
 **/
function is_ecart_page ($page=false) {
	global $Ecart,$wp_query;

	if (isset($wp_query->post->post_type) &&
		$wp_query->post->post_type != "page") return false;

	$pages = $Ecart->Settings->get('pages');

	// Detect if the requested page is a Ecart page
	if (!$page) {
		foreach ($pages as $page)
			if ($page['id'] == $wp_query->post->ID) return true;
		return false;
	}

	// Determine if the visitor's requested page matches the provided page
	if (!isset($pages[strtolower($page)])) return false;
	$page = $pages[strtolower($page)];
	if (isset($wp_query->post->ID) &&
		$page['id'] == $wp_query->post->ID) return true;
	return false;
}

/**
 * Detects SSL requests
 *
 * @since 1.0
 *
 * @return boolean
 **/
function is_ecart_secure () {
	return (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != "off");
}

/**
 * Encodes an all parts of a URL
 *
 * @since 1.1
 *
 * @param string $url The URL of the link to encode
 * @return void
 **/
function linkencode ($url) {
	$search = array('%2F','%3A','%3F','%3D','%26');
	$replace = array('/',':','?','=','&');
	$url = rawurlencode($url);
	return str_replace($search, $replace, $url);
}

/**
 * Read the wp-config file to import WP settings without loading all of WordPress
 *
 * @since 1.1
 * @return boolean If the load was successful or not
 **/
function load_ecarts_wpconfig () {
	global $table_prefix;

	$configfile = 'wp-config.php';
	$loadfile = 'wp-load.php';
	$wp_config_path = $wp_abspath = false;

	$syspath = explode('/',$_SERVER['SCRIPT_FILENAME']);
	$uripath = explode('/',$_SERVER['SCRIPT_NAME']);
	$rootpath = array_diff($syspath,$uripath);
	$root = '/'.join('/',$rootpath);

	$filepath = dirname(!empty($_SERVER['SCRIPT_FILENAME'])?$_SERVER['SCRIPT_FILENAME']:__FILE__);

	if ( file_exists(sanitize_path($root).'/'.$loadfile))
		$wp_abspath = $root;

	if ( isset($_SERVER['ECART_WPCONFIG_PATH'])
		&& file_exists(sanitize_path($_SERVER['ECART_WPCONFIG_PATH']).'/'.$configfile) ) {
		// SetEnv ECART_WPCONFIG_PATH /path/to/wpconfig
		// and ECART_ABSPATH used on webserver site config
		$wp_config_path = $_SERVER['ECART_WPCONFIG_PATH'];

	} elseif ( strpos($filepath, $root) !== false ) {
		// Ecart directory has DOCUMENT_ROOT ancenstor, find wp-config.php
		$fullpath = explode ('/', sanitize_path($filepath) );
		while (!$wp_config_path && ($dir = array_pop($fullpath)) !== null) {
			if (file_exists( sanitize_path(join('/',$fullpath)).'/'.$loadfile ))
				$wp_abspath = join('/',$fullpath);
			if (file_exists( sanitize_path(join('/',$fullpath)).'/'.$configfile ))
				$wp_config_path = join('/',$fullpath);
		}

	} elseif ( file_exists(sanitize_path($root).'/'.$configfile) ) {
		$wp_config_path = $root; // WordPress install in DOCUMENT_ROOT
	} elseif ( file_exists(sanitize_path(dirname($root)).'/'.$configfile) ) {
		$wp_config_path = dirname($root); // wp-config up one directory from DOCUMENT_ROOT
	}

	$wp_config_file = sanitize_path($wp_config_path).'/'.$configfile;
	if ( $wp_config_path !== false )
		$config = file_get_contents($wp_config_file);
	else return false;

	preg_match_all('/^\s*?(define\(\s*?\'(.*?)\'\s*?,\s*(.*?)\);)/m',$config,$defines,PREG_SET_ORDER);
	foreach($defines as $defined) if (!defined($defined[2])) {
		list($line,$line,$name,$value) = $defined;
		$value = str_replace('__FILE__',"'$wp_abspath/$loadfile'",$value);
		$value = safe_define_ev($value);

		// Override ABSPATH with ECART_ABSPATH
		if ($name == "ABSPATH" && isset($_SERVER['ECART_ABSPATH'])
				&& file_exists(sanitize_path($_SERVER['ECART_ABSPATH']).'/'.$loadfile))
			$value = rtrim(sanitize_path($_SERVER['ECART_ABSPATH']),'/').'/';
		define($name,$value);
	}

	// Get the $table_prefix value
	preg_match('/(\$table_prefix\s*?=.+?);/m',$config,$match);
	$table_prefix = safe_define_ev($match[1]);

	if(function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get"))
		@date_default_timezone_set(@date_default_timezone_get());

	return true;
}

/**
 * Appends the blog id to the table prefix for multisite installs
 *
 * @since 1.1
 *
 * @return void
 **/
function ecart_ms_tableprefix () {
	global $table_prefix;

	$domain = $_SERVER['HTTP_HOST'] = (strpos($_SERVER['HTTP_HOST'],':') !== false) ?
	 				str_replace(array(':80',':443'),'',addslashes($_SERVER['HTTP_HOST'])):
					addslashes($_SERVER['HTTP_HOST']);

	if (strpos($_SERVER['HTTP_HOST'],':') !== false) die('Multisite only works without the port number in the URL.');

	$domain = rtrim($domain, '.');

	$path = preg_replace('|([a-z0-9-]+.php.*)|', '', $_SERVER['REQUEST_URI']);
	$path = str_replace ('/wp-admin/', '/', $path);
	$path = preg_replace('|(/[a-z0-9-]+?/).*|', '$1', $path);

	$wpdb_blogs = $table_prefix.'blogs';
	$db =& DB::get();
	$r = $db->query("SELECT blog_id FROM $wpdb_blogs WHERE domain='$domain' AND path='$path' LIMIT 1");
	if (!empty($r->blog_id))
		$table_prefix .= $r->blog_id.'_';
}

/**
 * Generates a timestamp from a MySQL datetime format
 *
 * @since 1.0
 *
 * @param string $datetime A MySQL date time string
 * @return int A timestamp number usable by PHP date functions
 **/
function mktimestamp ($datetime) {
	$h = $mn = $s = 0;
	list($Y, $M, $D, $h, $mn, $s) = sscanf($datetime,"%d-%d-%d %d:%d:%d");
	if (max($Y, $M, $D, $h, $mn, $s) == 0) return 0;
	return mktime($h, $mn, $s, $M, $D, $Y);
}

/**
 * Converts a timestamp number to an SQL datetime formatted string
 *
 * @since 1.0
 *
 * @param int $timestamp A timestamp number
 * @return string An SQL datetime formatted string
 **/
function mkdatetime ($timestamp) {
	return date("Y-m-d H:i:s",$timestamp);
}

/**
 * Returns the 24-hour equivalent of a the Ante Meridiem or Post Meridem hour
 *
 * @since 1.0
 *
 * @param int $hour The hour of the meridiem
 * @param string $meridiem Specified meridiem of "AM" or "PM"
 * @return int The 24-hour equivalent
 **/
function mk24hour ($hour, $meridiem) {
	if ($hour < 12 && $meridiem == "PM") return $hour + 12;
	if ($hour == 12 && $meridiem == "AM") return 0;
	return $hour;
}

/**
 * Returns a list marked-up as drop-down menu options */
/**
 * Generates HTML markup for the options of a drop-down menu
 *
 * Takes a list of options and generates the option elements for an HTML
 * select element.  By default, the option values and labels will be the
 * same.  If the values option is set, the option values will use the
 * key of the associative array, and the option label will be the value in
 * the array.  The extend option can be used to ensure that if the selected
 * value does not exist in the menu, it will be automatically added at the
 * top of the list.
 *
 * @since 1.0
 *
 * @param array $list The list of options
 * @param int|string $selected The array index, or key name of the selected value
 * @param boolean $values (optional) Use the array key as the option value attribute (defaults to false)
 * @param boolean $extend (optional) Use to add the selected value if it doesn't exist in the specified list of options
 * @return string The markup of option elements
 **/
function menuoptions ($list,$selected=null,$values=false,$extend=false) {
	if (!is_array($list)) return "";
	$string = "";
	// Extend the options if the selected value doesn't exist
	if ((!in_array($selected,$list) && !isset($list[$selected])) && $extend)
		$string .= "<option value=\"$selected\">$selected</option>";
	foreach ($list as $value => $text) {
		if ($values) {
			if ($value == $selected) $string .= "<option value=\"$value\" selected=\"selected\">$text</option>";
			else  $string .= "<option value=\"$value\">$text</option>";
		} else {
			if ($text == $selected) $string .= "<option selected=\"selected\">$text</option>";
			else  $string .= "<option>$text</option>";
		}
	}
	return $string;
}

/**
 * Formats a number amount using a specified currency format
 *
 * The number is formatted based on a currency formatting configuration
 * array that  includes the currency symbol position (cpos), the currency
 * symbol (currency), the decimal precision (precision), the decimal character
 * to use (decimals) and the thousands separator (thousands).
 *
 * If the currency format is not specified, the currency format from the
 * store setting is used.  If no setting is available, the currency format
 * for US dollars is used.
 *
 * @since 1.0
 *
 * @param float $amount The amount to be formatted
 * @param array $format The currency format to use
 * @return string The formatted amount
 **/
function money ($amount,$format=false) {
	$format = currency_format($format);
	$number = numeric_format($amount, $format['precision'], $format['decimals'], $format['thousands'], $format['grouping']);
	if ($format['cpos']) return $format['currency'].$number;
	else return $number.$format['currency'];
}

/**
 * Formats a number with typographically accurate multi-byte separators and variable algorisms
 *
 * @since 1.1
 *
 * @param float $number A floating point or integer to format
 * @param int $precision (optional) The number of decimal precision to format to [default: 2]
 * @param string $decimals The decimal separator character [default: .]
 * @param string $separator The number grouping separator character [default: ,]
 * @param int|array $grouping The number grouping pattern [default: array(3)]
 * @return string The formatted number
 **/
function numeric_format ($number, $precision=2, $decimals='.', $separator=',', $grouping=array(3)) {
	$n = sprintf("%0.{$precision}F",$number);
	$whole = $fraction = 0;

	if (strpos($n,'.') !== false) list($whole,$fraction) = explode('.',$n);
	else $whole = $n;

	if (!is_array($grouping)) $grouping = array($grouping);

	$i = 0;
	$lg = count($grouping)-1;
	$ng = array();
	while(strlen($whole) > $grouping[min($i,$lg)] && !empty($grouping[min($i,$lg)])) {
		$divide = strlen($whole) - $grouping[min($i++,$lg)];
		$sequence = $whole;
		$whole = substr($sequence,0,$divide);
		array_unshift($ng,substr($sequence,$divide));
	}
	if (!empty($whole)) array_unshift($ng,$whole);

	$whole = join($separator,$ng);
	$whole = str_pad($whole,1,'0');

	$fraction = rtrim(substr($fraction,0,$precision),'0');
	$fraction = str_pad($fraction,$precision,'0');

	$n = $whole.(!empty($fraction)?$decimals.$fraction:'');

	return $n;
}

/**
 * Formats a number to telephone number style
 *
 * @since 1.0
 *
 * @param int $num The number to format
 * @return string The formatted telephone number
 **/
function phone ($num) {
	if (empty($num)) return "";
	$num = preg_replace("/[A-Za-z\-\s\(\)]/","",$num);

	if (strlen($num) == 7) sscanf($num, "%3s%4s", $prefix, $exchange);
	if (strlen($num) == 10) sscanf($num, "%3s%3s%4s", $area, $prefix, $exchange);
	if (strlen($num) == 11) sscanf($num, "%1s%3s%3s%4s",$country, $area, $prefix, $exchange);
	//if (strlen($num) > 11) sscanf($num, "%3s%3s%4s%s", $area, $prefix, $exchange, $ext);

	$string = "";
	$string .= (isset($country))?"$country ":"";
	$string .= (isset($area))?"($area) ":"";
	$string .= (isset($prefix))?$prefix:"";
	$string .= (isset($exchange))?"-$exchange":"";
	$string .= (isset($ext))?" x$ext":"";
	return $string;

}

/**
 * Formats a numeric amount to a percentage using a specified format
 *
 * Uses a format configuration array to specify how the amount needs to be
 * formatted.  When no format is specified, the currency format setting
 * is used only paying attention to the decimal precision, decimal symbol and
 * thousands separator.  If no setting is available, a default configuration
 * is used (precision: 1) (decimal separator: .) (thousands separator: ,)
 *
 * @since 1.0
 *
 * @param float $amount The amount to format
 * @param array $format A specific format for the number
 * @return string The formatted percentage
 **/
function percentage ($amount,$format=false) {
	$format = currency_format($format);
	return numeric_format(round($amount,$format['precision']), $format['precision'], $format['decimals'], $format['thousands'], $format['grouping']).'%';
}

/**
 * Translate callback function for preg_replace_callback.
 *
 * Helper function for copy_ecart_templates to translate strings in core template files.
 *
 * @since 1.1
 *
 * @param array $matches preg matches array, expects $1 to be type and $2 to be string
 * @return string _e translated string
 **/
function preg_e_callback ($matches) {
	return ($matches[1] == 'e') ? __($matches[2],'Ecart') : "'".__($matches[2],'Ecart')."'";
}

/**
 * Returns the raw url that was requested
 *
 * Useful for getting the complete value of the requested url
 *
 * @since 1.1
 *
 * @return string raw request url
 **/
function raw_request_url () {
	return esc_url(
		'http'.
		(is_ecart_secure()?'s':'').
		'://'.
		$_SERVER['HTTP_HOST'].
		$_SERVER['REQUEST_URI'].
		(ECART_PRETTYURLS?((!empty($_SERVER['QUERY_STRING'])?'?':'').$_SERVER['QUERY_STRING']):'')
	);
}

/**
 * Converts bytes to the largest applicable human readable unit
 *
 * Supports up to petabyte sizes
 *
 * @since 1.0
 *
 * @param int $bytes The number of bytes
 * @return string The formatted unit size
 **/
function readableFileSize($bytes,$precision=1) {
	$units = array(__("bytes","Ecart"),"KB","MB","GB","TB","PB");
	$sized = $bytes*1;
	if ($sized == 0) return $sized;
	$unit = 0;
	while ($sized > 1024 && ++$unit) $sized = $sized/1024;
	return round($sized,$precision)." ".$units[$unit];
}

/**
 * Creates natural language amount of time from a specified timestamp to today
 *
 * The string includes the number of years, months, days, hours, minutes
 * and even seconds e.g.: 1 year, 5 months, 29 days , 23 hours and 59 minutes
 *
 * @since 1.0
 *
 * @param int $date The original timestamp
 * @return string The formatted time range
 **/
function readableTime($date, $long = false) {

	$secs = time() - $date;
	if (!$secs) return false;
	$i = 0; $j = 1;
	$desc = array(1 => 'second',
				  60 => 'minute',
				  3600 => 'hour',
				  86400 => 'day',

				  604800 => 'week',
				  2628000 => 'month',
				  31536000 => 'year');


	while (list($k,) = each($desc)) $breaks[] = $k;
	sort($breaks);

	while ($i < count($breaks) && $secs >= $breaks[$i]) $i++;
	$i--;
	$break = $breaks[$i];

	$val = intval($secs / $break);
	$retval = $val . ' ' . $desc[$break] . ($val>1?'s':'');

	if ($long && $i > 0) {
		$rest = $secs % $break;
		$break = $breaks[--$i];
		$rest = intval($rest/$break);

		if ($rest > 0) {
			$resttime = $rest.' '.$desc[$break].($rest > 1?'s':'');

			$retval .= ", $resttime";
		}
	}

	return $retval;
}

/**
 * Rounds a price amount with the store's currency format
 *
 * @since 1.1
 *
 * @param float $amount The number to be rounded
 * @param array $format (optional) The formatting settings to use,
 * @return float The rounded float
 **/
function roundprice ($amount,$format=false) {
	$format = currency_format($format);
	extract($format);
	return round($amount, $precision);
}

/**
 * Uses builtin php openssl library to encrypt data.
 *
 * @since 1.1
 *
 * @param string $data data to be encrypted
 * @param string $pkey PEM encoded RSA public key
 * @return string Encrypted binary data
 **/
function rsa_encrypt($data, $pkey){
	openssl_public_encrypt($data, $encrypted,$pkey);
	return ($encrypted)?$encrypted:false;
}

/**
 * Safely interprets a single PHP statement for dynamic macro definitions
 *
 * Ensures that unsafe code cannot be arbitrarily executed by three levels
 * of protection: unsafe function blacklist, no anonymous functions,
 * no backtick operations, and no variable variables.
 *
 * Additionally, the use of create_function to interpret the code ensures
 * that executed code doesn't taint the rest of the runtime environment.
 *
 * An error is generated to help detect and debug problem macro definitions.
 *
 * @since 1.1
 *
 * @param string $string A PHP statement to be interpreted
 * @return mixed The returned value
 **/
function safe_define_ev ($string) {
	$error = false;
	$f = array(	'base64_decode','base64_encode','copy','create_function','curl_init',
			'dl','exec','file_get_contents','fopen','fpassthru','include',
			'include_once','ini_restore','leak','link','mail','passthru','pcntl_exec',
			'pcntl_fork','pcntl_signal','pcntl_waitpid','pcntl_wexitstatus',
			'pcntl_wifexited','pcntl_wifsignaled','pcntl_wifstopped','pcntl_wstopsig',
			'pcntl_wtermsig','pfsockopen','phpinfo','popen','preg_replace','proc_get_status',
			'proc_nice','proc_open','proc_terminate','readfile','register_shutdown_function',
			'register_tick_function','require','require_once','shell_exec','socket_accept',
			'socket_bind','socket_connect','socket_create','socket_create_listen',
			'socket_create_pair','stream_socket_server','symlink','syslog','system');

	if (preg_match('/('.join('|',$f).')\s*\(.*?\)/',$string) !== 0)
		$error = "Unsafe function detected while interpreting a macro definition";
	elseif (preg_match('/\$\w+\s*=\s*function\s*\(/',$string) !== 0)
		$error = "Anoymous function detected while interpreting a macro definition";
	elseif (preg_match('/(\`.+?\`)/',preg_replace('/(\'.*?\'|".*?")/m','',$string)) !== 0)
		$error = "Unsafe backtick operator usage detected while interpreting a macro definition";
	elseif (strpos($string,'$$') !== false)
		$error = "Unsafe variable detected while interpreting a macro definition";

	if ($error !== false) {
		trigger_error($error,E_USER_ERROR);
		return '';
	}

	$code = create_function('','return ('.$string.');');
	if (empty($code)) return '';
	return $code();
}

if(!function_exists('sanitize_path')){
	/**
	 * Normalizes path separators to always use forward-slashes
	 *
	 * PHP path functions on Windows-based systems will return paths with
	 * backslashes as the directory separator.  This function is used to
	 * ensure we are always working with forward-slash paths
	 *
	 * @since 1.0
	 *
	 * @param string $path The path to clean up
	 * @return string $path The forward-slash path
	 **/
	function sanitize_path ($path) {
		return str_replace('\\', '/', $path);
	}
}

/**
 * Scans a formatted string to build a list of currency formatting settings
 *
 * @since 1.0
 * @version 1.1
 *
 * @param string $format A currency formatting string such as $#,###.##
 * @return array Formatting options list
 **/
function scan_money_format ($format) {
	$f = array(
		"cpos" => true,
		"currency" => "",
		"precision" => 0,
		"decimals" => "",
		"thousands" => "",
		"grouping" => 3
	);

	$ds = strpos($format,'#'); $de = strrpos($format,'#')+1;
	$df = substr($format,$ds,($de-$ds));

	$f['cpos'] = true;
	if ($de == strlen($format)) $f['currency'] = substr($format,0,$ds);
	else {
		$f['currency'] = substr($format,$de);
		$f['cpos'] = false;
	}

	$found = array();
	if (!preg_match_all('/([^#]+)/',$df,$found) || empty($found)) return $f;

	$dl = $found[0];
	$dd = 0; // Decimal digits

	if (count($dl) > 1) {
		if ($dl[0] == $dl[1] && !isset($dl[2])) {
			$f['thousands'] = $dl[1];
			$f['precision'] = 0;
		} else {
			$f['decimals'] = $dl[count($dl)-1];
			$f['thousands'] = $dl[0];
		}
	} else $f['decimals'] = $dl[0];

	$dfc = $df;
	// Count for precision
	if (!empty($f['decimals']) && strpos($df,$f['decimals']) !== false) {
		list($dfc,$dd) = explode($f['decimals'],$df);
		$f['precision'] = strlen($dd);
	}

	if (!empty($f['thousands']) && strpos($df,$f['thousands']) !== false) {
		$groupings = explode($f['thousands'],$dfc);
		$grouping = array();
		while (list($i,$g) = each($groupings))
			if (strlen($g) > 1) array_unshift($grouping,strlen($g));
		$f['grouping'] = $grouping;
	}

	return $f;
}

/**
 * Wraps mark-up in a #ecart container, if needed
 *
 * @since 1.1
 *
 * @param string $string The content markup to be wrapped
 * @return string The wrapped markup
 **/
function ecartdiv ($string) {
	if (strpos($string,'<div id="ecart">') === false)
		return '<div id="ecart">'.$string.'</div>';
	return $string;
}

/**
 * Sends an email message based on a specified template file
 *
 * Sends an e-mail message in the format of a specified e-mail
 * template file using variable substitution for variables appearing in
 * the template as a bracketed [variable] with data from the
 * provided data array or the super-global $_POST array
 *
 * @since 1.0
 *
 * @param string $template Email template file path
 * @param array $data The data to populate the template with
 * @return boolean True on success, false on failure
 **/
function ecart_email ($template,$data=array()) {

	if (strpos($template,"\r\n") !== false) $f = explode("\r\n",$template);
	elseif (strpos($template,"\n") !== false) $f = explode("\n",$template);
	else {
		if (strpos($template,".php") !== false) {
			// Parse a PHP template
			ob_start();
			include($template);
			$content = ob_get_contents();
			ob_end_clean();
			if (strpos($content,"\r\n") !== false) $f = explode("\r\n",$content);
			elseif (strpos($content,"\n") !== false) $f = explode("\n",$content);
		} elseif (file_exists($template)) $f = file($template);	// Load an HTML/text template
		else new EcartError(__("Could not open the email template because the file does not exist or is not readable.","Ecart"),'email_template',ECART_ADMIN_ERR,array('template'=>$template));
	}

	$replacements = array(
		"$" => "\\\$",		// Treat $ signs as literals
		"€" => "&euro;",	// Fix euro symbols
		"¥" => "&yen;",		// Fix yen symbols
		"£" => "&pound;",	// Fix pound symbols
		"¤" => "&curren;"	// Fix generic currency symbols
	);

	$debug = false;
	$in_body = false;
	$headers = "";
	$message = "";
	$to = "";
	$subject = "";
	$protected = array("from","to","subject","cc","bcc");
	while ( list($linenum,$line) = each($f) ) {
		$line = rtrim($line);
		// Data parse
		if ( preg_match_all("/\[(.+?)\]/",$line,$labels,PREG_SET_ORDER) ) {
			while ( list($i,$label) = each($labels) ) {
				$code = $label[1];
				if (empty($data)) $string = (isset($_POST[$code])?$_POST[$code]:'');
				else $string = apply_filters('ecart_email_data', $data[$code], $code);

				$string = str_replace(array_keys($replacements),array_values($replacements),$string);

				if (isset($string) && !is_array($string)) $line = preg_replace("/\[".$code."\]/",$string,$line);
			}
		}

		// Header parse
		if ( preg_match("/^(.+?):\s(.+)$/",$line,$found) && !$in_body ) {
			$header = $found[1];
			$string = $found[2];
			if (in_array(strtolower($header),$protected)) // Protect against header injection
				$string = str_replace(array("\r","\n"),"",urldecode($string));
			if ( strtolower($header) == "to" ) $to = $string;
			else if ( strtolower($header) == "subject" ) $subject = $string;
			else $headers .= $line."\n";
		}

		// Catches the first blank line to begin capturing message body
		if ( empty($line) ) $in_body = true;
		if ( $in_body ) $message .= $line."\n";
	}

	// Use only the email address, discard everything else
	if (strpos($to,'<') !== false) {
		list($name, $email) = explode('<',$to);
		$to = trim(rtrim($email,'>'));
	}

	if (!$debug) return wp_mail($to,$subject,$message,$headers);
	else {
		echo "<pre>";
		echo "To: $to\n";
		echo "Subject: $subject\n\n";
		echo "Message:\n$message\n";
		echo "Headers:\n";
		print_r($headers);
		echo "<pre>";
		exit();
	}
}

/**
 * Locates the Ecart content gateway pages in the WordPress posts table
 *
 * @since 1.1
 *
 * @param array $pages Currently known page data
 * @return array
 **/
function ecart_locate_pages () {
	global $wpdb;

	// No pages provided, use the Storefront definitions
	$pages = Storefront::$_pages;

	// Find pages with Ecart-related main shortcodes
	$search = "";
	foreach ($pages as $page)
		$search .= (!empty($search)?" OR ":"")."post_content LIKE '%".$page['shortcode']."%'";
	$query = "SELECT ID,post_title,post_name,post_content FROM $wpdb->posts WHERE ($search) AND post_type='page'";
	$results = $wpdb->get_results($query);

	// Match updates from the found results to our pages index
	foreach ($pages as $key => &$page) {
		// Convert Ecart 1.0 page definitions
		if (!isset($page['shortcode']) && isset($page['content'])) $page['shortcode'] = $page['content'];
		foreach ($results as $index => $post) {
			if (strpos($post->post_content,$page['shortcode']) !== false) {
				$page = array(
					'id' => $post->ID,
					'title' => $post->post_title,
					'name' => $post->post_name,
					'uri' => get_page_uri($post->ID)
				);
				break;
			}
		}
	}
	return $pages;
}

/**
 * Generates RSS markup in XML from a set of provided data
 *
 * @since 1.0
 *
 * @param array $data The data to populate the RSS feed with
 * @return string The RSS markup
 **/
function ecart_rss ($data) {
	// RSS filters
	add_filter('ecart_rss_description','convert_chars');
	add_filter('ecart_rss_description','ent2ncr');

	$xmlns = '';
	if (is_array($data['xmlns']))
		foreach ($data['xmlns'] as $key => $value)
			$xmlns .= 'xmlns:'.$key.'="'.$value.'" ';

	$xml = "<?xml version=\"1.0\""." encoding=\"utf-8\"?>\n";
	$xml .= "<rss version=\"2.0\" $xmlns>\n";
	$xml .= "<channel>\n";

	$xml .= '<atom:link href="'.esc_attr($data['link']).'" rel="self" type="application/rss+xml" />'."\n";
	$xml .= "<title>".esc_html($data['title'])."</title>\n";
	$xml .= "<description>".esc_html($data['description'])."</description>\n";
	$xml .= "<link>".esc_html($data['link'])."</link>\n";
	$xml .= "<language>en-us</language>\n";
	$xml .= "<copyright>".esc_html("Copyright ".date('Y').", ".$data['sitename'])."</copyright>\n";

	if (is_array($data['items'])) {
		foreach($data['items'] as $item) {
			$xml .= "\t<item>\n";
			foreach ($item as $key => $value) {
				$attrs = '';
				if (is_array($value)) {
					$data = $value;
					$value = '';
					foreach ($data as $name => $content) {
						if (empty($name)) $value = $content;
						else $attrs .= ' '.$name.'="'.esc_attr($content).'"';
					}
				}
				if (strpos($value,'<![CDATA[') === false) $value = esc_html($value);
				if (!empty($value)) $xml .= "\t\t<$key$attrs>$value</$key>\n";
				else $xml .= "\t\t<$key$attrs />\n";
			}
			$xml .= "\t</item>\n";
		}
	}

	$xml .= "</channel>\n";
	$xml .= "</rss>\n";

	return $xml;
}

/**
 * Checks for prerequisite technologies needed for Ecart
 *
 * @since 1.0
 *
 * @return boolean Returns true if all technologies are available
 **/
function ecart_prereqs () {
	$errors = array();

	// Check PHP version, this won't appear much since syntax errors in earlier
	// PHP releases will cause this code to never be executed
	if (!version_compare(PHP_VERSION, '5.0','>='))
		$errors[] = __("Ecart requires PHP version 5.0+.  You are using PHP version ").PHP_VERSION;

	if (version_compare(PHP_VERSION, '5.1.3','=='))
		$errors[] = __("Ecart will not work with PHP version 5.1.3 because of a critical bug in complex POST data structures.  Please upgrade PHP to version 5.1.4 or higher.");

	// Check WordPress version
	if (!version_compare(get_bloginfo('version'),'2.8','>='))
		$errors[] = __("Ecart requires WordPress version 2.8+.  You are using WordPress version ").get_bloginfo('version');

	// Check for cURL
	if( !function_exists("curl_init") &&
	      !function_exists("curl_setopt") &&
	      !function_exists("curl_exec") &&
	      !function_exists("curl_close") ) $errors[] = __("Ecart requires the cURL library for processing transactions securely. Your web hosting environment does not currently have cURL installed (or built into PHP).");

	// Check for GD
	if (!function_exists("gd_info")) $errors[] = __("Ecart requires the GD image library with JPEG support for generating gallery and thumbnail images.  Your web hosting environment does not currently have GD installed (or built into PHP).");
	else {
		$gd = gd_info();
		if (!isset($gd['JPG Support']) && !isset($gd['JPEG Support'])) $errors[] = __("Ecart requires JPEG support in the GD image library.  Your web hosting environment does not currently have a version of GD installed that has JPEG support.");
	}

	if (!empty($errors)) {
		$string .= '<style type="text/css">body { font: 13px/1 "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, sans-serif; } p { margin: 10px; }</style>';

		foreach ($errors as $error) $string .= "<p>$error</p>";

		$string .= '<p>'.__('Sorry! You will not be able to use Ecart.  For more information, see the <a href="http://docs.ecartlugin.net/Installation" target="_blank">online Ecart documentation.</a>').'</p>';

		trigger_error($string,E_USER_ERROR);
		exit();
	}
	return true;
}

/**
 * Returns the platform appropriate page name for Ecart internal pages
 *
 * IIS rewriting requires including index.php as part of the page
 *
 * @since 1.0
 *
 * @param string $page The normal page name
 * @return string The modified page name
 **/
function ecart_pagename ($page) {
	global $is_IIS;
	$prefix = strpos($page,"index.php/");
	if ($prefix !== false) return substr($page,$prefix+10);
	else return $page;
}

/**
 * Redirects the browser to a specified URL
 *
 * A wrapper for the wp_redirect function
 *
 * @since 1.0
 *
 * @param string $uri The URI to redirect to
 * @param boolean $exit (optional) Exit immediately after the redirect (defaults to true, set to false to override)
 * @return void
 **/
function ecart_redirect ($uri,$exit=true) {
	if (class_exists('EcartError'))	new EcartError('Redirecting to: '.$uri,'ecart_redirect',ECART_DEBUG_ERR);
	wp_redirect($uri);
	if ($exit) exit();
}

/**
 * Safely handles redirect requests to ensure they remain onsite
 *
 * Derived from WP 2.8 wp_safe_redirect
 *
 * @since 1.1
 *
 * @param string $location The URL to redirect to
 * @param int $status (optional) The HTTP status to send to the browser
 * @return void
 **/
function ecart_safe_redirect($location, $status = 302) {

	// Need to look at the URL the way it will end up in wp_redirect()
	$location = wp_sanitize_redirect($location);

	// browsers will assume 'http' is your protocol, and will obey a redirect to a URL starting with '//'
	if ( substr($location, 0, 2) == '//' )
		$location = 'http:' . $location;

	// In php 5 parse_url may fail if the URL query part contains http://, bug #38143
	$test = ( $cut = strpos($location, '?') ) ? substr( $location, 0, $cut ) : $location;

	$lp  = parse_url($test);
	$wpp = parse_url(get_option('home'));

	$allowed_hosts = (array) apply_filters('allowed_redirect_hosts', array($wpp['host']), isset($lp['host']) ? $lp['host'] : '');

	if ( isset($lp['host']) && ( !in_array($lp['host'], $allowed_hosts) && $lp['host'] != strtolower($wpp['host'])) )
		$location = ecarturl(false,'account');

	wp_redirect($location, $status);
}

/**
 * Determines the current taxrate from the store settings and provided options
 *
 * Contextually works out if the tax rate applies or not based on storefront
 * settings and the provided override options
 *
 * @since 1.0
 *
 * @param string $override (optional) Specifies whether to override the default taxrate behavior
 * @param string $taxprice (optional) Supports a secondary contextual override
 * @return float The determined tax rate
 **/
function ecart_taxrate ($override=null,$taxprice=true,$Item=false) {
	$Settings = &EcartSettings();
	$locale = $Settings->get('base_operations');
	$rated = false;
	$taxrate = 0;
	$Taxes = new CartTax();

	if ($locale['vat']) $rated = true;
	if (!is_null($override)) $rated = $override;
	if (!value_is_true($taxprice)) $rated = false;

	if ($rated) $taxrate = $Taxes->rate($Item);
	return $taxrate;
}

/**
 * Sets the default timezone based on the WordPress option (if available)
 *
 * @since 1.1
 *
 * @return void
 **/
function ecart_timezone () {
	if (function_exists('date_default_timezone_set') && get_option('timezone_string'))
		date_default_timezone_set(get_option('timezone_string'));
}

/**
 * Generates canonical storefront URLs that respects the WordPress permalink settings
 *
 * @since 1.1
 *
 * @param mixed $request Additional URI requests
 * @param string $page The gateway page
 * @param boolean $secure (optional) True for secure URLs, false to force unsecure URLs
 * @return string The final URL
 **/
function ecarturl ($request=false,$page='catalog',$secure=null) {
	$dynamic = array("thanks","receipt","confirm-order");

	$Settings =& EcartSettings();
	if (!$Settings->available) return;

	// Get the currently indexed Ecart gateway pages
	$pages = $Settings->get('pages');
	if (empty($pages)) { // Hrm, no pages, attempt to rescan for them
		// No WordPress actions, #epicfail
		if (!function_exists('do_action')) return false;
		do_action('ecart_reindex_pages');
		$pages = $Settings->get('pages');
		// Still no pages? WTH? #epicfailalso
		if (empty($pages)) return false;
	}

	// Start with the site url
	$siteurl = trailingslashit(get_bloginfo('url'));

	// Rewrite as an HTTPS connection if necessary
	if ($secure === false) $siteurl = str_replace('https://','http://',$siteurl);
	elseif (($secure || is_ecart_secure()) && !ECART_NOSSL) $siteurl = str_replace('http://','https://',$siteurl);

	// Determine WordPress gateway page URI path fragment
	if (isset($pages[$page])) {
		$path = $pages[$page]['uri'];
		$pageid = $pages[$page]['id'];
	} else {
		if (in_array($page,$dynamic)) {
			$target = $pages['checkout'];
			if (ECART_PRETTYURLS) {
				$catalog = empty($pages['catalog']['uri'])?$pages['catalog']['name']:$pages['catalog']['uri'];
				$path = trailingslashit($catalog).$page;
			} else $pageid = $target['id']."&ecart_proc=$page";
		} elseif ('images' == $page) {
			$target = $pages['catalog'];
			$path = trailingslashit($target['uri']).'images';
			if (!ECART_PRETTYURLS) $request = array('siid'=>$request);
		} else {
			$path = $pages['catalog']['uri'];
			$pageid = $pages['catalog']['id'];
		}
	}

	if (ECART_PRETTYURLS) $url = user_trailingslashit($siteurl.$path);
	else $url = isset($pageid)?add_query_arg('page_id',$pageid,$siteurl):$siteurl;

	// No extra request, return the complete URL
	if (!$request) return $url;

	// Filter URI request
	$uri = false;
	if (!is_array($request)) $uri = urldecode($request);
	if (is_array($request && isset($request[0]))) $uri = array_shift($request);
	if (!empty($uri)) $uri = join('/',array_map('urlencode',explode('/',$uri))); // sanitize

	$url = user_trailingslashit(trailingslashit($url).$uri);

	if (!empty($request) && is_array($request)) {
		$request = array_map('urldecode',$request);
		$request = array_map('urlencode',$request);
		$url = add_query_arg($request,$url);
	}

	return $url;
}

/**
 * Recursively sorts a heirarchical tree of data
 *
 * @param array $item The item data to be sorted
 * @param int $parent (internal) The parent item of the current iteration
 * @param int $key (internal) The identified index of the parent item in the current iteration
 * @param int $depth (internal) The number of the nested depth in the current iteration
 * @return array The sorted tree of data
 **/
function sort_tree ($items,$parent=0,$key=-1,$depth=-1) {
	$depth++;
	$position = 1;
	$result = array();
	if ($items) {
		foreach ($items as $item) {
			// Preserve initial priority
			if (isset($item->priority))	$item->_priority = $item->priority;
			if ($item->parent == $parent) {
				$item->parentkey = $key;
				$item->depth = $depth;
				$item->priority = $position++;
				$result[] = $item;
				$children = sort_tree($items, $item->id, count($result)-1, $depth);
				$result = array_merge($result,$children); // Add children in as they are found
			}
		}
	}
	$depth--;
	return $result;
}

/**
 * Converts natural language text to boolean values
 *
 * Used primarily for handling boolean text provided in ecart() tag options.
 *
 * @since 1.0
 *
 * @param string $value The natural language value
 * @return boolean The boolean value of the provided text
 **/
function value_is_true ($value) {
	switch (strtolower($value)) {
		case "yes": case "true": case "1": case "on": return true;
		default: return false;
	}
}

/**
 * Determines if a specified type is a valid HTML input element
 *
 * @since 1.0
 *
 * @param string $type The HTML element type name
 * @return boolean True if valid, false if not
 **/
function valid_input ($type) {
	$inputs = array("text","hidden","checkbox","radio","button","submit");
	if (in_array($type,$inputs) !== false) return true;
	return false;
}

?>