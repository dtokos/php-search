<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

interface SearchFieldBuilder {
	/**
	 * @param callable(SearchFieldBuilder): SearchFieldBuilder $buildingBlock
	 * @return $this
	 */
	public function and(callable $buildingBlock): static;

	/**
	 * @param callable(SearchFieldBuilder): SearchFieldBuilder $buildingBlock
	 * @return $this
	 */
	public function or(callable $buildingBlock): static;

	/**
	 * @param mixed $condition
	 * @param callable(SearchFieldBuilder): SearchFieldBuilder $buildingBlock
	 * @return $this
	 */
	public function if(mixed $condition, callable $buildingBlock): static;

	/**
	 * @param mixed $condition
	 * @param callable(SearchFieldBuilder): SearchFieldBuilder $buildingBlock
	 * @return $this
	 */
	public function unless(mixed $condition, callable $buildingBlock): static;
	public function add(SearchField $field): static;
	public function build(): ResultQueryScope;
}
