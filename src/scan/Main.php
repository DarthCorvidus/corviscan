<?php
namespace corviscan\scan;
use plibv4\argv\Argv;
use plibv4\argv\ArgvException;
use plibv4\argv\ArgvReference;
final class Main {
	private string $target;
	function __construct(array $argv) {
		$argvModel = new ArgvScan();
		try {
			$argvImport = new Argv($argv, $argvModel);
		} catch (ArgvException $e) {
			echo $e->getMessage().PHP_EOL;
			$ref = new ArgvReference($argvModel);
			echo $ref->getReference().PHP_EOL;
			exit();
		}
		$this->target = $argvImport->getPositional(0);		
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
		$x = 210;
		$y = 297;
		$res = 150;
		$tmpTIFF = "/tmp/scan.tif";
		$tmpPNG = "/tmp/scan.png";
		while(true) {
			$this->verboseExec("scanimage --mode gray -x ".$x." -y ".$y." --format=tiff --resolution=".$res." > ".$tmpTIFF);
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
