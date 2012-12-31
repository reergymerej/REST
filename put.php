<?php

//	POST = create
parse_str( file_get_contents("php://input"), $post_vars );

//	find noun
$request = $_SERVER['REQUEST_URI'];
$request = explode( '/', substr( $request, strpos( $request, 'api' ) + 4 ) );

$noun = $request[0];
$id = isset($request[1]) && $request[1] !== '' ? $request[1] : null;

if( is_null($id) ){
	echo "id missing";
	return;
};


require('easydb.php');

update($noun, $id, $post_vars);







return;





//	get table info
$fields = getFieldsFrom($noun);


$q = "UPDATE $noun SET ";

//	field & values
foreach($post_vars as $field => $value){
	$q .= mysql_real_escape_string( "$field = " );

	$value = mysql_real_escape_string( $value );

	//	add quotes for strings
	if( !isset($fields[$field]) ){
		echo "unknown field '$field'";
		return;
	};

	if( strpos( $fields[$field], 'varchar' ) > -1 ){
		$q .= "'$value', ";
	} else {
		if( !is_numeric($value) ){
			echo "'$value' is not a number";
			return;
		};
		$q .= "$value, ";
	};

};

//	remove last ,
$q = substr($q, 0, strlen($q) - 2);

$q .= " WHERE id = $id;";

//	perform query
$result = mysql_query($q);

if( $error = mysql_error() ){
	echo "I could not update the $noun with id $id with this data.\n";
	echo $error;
	return;
} else {
	echo true;
};


function getFieldsFrom($table){
	$q = "SHOW COLUMNS FROM $table";
	$result = mysql_query($q);
	// $rows = [];
	$rows = array();
	while($r = mysql_fetch_assoc($result)){
		$rows[$r['Field']] = $r['Type'];
	};
	return $rows;
};
?>