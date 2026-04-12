<?php
declare(strict_types=1);

namespace corviscan\scan;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use plibv4\import\Import;

final class ProfileTest extends TestCase {
	function testConstruct():void {
		$profile = Profile::asNamed();
		$this->assertInstanceOf(Profile::class, $profile);
	}
	
	function testImportDefault(): void {
		$profile = Profile::asNamed();
		$import = new Import(array("name" => "default"), $profile);
		$array = $import->getArray();
		$this->assertSame("gray", $array["mode"]);
		$this->assertSame("150", $array["resolution"]);
		$this->assertSame("210", $array["width"]);
		$this->assertSame("297", $array["height"]);
	}
}