<?php

namespace Artvys\Search\Engines\Compiled;

use Artvys\Search\SearchResult;

interface FetchingStrategy {
	/**
	 * @param CompilationResult $result
	 * @param int $limit
	 * @return SearchResult[]
	 */
	public function fetch(CompilationResult $result, int $limit): array;
}
