<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Static;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Field;
use Artvys\Search\Engines\Compiled\SearchSources\Field\FieldSearchSource;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;
use Artvys\Search\Engines\Compiled\SearchSources\Field\SearchFieldBuilder;
use Artvys\Search\SearchResult;
use Generator;

abstract class StaticSearchSource extends FieldSearchSource {
	protected function fields(SearchFieldBuilder $builder, CompiledQuery $query, int $limit): void {
		$builder->add(Field::contains('title'))
			->add(Field::contains('description'))
			->add(Field::contains('helpText'));
	}

	protected function makeResultQueryBuilder(): ResultQueryBuilder {
		return new StaticResultQueryBuilder($this->allResults());
	}

	/** @return Generator<SearchResult> */
	abstract protected function allResults(): Generator;
}
