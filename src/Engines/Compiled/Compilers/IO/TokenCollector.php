<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

interface TokenCollector {
	public function token(string $token): void;
	public function symbol(string $symbol): void;
	public function comma(string $symbol): void;
	public function colon(string $symbol): void;
	public function eof(): void;
}
