<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\CompiledQuery;

interface ResultQueryScope {
	public function apply(ResultQueryBuilder $builder, CompiledQuery $query): ResultQueryBuilder;
}
