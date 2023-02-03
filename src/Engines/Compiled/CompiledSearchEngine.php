<?php

namespace Artvys\Search\Engines\Compiled;

use Artvys\Search\SearchEngine;
use Artvys\Search\SearchResult;

class CompiledSearchEngine implements SearchEngine {
	private readonly CompilerFactory $compilerFactory;
	private readonly FetchingStrategy $fetchingStrategy;

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

	private function makeCompiler(): Compiler {
		return $this->compilerFactory->make();
	}

	private function compile(string $query, Compiler $compiler): CompilationResult {
		return $compiler->compile($query);
	}

	/**
	 * @param CompilationResult $result
	 * @param int $limit
	 * @return SearchResult[]
	 */
	private function fetchResults(CompilationResult $result, int $limit): array {
		return $this->fetchingStrategy->fetch($result, $limit);
	}
}
