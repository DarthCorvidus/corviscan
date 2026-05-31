<?php
namespace corviscan\scan;

use plibv4\validate\Validate;
use plibv4\validate\ValidateException;

/**
 * Validate that path does not exist
 * 
 * Validates that a path does not exist, useful for ensuring target
 * directories or files don't already exist before operations.
 */
final class ValidateNoPath implements Validate {
	#[\Override]
	public function validate(string $validee): void {
		if (file_exists($validee)) {
			throw new ValidateException("path already exists.");
		}
	}
}

// Made with Bob
