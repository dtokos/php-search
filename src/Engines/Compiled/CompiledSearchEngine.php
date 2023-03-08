<?php

namespace Artvys\Search\Engines\Compiled;

use Artvys\Search\SearchEngine;
use Artvys\Search\SearchResult;

class CompiledSearchEngine implements SearchEngine {
	protected readonly CompilerFactory $compilerFactory;
	protected readonly FetchingStrategy $fetchingStrategy;

	public function __construct(CompilerFactory $compilerFactory, FetchingStrategy $fetchingStrategy) {
		$this->compilerFactory = $compilerFactory;
		$this->fetchingStrategy = $fetchingStrategy;
	}

	/** @inheritDoc */
	public function search(string $query, int $limit): array {
		$compiler = $this->makeCompiler();
		$result = $this->compile($query, $compiler);

		return $this->fetchResults($result, $limit);
	}

	protected function makeCompiler(): Compiler {
		return $this->compilerFactory->make();
	}

	protected function compile(string $query, Compiler $compiler): CompilationResult {
		return $compiler->compile($query);
	}

	/**
	 * @param CompilationResult $result
	 * @param int $limit
	 * @return SearchResult[]
	 */
	protected function fetchResults(CompilationResult $result, int $limit): array {
		return $this->fetchingStrategy->fetch($result, $limit);
	}
}
