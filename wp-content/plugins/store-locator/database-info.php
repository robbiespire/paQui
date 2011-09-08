<?php
if (file_exists("./wp-config.php")){include("./wp-config.php");}
elseif (file_exists("../wp-config.php")){include("../wp-config.php");}
elseif (file_exists("../../wp-config.php")){include("../../wp-config.php");}
elseif (file_exists("../../../wp-config.php")){include("../../../wp-config.php");}
elseif (file_exists("../../../../wp-config.php")){include("../../../../wp-config.php");}
elseif (file_exists("../../../../../wp-config.php")){include("../../../../../wp-config.php");}
elseif (file_exists("../../../../../../wp-config.php")){include("../../../../../../wp-config.php");}
elseif (file_exists("../../../../../../../wp-config.php")){include("../../../../../../../wp-config.php");}
elseif (file_exists("../../../../../../../../wp-config.php")){include("../../../../../../../../wp-config.php");}

$username=DB_USER;
$password=DB_PASSWORD;
$database=DB_NAME;
$host=DB_HOST;

//print "u:$username, p:$password, d:$database";
?>

