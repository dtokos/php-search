<?php

namespace Artvys\Search\Engines\Compiled;

use Artvys\Search\Engines\Compiled\Compilers\IO\SearchSourceProvider;

class SearchSourceRegistry implements SearchSourceProvider {
	/** @var SearchSource[] */
	protected array $sources = [];
	/** @var array<string, int[]> */
	protected array $aliasMap = [];
	/** @var int[] */
	protected array $unaliased = [];

	/**
	 * @param SearchSource $source
	 * @param string[] $aliases
	 * @param bool $allowUnaliased
	 * @return $this
	 */
	public function register(SearchSource $source, array $aliases, bool $allowUnaliased = true): static {
		$key = $this->registerSource($source);
		$this->registerAliases($key, $aliases);

		if ($allowUnaliased)
			$this->registerUnaliased($key);

		return $this;
	}

	protected function registerSource(SearchSource $source): int {
		$this->sources[] = $source;
		return array_key_last($this->sources);
	}

	/**
	 * @param int $key
	 * @param string[] $aliases
	 * @return void
	 */
	protected function registerAliases(int $key, array $aliases): void {
		foreach ($aliases as $alias) {
			$this->aliasMap[$alias] ??= [];
			$this->aliasMap[$alias][] = $key;
		}
	}

	protected function registerUnaliased(int $key): void {
		$this->unaliased[] = $key;
	}

	public function has(string $alias): bool {
		return !empty($this->for([$alias]));
	}

	/** @inheritDoc */
	public function all(): array {
		return $this->sources;
	}

	/** @inheritDoc */
	public function unaliased(): array {
		return $this->forKeys($this->unaliased);
	}

	/** @inheritDoc */
	public function for(array $aliases): array {
		$keys = [];

		foreach ($aliases as $alias)
			foreach ($this->aliasMap[$alias] ?? [] as $key)
				$keys[] = $key;

		return $this->forKeys(array_unique($keys));
	}

	/**
	 * @param int[] $keys
	 * @return SearchSource[]
	 */
	protected function forKeys(array $keys): array {
		return array_map(fn(int $key) => $this->sources[$key], $keys);
	}
}
