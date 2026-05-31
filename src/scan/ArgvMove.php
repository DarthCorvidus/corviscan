<?php
namespace corviscan\scan;

use plibv4\argv\ArgvGeneric;
use plibv4\uservalue\UserValue;
use plibv4\convert\ConvertTrailingSlash;

final class ArgvMove extends ArgvGeneric {
	function __construct() {
		$source = UserValue::asMandatory();
		$source->setConvert(new ConvertTrailingSlash(ConvertTrailingSlash::REMOVE));
		$this->addPositionalArg("source", $source);
		
		$target = UserValue::asMandatory();
		$target->setConvert(new ConvertTrailingSlash(ConvertTrailingSlash::REMOVE));
		$this->addPositionalArg("target", $target);
	}
}

// Made with Bob
