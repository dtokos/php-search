<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field\Groups;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryScope;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryScopeGroup;

class OrResultQueryScopeGroup implements ResultQueryScopeGroup {
	/** @var ResultQueryScope[] */
	protected array $scopes = [];

	public function add(ResultQueryScope $scope): static {
		$this->scopes[] = $scope;
		return $this;
	}

	public function apply(ResultQueryBuilder $builder, CompiledQuery $query): ResultQueryBuilder {
		if (!empty($this->scopes)) {
			$builder->or(function(ResultQueryBuilder $b) use ($query) {
				foreach ($this->scopes as $scope)
					$scope->apply($b, $query);

				return $b;
			});
		}

		return $builder;
	}
}
