<?php
declare(strict_types=1);

namespace corviscan\scan;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class ProfilesTest extends TestCase {
	const TEST0 = 0;
	const TEST1 = 1;
	const TEST2 = 2;
	function testConstruct():void {
		$profiles = new Profiles(__DIR__."/profiles.yml");
		$this->assertInstanceOf(Profiles::class, $profiles);
	}

	function testConstructMissingPath():void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage("profile path /home/hm/NetBeansProjects/corviscan/tests/scan/profiles_none.yml does not exist.");
		new Profiles(__DIR__."/profiles_none.yml");
	}
	
	function testConstructInvalid():void {
		$this->expectException(InvalidArgumentException::class);
		new Profiles(__DIR__."/invalid.yml");
	}
	
	function testGetDefaultProfile(): void {
		$profiles = new Profiles(__DIR__."/profiles.yml");
		$profile = $profiles->getDefaultImport();
		$this->assertSame("297", $profile->getString("height"));
		$this->assertSame("210", $profile->getString("width"));
		$this->assertSame("150", $profile->getString("resolution"));
		$this->assertSame("gray", $profile->getString("mode"));
	}

	function testGetProfileNames(): void {
		$expectedNames = array("credit", "vvs-ticket");
		$profiles = new Profiles(__DIR__."/profiles.yml");
		$profileNames = $profiles->getProfileNames();
		$this->assertEquals($expectedNames, $profileNames);
	}
}