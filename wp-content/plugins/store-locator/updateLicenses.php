<?php
//var_dump($_POST);
//exit;

foreach ($_POST as $key=>$value) {

	if (ereg("sl_license_",$key) && $value!="") {
 $target = "http://www.viadat.com/sl_validate/confirm_single_license.php?lic=".urlencode($value)."&url=".urlencode($_SERVER[HTTP_HOST])."&dir=".urlencode(ereg_replace("sl_license_","",$key));
  //exit($target);
  $remote_access_fail = false;
	$useragent = 'Lots of Locales Store Locator Plugin';
  if(function_exists("curl_init")) {
    ob_start();
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_URL,$target);
    curl_exec($ch);
    $returned_value = ob_get_contents();
	//exit($returned_value);
   ob_end_clean();
	} else {
	  //$activation_name = urlencode($_POST['activation_name']);
	  //$activation_key = urlencode($_POST['activation_key']);
	  //$siteurl = urlencode(get_option('siteurl'));
	  $request = '';
	  $http_request  = "GET /sl_validate/confirm_single_license.php?lic=".urlencode($value)."&url=".urlencode($_SERVER[HTTP_HOST])."&dir=".urlencode(ereg_replace("sl_license_","",$key))." HTTP/1.0\r\n";
		$http_request .= "Host: viadat.com\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
		$http_request .= "Content-Length: " . strlen($request) . "\r\n";
		$http_request .= "User-Agent: $useragent\r\n";
		$http_request .= "\r\n";
		$http_request .= $request;
		$response = '';
		if( false != ( $fs = @fsockopen('viadat.com', 80, $errno, $errstr, 10) ) ) {
			fwrite($fs, $http_request);
			while ( !feof($fs) )
				$response .= fgets($fs, 1160); // One TCP-IP packet
			fclose($fs);
		}
		//$response = explode("\r\n\r\n", $response, 2);
		//$returned_value = (int)trim($response[1]);
		$returned_value = trim($response);
		//print $returned_value;
	}
	  if (ereg("validated",$returned_value)) {
		$activ=ereg_replace("sl_license_", "sl_activation_", $key);
		$dir_name=ereg_replace("sl_license_", "", $key);
		$enc=sha1(md5(base64_encode($_SERVER[HTTP_HOST].":".$dir_name)));
		$key_option=get_option("$key");
		$activ_option=get_option("$activ");
		if (empty($key_option)) {
			add_option("$key", $value);
		} else {
			update_option("$key", $value);
		}
		if (empty($activ_option)) {
			add_option("$activ", $enc);
		} else {
			update_option("$activ", $enc);
		}
		print "<div class='highlight'> Successful validation using key '$value' $view_link</div><br>";
	  }
	  else {
		print "<div class='highlight' style='border-color:red; background-color:salmon'>It appears that you've already used the key '$value'. Please try a different one.</div><br>";
	  }
  
  }
}

?>