<?php

//	utilities for basic db operations on our crazy database
//	This allows an interface that is unaware of our crazy layout.

require('connect.php');


/**
* Return result of basic select statement.
* @param {string} $noun
* @param {integer} [$id=null]
* @return {array}
**/
function select($noun, $id = null){

	if( is_null($id) ){
		$q = "SELECT * FROM crud WHERE noun = '$noun' AND noun_id IS NOT NULL ORDER BY noun_id";
	} else {
		$q = "SELECT * FROM crud WHERE noun = '$noun' AND noun_id = $id";
	};

	$result = mysql_query($q);

	// $rows = [];
	$rows = array();

	while($r = mysql_fetch_assoc($result)){
		array_push($rows, $r);
	};

	//	combine our crud layout to resemble a standard table
	$id = null;
	$last_id = null;
	// $virtual_row = [];
	$virtual_row = array();
	// $virtual_rows = [];
	$virtual_rows = array();

	foreach($rows as $r){
		
		$id = $r['noun_id'];
		
		if($id !== $last_id){

			//	new row
			$last_id = $id;
			$virtual_row = array('id' => $id);

		};

		//	Which virtual field has a value set?
		foreach($r as $field => $val){
			if( $field === 'field_name' || strpos($field, 'field_') !== 0 ){
				continue;
			};

			if($val !== null){
				$virtual_row[ $r['field_name'] ] = $val;
			};
		};

		$virtual_rows[$id] = $virtual_row;	
	};

	// $rows = [];
	$rows = array();
	foreach($virtual_rows as $r){
		array_push($rows, $r);
	};

	return $rows;
};


/**
* Insert a new row with variable number of fields.
* @param {string} $noun
* @param {array} $fields associative array of fields -- fields['some_field'] = 'asdf'
* @return {integer} the ID of the new object
**/
function insert($noun, $fields){

	//	insert blank* row to get the id
	$q = "INSERT INTO crud (noun, field_name) VALUES ('$noun', 'id');";
	$result = mysql_query($q);
	
	//	get the id and use it for noun_id
	$noun_id = mysql_insert_id();

	//	group fields into data types
	// $typeGroups = [];
	$typeGroups = array();

	foreach($fields as $f => $val){

		$actualField = getField($val);

		//	make sure a group for this is declared
		if( !isset($typeGroups[$actualField]) ){
			// $typeGroups[$actualField] = [];
			$typeGroups[$actualField] = array();
		};

		//	push this name/val pair to this group
		$typeGroups[$actualField][$f] = $val;
	};

	//	run an insert for each group of field types
	foreach($typeGroups as $field => $pair){
		
		$q = "INSERT INTO crud (noun, noun_id, field_name, $field) VALUES ";
			
		foreach($pair as $name => $val){

			//	wrap quotes around strings
			if($field === 'field_varchar'){
				$val = "'$val'";
			};

			$q .= "('$noun', $noun_id, '$name', $val),";
		};

		//	remove last comma
		$q = substr($q, 0, strlen($q) - 1) . ';';

		$result = mysql_query($q);
	};
	
	return $noun_id;
};


/**
* Update a "row" by ID.
* @param {string} $noun
* @param {integer} $id
* @param {array} $fields associative array of fields -- fields['some_field'] = 'asdf'
**/
function update($noun, $id, $fields){
	
	foreach($fields as $name => $val){
		$field = getField($val);

		//	wrap quotes around strings
		if($field === 'field_varchar'){
			$val = "'$val'";
		};

		$q = "UPDATE crud set $field = $val";
		$q .= " WHERE field_name = '$name' AND noun_id = $id;";

		$result = mysql_query($q);

		//	If this field doesn't exist, make it.
		$info = explode(" ", mysql_info());
		if( ($info[2] === '0') ){
			$q = "INSERT INTO crud (noun, noun_id, field_name, $field) VALUES ('$noun', $id, '$name', $val);";
			mysql_query($q);
		};
	};
};


/**
* Update a row by ID.
* @param {string} $noun
* @param {integer} $id
**/
function delete($noun, $id){
	$q = "DELETE FROM crud WHERE id = $id OR noun_id = $id;";
	mysql_query($q);
};

/**
* Get the field that should store this value in the db based on the value's type.
* @param {string} $val All POSTed values are strings, but we convert to their actual value.
* @return {string} the name of the field the value should be stored in
**/
function getField($val){
	return 'field_' . getActualType($val);
};


/**
* Get data type from string value.  All POSTed values are strings, so use this to try and figure out 
* what the real data type is.
* @param {string} $val 
* @return {string} 'int, float, varchar'
**/ 
function getActualType($val){
	if( is_numeric($val) ){
		if( strpos($val, '.') !== FALSE ){
			return 'float';
		};

		return 'int';
	} else {
		return 'varchar';
	};
};
?>