<?php

//	DELETE = delete

//	what was requested?
$request = $_SERVER['REQUEST_URI'];
$request = explode( '/', substr( $request, strpos( $request, 'api' ) + 4 ) );

$noun = $request[0];
$id = isset($request[1]) && $request[1] !== '' ? $request[1] : null;

if( is_null($id) ){
	echo "id missing";
	return;
};

require('easydb.php');
delete($noun, $id);
?>