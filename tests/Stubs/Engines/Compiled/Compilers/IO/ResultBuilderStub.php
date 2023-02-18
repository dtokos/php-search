<?php

namespace Tests\Stubs\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\ResultBuilder;
use Artvys\Search\Engines\Compiled\SearchSource;

class ResultBuilderStub implements ResultBuilder {
	/** @var array<int, array{query: string[], sources: SearchSource[]}> */
	public array $queries = [];
	/** @var string[] */
	private array $tokens = [];

	public function addToken(string $token): void {
		$this->tokens[] = $token;
	}

	/** @inheritDoc */
	public function addQuery(array $sources): void {
		$this->queries[] = ['query' => $this->tokens, 'sources' => $sources];
		$this->tokens = [];
	}
}
