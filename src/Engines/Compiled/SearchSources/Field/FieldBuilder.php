<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

class FieldBuilder implements SearchFieldBuilder {
	private readonly ResultQueryScopeGroupFactory $factory;
	private ResultQueryScopeGroup $group;

	public function __construct(ResultQueryScopeGroupFactory $factory, ResultQueryScopeGroup $group) {
		$this->factory = $factory;
		$this->group = $group;
	}

	/** @inheritDoc */
	public function and(callable $buildingBlock): static {
		$builder = $this->subBuilder($this->factory->makeAnd());
		$buildingBlock($builder);
		$this->group->add($builder->build());

		return $this;
	}

	/** @inheritDoc */
	public function or(callable $buildingBlock): static {
		$builder = $this->subBuilder($this->factory->makeOr());
		$buildingBlock($builder);
		$this->group->add($builder->build());

		return $this;
	}

	/** @inheritDoc */
	public function if(mixed $condition, callable $buildingBlock): static {
		if ($this->evaluate($condition))
			$buildingBlock($this);

		return $this;
	}

	/** @inheritDoc */
	public function unless(mixed $condition, callable $buildingBlock): static {
		if (!$this->evaluate($condition))
			$buildingBlock($this);

		return $this;
	}

	public function add(SearchField $field): static {
		$this->group->add($field);
		return $this;
	}

	public function build(): ResultQueryScope {
		return $this->group;
	}

	protected function subBuilder(ResultQueryScopeGroup $group): SearchFieldBuilder {
		return new self($this->factory, $group);
	}

	protected function evaluate(mixed $condition): bool {
		$value = is_callable($condition) ? $condition() : $condition;
		return !!$value;
	}
}
