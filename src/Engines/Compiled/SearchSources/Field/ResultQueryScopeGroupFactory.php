<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

interface ResultQueryScopeGroupFactory {
	public function makeAnd(): ResultQueryScopeGroup;
	public function makeOr(): ResultQueryScopeGroup;
}
