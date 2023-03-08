<?php

namespace Tests\Stubs\Engines\Compiled\SearchSources\Static;

use Artvys\Search\Engines\Compiled\SearchSources\Static\StaticSearchSource;
use Artvys\Search\SearchResult;
use Generator;

class UsersSearchSourceStub extends StaticSearchSource {
	/** @var SearchResult[] */
	public array $allResults;

	public function __construct() {
		$this->allResults = [
			'foo' => SearchResult::make('Foo', 'Occupation: Entertainment', 'https://foo.foo'),
			'bar' => SearchResult::make('Bar', 'Occupation: IT', 'https://bar.bar'),
			'baz' => SearchResult::make('Baz', 'Occupation: Real estate', 'https://baz.baz'),
			'qux' => SearchResult::make('Qux', 'Occupation: IT', 'https://qux.qux'),
		];
	}

	/** @inheritDoc */
	protected function allResults(): Generator {
		foreach ($this->allResults as $result) yield $result;
	}
}
