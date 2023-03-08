<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSource;
use Artvys\Search\SearchResult;

abstract class FieldSearchSource implements SearchSource {
	/** @inheritDoc */
	public function search(CompiledQuery $query, int $limit): array {
		$transformedQuery = $this->transform($query, $limit);

		if (!$this->shouldContinue($transformedQuery, $limit)) {
			return [];
		}

		$scopes = $this->buildQueryScopes($transformedQuery, $limit);

		return $this->results($scopes, $query, $limit);
	}

	protected function transform(CompiledQuery $query, int $limit): CompiledQuery {
		return $query;
	}

	protected function shouldContinue(CompiledQuery $query, int $limit): bool {
		return true;
	}

	protected function buildQueryScopes(CompiledQuery $query, int $limit): ResultQueryScope {
		$builder = $this->makeFieldBuilder($query, $limit);
		$this->fields($builder, $query, $limit);

		return $builder->build();
	}

	protected function makeFieldBuilder(CompiledQuery $query, int $limit): SearchFieldBuilder {
		$factory = new ScopeGroupFactory();
		return new FieldBuilder($factory, $factory->makeOr());
	}

	abstract protected function fields(SearchFieldBuilder $builder, CompiledQuery $query, int $limit): void;

	/**
	 * @param ResultQueryScope $scopes
	 * @param CompiledQuery $query
	 * @param int $limit
	 * @return SearchResult[]
	 */
	protected function results(ResultQueryScope $scopes, CompiledQuery $query, int $limit): array {
		$builder = $this->makeResultQueryBuilder();
		$scopes->apply($builder, $query);

		return $builder->results($limit);
	}

	abstract protected function makeResultQueryBuilder(): ResultQueryBuilder;
}
