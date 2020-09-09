<?php
include 'server.php';

header('Content-Type: text/event-stream');
header('Access-Control-Allow-Origin: *');
header('X-Accel-Buffering: no');

$id = 0;

while ( true ) {
	
	if ( $id ==  0 ) {
		
		$type = 'open'; event( $id, $type );
	}
	
	if ( ! connection_aborted() ) {
		
		$type = 'load'; event( $id, $type );
		
	} else {
		
		ob_end_clean();clearstatcache();
	}
	
	if ( $id == TIME_OUT - 3 ) {
		
		$type = 'timeout'; event( $id, $type );
	}
	
	$id++;
	
	sleep(1); 
}