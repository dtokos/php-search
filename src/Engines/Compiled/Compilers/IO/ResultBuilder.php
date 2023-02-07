<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\SearchSource;

interface ResultBuilder {
	public function addToken(string $token): void;

	/**
	 * @param SearchSource[] $sources
	 * @return void
	 */
	public function addQuery(array $sources): void;
}
