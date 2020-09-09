<?php
const TIME_OUT = 60;
const NAME_FILE = 'json';

date_default_timezone_set('Asia/Jakarta');
set_time_limit( TIME_OUT );

function event( $id, $type )
{
	ob_implicit_flush();
	
	switch ( $type ) 
	{
		case 'open':
		
			if ( file_exists(NAME_FILE) ) {

				$d = json_load();
				
				$result = json_encode($d);
				
				echo "id: ". $id ."\nevent: ". $type ."\ndata: ". 'Stream open..' ."\n\n";
				ob_flush();flush();
				
				echo "\ndata: ". $result ."\n\n";
				ob_flush();flush();
				
				
			} else {
				
				echo "retry: ". 1000 ."\ndata: ". 'Create json and Stream Open' ."\n\n";
				ob_flush();flush();
				
				$file = fopen('json', 'a+');
				fclose( $file );
			}
			break;
		
		case 'load':
			
			$mt = filemtime(NAME_FILE);
			$d = json_load();
			
			$result = json_encode($d);

			if ( $mt == time() ) {	
			
				for($i = 0; $i < touch(NAME_FILE); $i++) {
					echo "id: ". $id ."\nevent: ". $type ."\ndata: ". $result ."\n\n";
					ob_flush();flush();
				}
			}
			break;
		
		case 'timeout':
		
			echo "retry: ". 5000 ."\nevent: ". $type ."\ndata: ". 'Timeout' ."\n\n";
			ob_flush();flush();

			break;
	}
	clearstatcache();
}

function json_load($opt = false) {
	
	$file = file(NAME_FILE);
	$r = preg_replace('/\n/', '', $file);
				
	$d = array();
	foreach ( $r as $k => $v ) {
		
		$d[$k] = json_decode($v, $opt);
	}
	clearstatcache();
	return $d;
}