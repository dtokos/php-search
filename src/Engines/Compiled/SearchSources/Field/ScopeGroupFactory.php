<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\SearchSources\Field\Groups\AndResultQueryScopeGroup;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Groups\OrResultQueryScopeGroup;

class ScopeGroupFactory implements ResultQueryScopeGroupFactory {
	public function makeAnd(): ResultQueryScopeGroup {
		return new AndResultQueryScopeGroup();
	}

	public function makeOr(): ResultQueryScopeGroup {
		return new OrResultQueryScopeGroup();
	}
}
