<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\SearchSource;

interface SearchSourceProvider {
	public function has(string $alias): bool;

	/** @return SearchSource[] */
	public function all(): array;

	/** @return SearchSource[] */
	public function unaliased(): array;

	/**
	 * @param string[] $aliases
	 * @return SearchSource[]
	 */
	public function for(array $aliases): array;
}
