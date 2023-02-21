<?php

namespace Tests\Stubs\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;

class ResultQueryBuilderStub implements ResultQueryBuilder {
	private array $parts = [];
	private string $group = '';

	/** @inheritDoc */
	public function and(callable $buildingBlock): static {
		return $this->buildGroup('AND', $buildingBlock);
	}

	/** @inheritDoc */
	public function or(callable $buildingBlock): static {
		return $this->buildGroup('OR', $buildingBlock);
	}

	public function equals(string $field, string $token): static {
		$this->parts[] = $field .' == '. $token;
		return $this;
	}

	public function contains(string $field, string $token): static {
		$this->parts[] = $field .' contains '. $token;
		return $this;
	}

	public function startsWith(string $field, string $token): static {
		$this->parts[] = $field .' startsWith '. $token;
		return $this;
	}

	public function endsWith(string $field, string $token): static {
		$this->parts[] = $field .' endsWith '. $token;
		return $this;
	}

	public function query(): string {
		$middle = implode(', ', $this->parts);

		return empty($this->group)
			? $middle
			: ($this->group .' ('. $middle .')');
	}

	private function buildGroup(string $group, callable $buildingBlock): static {
		$builder = $this->subBuilder($group);
		$buildingBlock($builder);
		$this->parts[] = $builder->query();

		return $this;
	}

	private function subBuilder(string $group): self {
		$builder = new self();
		$builder->group = $group;

		return $builder;
	}
}
