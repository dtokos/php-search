<?php

namespace Artvys\Search;

interface SearchEngine {
	/**
	 * @param string $query
	 * @param int $limit
	 * @return SearchResult[]
	 */
	public function search(string $query, int $limit): array;
}
