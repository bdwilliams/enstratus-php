# enStratus-php

Initial release of the PHP Wrapper only featuring 'GET' operations.

## Usage

	<?
	include "enstratus_class.php";
	
	$enstratus = new Enstratus;
	$enstratus->api_endpoint = 'http://api.enstratus.com';
	$enstratus->api_key = '<YOUR API KEY>';
	$enstratus->secret_key = '<YOUR SECRET KEY>';
	
	echo "<pre>";
	print_r($enstratus->getRegions());
	echo "</pre>";
	?>