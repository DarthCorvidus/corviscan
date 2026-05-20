<?php
namespace corviscan\scan;

use InvalidArgumentException;
use plibv4\argv\Argv;
use plibv4\argv\ArgvException;
use plibv4\argv\ArgvReference;
use plibv4\import\Import;
final class Main {
	private string $target;
	private Import $actProfile;
	/**
	 * 
	 * @param list<string> $argv
	 */
	function __construct(array $argv) {
		$argvModel = new ArgvScan();
		
		if(!isset($_SERVER["HOME"])) {
			echo "\$HOME not set, unable to determine config path.".PHP_EOL;
			exit();
		}
		$profiles = new Profiles($_SERVER["HOME"]."/.config/corviscan/profiles.yml");
		try {
			$argvImport = new Argv($argv, $argvModel);
		} catch (ArgvException $e) {
			echo $e->getMessage().PHP_EOL;
			$ref = new ArgvReference($argvModel);
			echo $ref->getReference().PHP_EOL;
			exit();
		}
		$this->target = $argvImport->getPositional(0);
		$this->actProfile = $profiles->getDefaultImport();
		if($argvImport->hasValue("profile")) {
			try {
				$this->actProfile = $profiles->getNamedImport($argvImport->getValue("profile"));				
			} catch(InvalidArgumentException $e) {
				$profileNames = $profiles->getProfileNames();
				if(empty($profileNames)) {
					echo "Invalid profile, no profiles defined.".PHP_EOL;
					exit(1);
				}
				echo "Invalid profile. Available profiles:".PHP_EOL;
				foreach($profileNames as $value) {
					echo "\t".$value.PHP_EOL;
				}
				exit(1);
			}
		}
		if(file_exists($this->target)) {
			echo sprintf("Target directory %s already exists. Scan anyway (y/N)?", $this->target);
			$input = $this->getInput();
			if($input !== "y") {
				exit();
			}
		} else {
			mkdir($this->target);
		}
	}
	
	private function getInput(): string {
		$untrimmed = fgets(STDIN);
		if($untrimmed === false) {
			echo "Unable to read from STDIN".PHP_EOL;
			exit();
		}
	return trim($untrimmed);
	}
	
	private function verboseExec(string $command): void {
		echo "Running command: ".$command.PHP_EOL;
		$ph = popen($command, "r");
		if($ph === false) {
			echo "Unable to run command: ".$command.PHP_EOL;
			exit();
		}
		while($line = fgets($ph)) {
			echo $line;
		}
		pclose($ph);
	}
	
	function run(): void {
		$count = 1;
		$x = $this->actProfile->getString("width");
		$y = $this->actProfile->getString("height");
		$res = $this->actProfile->getString("resolution");
		$mode = $this->actProfile->getString("mode");
		$tmpTIFF = "/tmp/scan.tif";
		$tmpPNG = "/tmp/scan.png";
		while(true) {
			$this->verboseExec("scanimage --mode ".$mode." -x ".$x." -y ".$y." --format=tiff --resolution=".$res." > ".$tmpTIFF);
			$this->verboseExec("convert ".escapeshellarg($tmpTIFF)." ".escapeshellarg($tmpPNG));
			$final = sprintf("%s/%s-%02d.png", $this->target, $this->target, $count);
			$this->verboseExec("pngcrush ".escapeshellarg($tmpPNG)." ".escapeshellarg($final));
			$count++;
			echo sprintf("Insert next page (%d) or x to cancel", $count);
			$input = $this->getInput();
			if($input === "x") {
				break;
			}
		}
	}
}
