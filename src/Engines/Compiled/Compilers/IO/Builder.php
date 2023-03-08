<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\CompiledBinding;
use Artvys\Search\Engines\Compiled\CompiledQuery;

class Builder implements ResultBuilder, CompilerOutput {
	/** @var string[] */
	protected array $tokens = [];
	/** @var CompiledBinding[] */
	protected array $bindings = [];

	public function addToken(string $token): void {
		$this->tokens[] = $token;
	}

	/** @inheritDoc */
	public function addQuery(array $sources): void {
		if (empty($this->tokens) || empty($sources)) return;

		$this->bindings[] = new CompiledBinding(new CompiledQuery($this->tokens), $sources);
		$this->tokens = [];
	}

	public function result(): CompilationResult {
		return new CompilationResult($this->bindings);
	}
}
