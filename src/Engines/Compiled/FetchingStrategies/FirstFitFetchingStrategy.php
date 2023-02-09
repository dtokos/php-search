<?php

namespace Artvys\Search\Engines\Compiled\FetchingStrategies;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\FetchingStrategy;

class FirstFitFetchingStrategy implements FetchingStrategy {
	/** @inheritDoc */
	public function fetch(CompilationResult $result, int $limit): array {
		$remaining = $limit;
		$allResults = [];

		foreach ($result->bindings() as $binding) {
			foreach ($binding->sources() as $source) {
				$sourceResults = $source->search($binding->query(), $limit);
				$allResults[] = $sourceResults;
				$remaining -= count($sourceResults);

				if ($remaining <= 0)
					break 2;
			}
		}

		return array_merge(...$allResults);
	}
}
