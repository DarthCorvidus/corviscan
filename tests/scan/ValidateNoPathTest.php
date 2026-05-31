<?php
declare(strict_types=1);

namespace corviscan\scan;

use PHPUnit\Framework\TestCase;
use plibv4\validate\ValidateException;
/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ValidateNoPathTest extends TestCase {
	private string $testDir;
	
    #[\Override]
	protected function setUp(): void {
		// Create a temporary test directory
		$this->testDir = sys_get_temp_dir() . '/corviscan_validate_test_' . uniqid();
	}
	
    #[\Override]
	protected function tearDown(): void {
		// Clean up test files and directories
		if (file_exists($this->testDir)) {
			if (is_dir($this->testDir)) {
				rmdir($this->testDir);
			} else {
				unlink($this->testDir);
			}
		}
	}
	
	function testValidateWithNonExistentPath(): void {
		$validator = new ValidateNoPath();
		
		// Should not throw exception for non-existent path
		$validator->validate($this->testDir);
		
		// If we get here, the test passed
		$this->assertTrue(true);
	}
	
	function testValidateWithExistingDirectory(): void {
		mkdir($this->testDir);
		
		$validator = new ValidateNoPath();
		
		$this->expectException(ValidateException::class);
		$this->expectExceptionMessage("path already exists.");
		
		$validator->validate($this->testDir);
	}
	
	function testValidateWithExistingFile(): void {
		file_put_contents($this->testDir, "test content");
		
		$validator = new ValidateNoPath();
		
		$this->expectException(ValidateException::class);
		$this->expectExceptionMessage("path already exists.");
		
		$validator->validate($this->testDir);
	}
	
	function testValidateWithSymlink(): void {
		$targetFile = sys_get_temp_dir() . '/corviscan_target_' . uniqid();
		file_put_contents($targetFile, "content");
		symlink($targetFile, $this->testDir);
		
		$validator = new ValidateNoPath();
		
		try {
			$this->expectException(ValidateException::class);
			$this->expectExceptionMessage("path already exists.");
			
			$validator->validate($this->testDir);
		} finally {
			// Clean up symlink and target
			if (is_link($this->testDir)) {
				unlink($this->testDir);
			}
			if (file_exists($targetFile)) {
				unlink($targetFile);
			}
		}
	}
}

// Made with Bob
