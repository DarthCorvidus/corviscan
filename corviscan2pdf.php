#!/usr/bin/env php
<?php
namespace corviscan\scan;

use Exception;
use plibv4\argv\Argv;
use plibv4\argv\ArgvGeneric;
use plibv4\convert\ConvertTrailingSlash;
use plibv4\uservalue\UserValue;
use plibv4\validate\ValidatePath;

require_once 'vendor/autoload.php';
$argvModel = new ArgvGeneric();

$corvifolder = UserValue::asMandatory();
$corvifolder->setValidate(new ValidatePath(ValidatePath::DIR));
$corvifolder->setConvert(new ConvertTrailingSlash());
$argvModel->addPositionalArg("dir", $corvifolder);

$arg = new Argv($argv, $argvModel);
$folder = $arg->getPositional(0);
$target = $folder.".pdf";
if(file_exists($target)) {
	echo "File {$target} already exists.".PHP_EOL;
	exit(1);
}

$command = "img2pdf ".escapeshellarg($folder)."/*.png -o {$target}";
Main::verboseExec($command);