<?php
declare(strict_types=1);

namespace corviscan\scan;

use PHPUnit\Framework\TestCase;
use RuntimeException;
/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MoveTest extends TestCase {
	private string $testDir;
	private string $sourceDir;
	private string $targetDir;
	
    #[\Override]
	protected function setUp(): void {
		// Create a temporary test directory
		$this->testDir = sys_get_temp_dir() . '/corviscan_test_' . uniqid();
		mkdir($this->testDir);
		
		$this->sourceDir = $this->testDir . '/scan-001';
		$this->targetDir = $this->testDir . '/patient-john';
	}
	
    #[\Override]
	protected function tearDown(): void {
		// Clean up test directory and all contents
		if (is_dir($this->testDir)) {
			$this->removeDirectory($this->testDir);
		}
	}
	
	private function removeDirectory(string $dir): void {
		if (!is_dir($dir)) {
			return;
		}
		
        $scan = scandir($dir);
        if($scan === false) {
            throw new RuntimeException("failed to scan directory {$dir}");
        }
		$files = array_diff($scan, ['.', '..']);
		foreach ($files as $file) {
			$path = $dir . '/' . $file;
			if (is_dir($path)) {
				$this->removeDirectory($path);
			} else {
				unlink($path);
			}
		}
		rmdir($dir);
	}
	
	private function createTestScan(string $dir, int $fileCount = 3): void {
		mkdir($dir);
		$basename = basename($dir);
		
		for ($i = 1; $i <= $fileCount; $i++) {
			$filename = sprintf("%s/%s-%02d.png", $dir, $basename, $i);
			file_put_contents($filename, "fake png content $i");
		}
	}
	
	function testConstructWithValidArguments(): void {
		$this->createTestScan($this->sourceDir);
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		
		$this->assertInstanceOf(Move::class, $move);
	}
	
	function testRunRenamesDirectoryAndFiles(): void {
		$this->createTestScan($this->sourceDir, 3);
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		$move->run();
		
		// Check that source directory no longer exists
		$this->assertDirectoryDoesNotExist($this->sourceDir);
		
		// Check that target directory exists
		$this->assertDirectoryExists($this->targetDir);
		
		// Check that files were renamed correctly
		$this->assertFileExists($this->targetDir . '/patient-john-01.png');
		$this->assertFileExists($this->targetDir . '/patient-john-02.png');
		$this->assertFileExists($this->targetDir . '/patient-john-03.png');
		
		// Check that old filenames don't exist
		$this->assertFileDoesNotExist($this->targetDir . '/scan-001-01.png');
		$this->assertFileDoesNotExist($this->targetDir . '/scan-001-02.png');
		$this->assertFileDoesNotExist($this->targetDir . '/scan-001-03.png');
		
		// Verify file contents are preserved
		$this->assertSame("fake png content 1", file_get_contents($this->targetDir . '/patient-john-01.png'));
		$this->assertSame("fake png content 2", file_get_contents($this->targetDir . '/patient-john-02.png'));
		$this->assertSame("fake png content 3", file_get_contents($this->targetDir . '/patient-john-03.png'));
	}
	
	function testRunWithEmptyDirectory(): void {
		mkdir($this->sourceDir);
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		$move->run();
		
		// Should still rename the directory even if empty
		$this->assertDirectoryDoesNotExist($this->sourceDir);
		$this->assertDirectoryExists($this->targetDir);
	}
	
	function testRunWithNonMatchingFiles(): void {
		mkdir($this->sourceDir);
		
		// Create files that don't match the pattern
		file_put_contents($this->sourceDir . '/random.png', 'content');
		file_put_contents($this->sourceDir . '/other-file.txt', 'content');
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		$move->run();
		
		// Directory should be renamed
		$this->assertDirectoryExists($this->targetDir);
		
		// Non-matching files should still exist with original names
		$this->assertFileExists($this->targetDir . '/random.png');
		$this->assertFileExists($this->targetDir . '/other-file.txt');
	}
	
	function testRunWithMixedFiles(): void {
		$this->createTestScan($this->sourceDir, 2);
		
		// Add a non-matching file
		file_put_contents($this->sourceDir . '/notes.txt', 'some notes');
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		$move->run();
		
		// Matching files should be renamed
		$this->assertFileExists($this->targetDir . '/patient-john-01.png');
		$this->assertFileExists($this->targetDir . '/patient-john-02.png');
		
		// Non-matching file should keep original name
		$this->assertFileExists($this->targetDir . '/notes.txt');
	}
	
	function testRunPreservesFileNumbering(): void {
		mkdir($this->sourceDir);
		$basename = basename($this->sourceDir);
		
		// Create files with specific numbering (not sequential)
		file_put_contents($this->sourceDir . "/{$basename}-01.png", 'content 1');
		file_put_contents($this->sourceDir . "/{$basename}-05.png", 'content 5');
		file_put_contents($this->sourceDir . "/{$basename}-10.png", 'content 10');
		
		$argv = ['corviscan-move.php', $this->sourceDir, $this->targetDir];
		$move = new Move($argv);
		$move->run();
		
		// Check that numbering is preserved
		$this->assertFileExists($this->targetDir . '/patient-john-01.png');
		$this->assertFileExists($this->targetDir . '/patient-john-05.png');
		$this->assertFileExists($this->targetDir . '/patient-john-10.png');
		
		// Verify contents
		$this->assertSame('content 1', file_get_contents($this->targetDir . '/patient-john-01.png'));
		$this->assertSame('content 5', file_get_contents($this->targetDir . '/patient-john-05.png'));
		$this->assertSame('content 10', file_get_contents($this->targetDir . '/patient-john-10.png'));
	}
}

// Made with Bob
