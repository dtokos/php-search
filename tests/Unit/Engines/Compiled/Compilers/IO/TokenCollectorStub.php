<?php

namespace Tests\Unit\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\TokenCollector;

class TokenCollectorStub implements TokenCollector {
	/** @var string[] */
	public array $tokens = [];

	public function token(string $token): void {
		$this->tokens[] = $token;
	}

	public function symbol(string $symbol): void {
		$this->tokens[] = $symbol;
	}

	public function comma(): void {
		$this->tokens[] = ',';
	}

	public function colon(): void {
		$this->tokens[] = ':';
	}

	public function eof(): void {
		$this->tokens[] = 'eof';
	}
}
