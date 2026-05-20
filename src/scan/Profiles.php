<?php
namespace corviscan\scan;
use InvalidArgumentException;
use plibv4\import\ImportGeneric;
use plibv4\import\Import;
final class Profiles extends ImportGeneric {
	private Import $import;
	function __construct(string $path) {
		$this->addImportModel("default", Profile::asDefault());
		$this->addImportList("profiles", Profile::asNamed());
		if(!file_exists($path)) {
			throw new InvalidArgumentException(sprintf("profile path %s does not exist.", $path));
		}
		set_error_handler(array($this, "catchYaml"));
		/**
		 * @psalm-suppress MixedAssignment, PossiblyFalseArgument
		 */
		$yml = yaml_parse_file($path);
		if($yml === false) {
			// Should not happen due to error handler
			exit();
		}
		restore_error_handler();
		/**
		 * Getting $yml typed is the whole point of Import.
		 * @psalm-suppress MixedArgument
		 */
		$this->import = new Import($yml, $this);
	}
	
	function getDefaultImport(): Import {
		return $this->import->getImport("default");
	}

	function getNamedImport(string $name): Import {
		for($i = 0; $i<$this->import->getImportList("profiles")->getCount(); $i++) {
			$import = $this->import->getImportList("profiles")->getImport($i);
			if($import->getString("name")===$name) {
				return $import;
			}
		}
	throw new \InvalidArgumentException(sprintf("unable to find profile '%s'", $name));
	}

	/**
	 * @return list<string>
	 */
	function getProfileNames(): array {
		$profiles = array();
		for($i = 0; $i<$this->import->getImportList("profiles")->getCount(); $i++) {
			$import = $this->import->getImportList("profiles")->getImport($i);
			$profiles[] = $import->getString("name");
		}
	return $profiles;
	}

	/** 
	 * @psalm-suppress PossiblyUnusedReturnValue, UnusedParam
	 */
	function catchYaml(int $errno, string $errstr): bool {
		if($errstr !== "") {
			throw new InvalidArgumentException($errstr);
		}
	return true;
	}
}
