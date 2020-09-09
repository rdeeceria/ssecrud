<?php
include 'server.php';
$input = file_get_contents('php://input');

$file = fopen(NAME_FILE, 'w+');
fclose($file);
clearstatcache();

$message = json_decode($input, true );
				
$file = fopen(NAME_FILE, 'a+');

foreach($message as $k => $v ) {
	
	fwrite($file, json_encode( $v ) ."\n");
}
fclose($file);