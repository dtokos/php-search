<?php

namespace Tests\Unit\Engines\Compiled;

use Artvys\Search\Engines\Compiled\CompiledSearchEngine;
use Artvys\Search\Engines\Compiled\CompilerFactory;
use Artvys\Search\Engines\Compiled\FetchingStrategy;
use Artvys\Search\SearchResult;
use PHPUnit\Framework\TestCase;

class CompiledSearchEngineTest extends TestCase {
	public function testSearch(): void {
		$expected = $this->makeResults();
		$factory = $this->createStub(CompilerFactory::class);
		$strategy = $this->createStub(FetchingStrategy::class);
		$strategy->method('fetch')->willReturn($expected);

		$engine = new CompiledSearchEngine($factory, $strategy);
		$results = $engine->search('foo', 10);
		$this->assertSame($expected, $results);
	}

	/** @return SearchResult[] */
	private function makeResults(): array {
		return [
			SearchResult::make('foo', 'foo', 'https://foo.foo'),
		];
	}
}
