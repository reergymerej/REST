<?php

//	GET = read

//	what was requested?
$request = $_SERVER['REQUEST_URI'];
$request = explode( '/', substr( $request, strpos( $request, 'api' ) + 4 ) );

$noun = $request[0];
$id = isset($request[1]) && $request[1] !== '' ? $request[1] : null;

require('easydb.php');

//	perform query
$result = select($noun, $id);

if( !$result || count($result) === 0 	){
	echo "I can't find a $noun with id $id.";
	return;
};

echo json_encode($result);
?>