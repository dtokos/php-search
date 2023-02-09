<?php

namespace Tests\Unit\Engines\Compiled\FetchingStrategies;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\CompiledBinding;
use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\FetchingStrategies\FirstFitFetchingStrategy;
use Artvys\Search\Engines\Compiled\SearchSource;
use Artvys\Search\SearchResult;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class FirstFitFetchingStrategyTest extends TestCase {
	private FirstFitFetchingStrategy $strategy;
	/** @var array<string, SearchSource&Stub> */
	private array $sources;

	protected function setUp(): void {
		$this->strategy = new FirstFitFetchingStrategy();
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
		];
	}

	public function testExample1(): void {
		$results = $this->strategy->fetch(new CompilationResult([]), 10);
		$this->assertSame([], $results);
	}

	public function testExample2(): void {
		$this->assertResults(['Foo', 'Bar'], ['Lorem', 'Ipsum'], 10, 4);
	}

	public function testExample3(): void {
		$this->assertResults([], ['Foo', 'Bar', 'Lorem'], 3, 3);
	}

	public function testExample4(): void {
		$this->assertResults(['Foo', 'Bar'], ['Lorem', 'Ipsum'], 2, 2);
	}

	/**
	 * @param string[] $users
	 * @param string[] $invoices
	 * @param int $limit
	 * @param int $expectedCount
	 * @return void
	 */
	private function assertResults(array $users, array $invoices, int $limit, int $expectedCount): void {
		$userResults = array_map(fn(string $user) => new SearchResult($user, '', ''), $users);
		$this->sources['@']->method('search')->willReturn($userResults);
		$invoiceResults = array_map(fn(string $invoice) => new SearchResult($invoice, '', ''), $invoices);
		$this->sources['#']->method('search')->willReturn($invoiceResults);

		$expectedResults = array_slice([...$userResults, ...$invoiceResults], 0, $expectedCount);

		$results = $this->strategy->fetch(
			new CompilationResult([new CompiledBinding(new CompiledQuery([]), $this->sources)]),
			$limit
		);

		$this->assertSame($expectedResults, $results);
	}
}
