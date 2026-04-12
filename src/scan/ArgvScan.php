<?php
namespace corviscan\scan;
use plibv4\argv\ArgvGeneric;
use plibv4\uservalue\UserValue;
class ArgvScan extends ArgvGeneric {
	function __construct() {
		$this->addPositionalArg("target", UserValue::asMandatory());
		$this->addNamedArg("profile", UserValue::asOptional());
	}
}
