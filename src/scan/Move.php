<?php
namespace corviscan\scan;

use InvalidArgumentException;
use RuntimeException;

final class Move {
	private string $source;
	private string $target;
	private bool $verbose;
	
	/**
	 * @param list<string> $argv
	 */
	function __construct(array $argv) {
		if (count($argv) !== 3) {
			throw new InvalidArgumentException(
				"Usage: " . basename($argv[0]) . " <source> <target>" . PHP_EOL .
				PHP_EOL .
				"Renames scan directory and its PNG files." . PHP_EOL .
				"Example: " . basename($argv[0]) . " scan-001 patient-john"
			);
		}
		
		$this->source = $argv[1];
		$this->target = $argv[2];
		$this->verbose = true;
		
		$this->validate();
	}
	
	private function validate(): void {
		// Validate source directory exists
		if (!is_dir($this->source)) {
			throw new InvalidArgumentException("Source directory '{$this->source}' does not exist.");
		}
		
		// Validate target directory doesn't exist
		if (file_exists($this->target)) {
			throw new InvalidArgumentException("Target '{$this->target}' already exists.");
		}
	}
	
	private function log(string $message): void {
		if ($this->verbose) {
			echo $message . PHP_EOL;
		}
	}
	
	/**
	 * @return list<string>
	 */
	private function getPngFiles(): array {
		$pngFiles = glob($this->source . "/*.png");
		
		if ($pngFiles === false) {
			throw new RuntimeException("Unable to read directory '{$this->source}'.");
		}
		
		return $pngFiles;
	}
	
	function run(): void {
		$pngFiles = $this->getPngFiles();
		
		if (empty($pngFiles)) {
			$this->log("Warning: No PNG files found in '{$this->source}'.");
		}
		
		$sourceBasename = basename($this->source);
		$targetBasename = basename($this->target);
		
		$this->log("Renaming directory: {$this->source} -> {$this->target}");
		$this->log("Found " . count($pngFiles) . " PNG file(s) to rename.");
		$this->log("");
		
		// Rename the directory first
		if (!rename($this->source, $this->target)) {
			throw new RuntimeException("Failed to rename directory '{$this->source}' to '{$this->target}'.");
		}
		
		$this->log("✓ Directory renamed successfully.");
		
		// Rename all PNG files
		$renamedCount = 0;
		$failedFiles = [];
		
		foreach ($pngFiles as $oldPath) {
			$filename = basename($oldPath);
			
			// Check if filename matches the pattern {source}-{number}.png
			if (preg_match('/^' . preg_quote($sourceBasename, '/') . '-(\d+)\.png$/', $filename, $matches)) {
				$number = $matches[1];
				$newFilename = $targetBasename . '-' . $number . '.png';
				$newPath = $this->target . '/' . $newFilename;
				
				// The old path is now invalid since we renamed the directory
				$currentPath = $this->target . '/' . $filename;
				
				if (rename($currentPath, $newPath)) {
					$this->log("✓ Renamed: $filename -> $newFilename");
					$renamedCount++;
				} else {
					$this->log("✗ Failed to rename: $filename");
					$failedFiles[] = $filename;
				}
			} else {
				$this->log("⊘ Skipped (doesn't match pattern): $filename");
			}
		}
		
		$this->log("");
		$this->log("Summary:");
		$this->log("  Renamed: $renamedCount file(s)");
		
		if (!empty($failedFiles)) {
			$this->log("  Failed: " . count($failedFiles) . " file(s)");
			throw new RuntimeException("Failed to rename " . count($failedFiles) . " file(s).");
		}
		
		$this->log("");
		$this->log("✓ Move completed successfully!");
	}
}

// Made with Bob
