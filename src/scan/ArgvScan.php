<?php
namespace corviscan\scan;
use plibv4\argv\ArgvGeneric;
use plibv4\uservalue\UserValue;
use plibv4\convert\ConvertTrailingSlash;
final class ArgvScan extends ArgvGeneric {
	function __construct() {
		$target = UserValue::asMandatory();
		$target->setConvert(new ConvertTrailingSlash(ConvertTrailingSlash::REMOVE));
		$this->addPositionalArg("target", $target);
		$this->addNamedArg("profile", UserValue::asOptional());
	}
}
