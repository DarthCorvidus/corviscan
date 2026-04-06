#!/usr/bin/env php
<?php
namespace corviscan\scan;
use corviscan\scan\Main;
require_once 'vendor/autoload.php';

$main = new Main($argv);
$main->run();