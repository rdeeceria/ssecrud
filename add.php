<?php
include 'server.php';
$input = file_get_contents('php://input');

$message = json_decode($input, true);
				
$file = fopen(NAME_FILE, 'a+');
fwrite($file, json_encode($message) ."\n");
fclose($file);
?>