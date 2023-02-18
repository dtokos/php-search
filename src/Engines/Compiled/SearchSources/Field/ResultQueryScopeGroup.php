<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

interface ResultQueryScopeGroup extends ResultQueryScope {
	public function add(ResultQueryScope $scope): static;
}
