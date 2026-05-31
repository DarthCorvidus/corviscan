#!/usr/bin/env php
<?php
namespace corviscan\scan;

use Exception;

require_once 'vendor/autoload.php';

try {
	$move = new Move($argv);
	$move->run();
} catch (Exception $e) {
	echo "Error: " . $e->getMessage() . PHP_EOL;
	exit(1);
}

// Made with Bob
