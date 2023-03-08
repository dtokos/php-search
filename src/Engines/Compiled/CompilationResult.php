<?php

namespace Artvys\Search\Engines\Compiled;

class CompilationResult {
	/** @var CompiledBinding[] */
	protected array $bindings;

	/**
	 * @param CompiledBinding[] $bindings
	 */
	public function __construct(array $bindings) {
		$this->bindings = $bindings;
	}

	/** @return CompiledBinding[] */
	public function bindings(): array {
		return $this->bindings;
	}

	/** @return CompiledQuery[] */
	public function queries(): array {
		return array_map(fn(CompiledBinding $b) => $b->query(), $this->bindings());
	}

	/** @return SearchSource[] */
	public function sources(): array {
		return array_merge(...array_map(fn(CompiledBinding $b) => $b->sources(), $this->bindings()));
	}
}
