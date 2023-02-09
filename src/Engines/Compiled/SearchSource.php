<?php

namespace Artvys\Search\Engines\Compiled;

use Artvys\Search\SearchResult;

interface SearchSource {
	/**
	 * @param CompiledQuery $query
	 * @param int $limit
	 * @return SearchResult[]
	 */
	public function search(CompiledQuery $query, int $limit): array;
}
