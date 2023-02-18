<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

interface ResultQueryBuilder {
	/**
	 * @param callable(ResultQueryBuilder): void $buildingBlock
	 * @return $this
	 */
	public function and(callable $buildingBlock): static;

	/**
	 * @param callable(ResultQueryBuilder): void $buildingBlock
	 * @return $this
	 */
	public function or(callable $buildingBlock): static;
	public function equals(string $field, string $token): static;
	public function contains(string $field, string $token): static;
	public function startsWith(string $field, string $token): static;
	public function endsWith(string $field, string $token): static;
}
