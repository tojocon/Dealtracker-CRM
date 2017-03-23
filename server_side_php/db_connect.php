<?php

$db_engine = 'mysql';
$db_user = 'your_info_goes_here';
$db_pass = 'your_info_goes_here';

$db_host = 'your_info_goes_here';
$db_name = 'iyour_info_goes_here';


$db_object= mysql_connect($db_host, $db_user, $db_pass);

mysql_select_db($db_name);



if(mysql_error() ) {

	echo("test ".mysql_error()."end" );
	die(mysql_error() );
}else{

}



?>
