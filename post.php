<?php

//	POST = create

//	find noun
$request = $_SERVER['REQUEST_URI'];
$request = explode( '/', substr( $request, strpos( $request, 'api' ) + 4 ) );

$noun = $request[0];

require('easydb.php');
$id = insert($noun, $_POST);

//	return the noun_id
echo $id;

return;
?>