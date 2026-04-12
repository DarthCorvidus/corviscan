<?php
namespace corviscan\scan;
use plibv4\import\ImportGeneric;
use plibv4\uservalue\UserValue;
use plibv4\validate\ValidateEnum;
final class Profile extends ImportGeneric {
	const A4_WIDTH = "210";
	const A4_HEIGHT = "297";
	const RESOLUTION = "150";
	private function __construct() {
	}
	
	public static function asDefault(): Profile {
		$profile = new Profile();
		$profile->addHeight();
		$profile->addMode();
		$profile->addResolution();
		$profile->addWidth();
	return $profile;
	}
	
	public static function asNamed(): Profile {
		$profile = Profile::asDefault();
		$profile->addName();
	return $profile;
	}


	private function addWidth(): void {
		$uv = UserValue::asMandatory();
		$uv->setDefault(self::A4_WIDTH);
		$this->addScalar("width", $uv);
	}
	
	private function addHeight(): void {
		$uv = UserValue::asMandatory();
		$uv->setDefault(self::A4_HEIGHT);
		$this->addScalar("height", $uv);
	}

	private function addMode(): void {
		$uv = UserValue::asMandatory();
		$uv->setValidate(new ValidateEnum(array("gray", "color")));
		$uv->setDefault("gray");
		$this->addScalar("mode", $uv);
	}
	
	private function addResolution(): void {
		$uv = UserValue::asMandatory();
		$uv->setDefault(self::RESOLUTION);
		$this->addScalar("resolution", $uv);
	}
	
	private function addName(): void {
		$uv = UserValue::asMandatory();
		$this->addScalar("name", $uv);
	}

}
