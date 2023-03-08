<?php

namespace Artvys\Search\Engines\Compiled;

class CompiledBinding {
	protected CompiledQuery $query;
	/** @var SearchSource[] */
	protected array $sources;

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
