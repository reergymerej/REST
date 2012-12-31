<?php

$request = $_SERVER['REQUEST_URI'];

//	what are we doing?
$verb = $_SERVER['REQUEST_METHOD'];

switch($verb){

	case 'GET':
		require('get.php');
		break;

	case 'POST':
		require('post.php');
		break;

	case 'DELETE':
		require('delete.php');
		break;

	case 'PUT':
		require('put.php');
		break;

	default:
		echo "What the hell are you talking about?";
		break;
};

?>