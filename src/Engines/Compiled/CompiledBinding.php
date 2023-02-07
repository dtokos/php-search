<?php

namespace Artvys\Search\Engines\Compiled;

class CompiledBinding {
	private CompiledQuery $query;
	/** @var SearchSource[] */
	private array $sources;

	/**
	 * @param CompiledQuery $query
	 * @param SearchSource[] $sources
	 */
	public function __construct(CompiledQuery $query, array $sources) {
		$this->query = $query;
		$this->sources = $sources;
	}

	public function query(): CompiledQuery {
		return $this->query;
	}

	/** @return SearchSource[] */
	public function sources(): array {
		return $this->sources;
	}
}
