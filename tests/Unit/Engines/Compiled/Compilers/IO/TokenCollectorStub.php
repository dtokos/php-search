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

	public function comma(string $symbol): void {
		$this->tokens[] = $symbol;
	}

	public function colon(string $symbol): void {
		$this->tokens[] = $symbol;
	}

	public function eof(): void {
		$this->tokens[] = 'eof';
	}
}
