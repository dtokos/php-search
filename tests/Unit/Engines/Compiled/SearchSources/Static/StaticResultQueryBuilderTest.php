<?php

namespace Tests\Unit\Engines\Compiled\SearchSources\Static;

use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;
use Artvys\Search\Engines\Compiled\SearchSources\Static\StaticResultQueryBuilder;
use Artvys\Search\Result\Breadcrumb;
use Artvys\Search\Result\Link;
use Artvys\Search\Result\Tag;
use Artvys\Search\SearchResult;
use Generator;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StaticResultQueryBuilderTest extends TestCase {
	/**
	 * @param SearchResult[] $expected
	 * @param Generator<SearchResult> $allResults
	 * @param string $field
	 * @return void
	 */
	#[DataProvider('equalsProvider')]
	public function testEquals(array $expected, Generator $allResults, string $field): void {
		$builder = new StaticResultQueryBuilder($allResults);
		$builder->equals($field, 'foo');

		$this->assertSame($expected, $builder->results(10));
	}

	/** @return array{array{SearchResult[], Generator<SearchResult>, string}} */
	private function equalsProvider(): array {
		return [
			$this->provide('title', [0], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('description', [2], ['foobar', 'bar', 'foo', 'baz']),
			$this->provide('url', [2], ['baz', 'foobar', 'foo', 'bar']),
			$this->provide('thumbnailUrl', [3], ['bar', 'baz', 'foobar', 'foo']),
			$this->provide('helpText', [0], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('breadcrumbs.title', [3], ['foobar', 'baz', 'bar', 'foo']),
			$this->provide('breadcrumbs.url', [0], ['foo', 'foobar', 'baz', 'bar']),
			$this->provide('tags.title', [1], ['bar', 'foo', 'foobar', 'baz']),
			$this->provide('tags.url', [1], ['baz', 'foo', 'bar', 'foobar']),
			$this->provide('tags.color', [3], ['foobar', 'bar', 'baz', 'foo']),
			$this->provide('links.title', [0], ['foo', 'foobar', 'bar', 'baz']),
			$this->provide('links.url', [3], ['baz', 'bar', 'foobar', 'foo']),
		];
	}

	/**
	 * @param SearchResult[] $expected
	 * @param Generator<SearchResult> $allResults
	 * @param string $field
	 * @return void
	 */
	#[DataProvider('containsProvider')]
	public function testContains(array $expected, Generator $allResults, string $field): void {
		$builder = new StaticResultQueryBuilder($allResults);
		$builder->contains($field, 'a');

		$this->assertSame($expected, $builder->results(10));
	}

	/** @return array{array{SearchResult[], Generator<SearchResult>, string}} */
	private function containsProvider(): array {
		return [
			$this->provide('title', [1, 2, 3], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('description', [0, 1, 3], ['foobar', 'bar', 'foo', 'baz']),
			$this->provide('url', [0, 1, 3], ['baz', 'foobar', 'foo', 'bar']),
			$this->provide('thumbnailUrl', [0, 1, 2], ['bar', 'baz', 'foobar', 'foo']),
			$this->provide('helpText', [1, 2, 3], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('breadcrumbs.title', [0, 1, 2], ['foobar', 'baz', 'bar', 'foo']),
			$this->provide('breadcrumbs.url', [1, 2, 3], ['foo', 'foobar', 'baz', 'bar']),
			$this->provide('tags.title', [0, 2, 3], ['bar', 'foo', 'foobar', 'baz']),
			$this->provide('tags.url', [0, 2, 3], ['baz', 'foo', 'bar', 'foobar']),
			$this->provide('tags.color', [0, 1, 2], ['foobar', 'bar', 'baz', 'foo']),
			$this->provide('links.title', [1, 2, 3], ['foo', 'foobar', 'bar', 'baz']),
			$this->provide('links.url', [0, 1, 2], ['baz', 'bar', 'foobar', 'foo']),
		];
	}

	/**
	 * @param SearchResult[] $expected
	 * @param Generator<SearchResult> $allResults
	 * @param string $field
	 * @return void
	 */
	#[DataProvider('startsWithProvider')]
	public function testStartsWith(array $expected, Generator $allResults, string $field): void {
		$builder = new StaticResultQueryBuilder($allResults);
		$builder->startsWith($field, 'ba');

		$this->assertSame($expected, $builder->results(10));
	}

	/** @return array{array{SearchResult[], Generator<SearchResult>, string}} */
	private function startsWithProvider(): array {
		return [
			$this->provide('title', [1, 2], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('description', [1, 3], ['foobar', 'bar', 'foo', 'baz']),
			$this->provide('url', [0, 3], ['baz', 'foobar', 'foo', 'bar']),
			$this->provide('thumbnailUrl', [0, 1], ['bar', 'baz', 'foobar', 'foo']),
			$this->provide('helpText', [1, 2], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('breadcrumbs.title', [1, 2], ['foobar', 'baz', 'bar', 'foo']),
			$this->provide('breadcrumbs.url', [2, 3], ['foo', 'foobar', 'baz', 'bar']),
			$this->provide('tags.title', [0, 3], ['bar', 'foo', 'foobar', 'baz']),
			$this->provide('tags.url', [0, 2], ['baz', 'foo', 'bar', 'foobar']),
			$this->provide('tags.color', [1, 2], ['foobar', 'bar', 'baz', 'foo']),
			$this->provide('links.title', [2, 3], ['foo', 'foobar', 'bar', 'baz']),
			$this->provide('links.url', [0, 1], ['baz', 'bar', 'foobar', 'foo']),
		];
	}

	/**
	 * @param SearchResult[] $expected
	 * @param Generator<SearchResult> $allResults
	 * @param string $field
	 * @return void
	 */
	#[DataProvider('endsWithProvider')]
	public function testEndsWith(array $expected, Generator $allResults, string $field): void {
		$builder = new StaticResultQueryBuilder($allResults);
		$builder->endsWith($field, 'oo');

		$this->assertSame($expected, $builder->results(10));
	}

	/** @return array{array{SearchResult[], Generator<SearchResult>, string}} */
	private function endsWithProvider(): array {
		return [
			$this->provide('title', [0], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('description', [2], ['foobar', 'bar', 'foo', 'baz']),
			$this->provide('url', [2], ['baz', 'foobar', 'foo', 'bar']),
			$this->provide('thumbnailUrl', [3], ['bar', 'baz', 'foobar', 'foo']),
			$this->provide('helpText', [0], ['foo', 'bar', 'baz', 'foobar']),
			$this->provide('breadcrumbs.title', [3], ['foobar', 'baz', 'bar', 'foo']),
			$this->provide('breadcrumbs.url', [0], ['foo', 'foobar', 'baz', 'bar']),
			$this->provide('tags.title', [1], ['bar', 'foo', 'foobar', 'baz']),
			$this->provide('tags.url', [1], ['baz', 'foo', 'bar', 'foobar']),
			$this->provide('tags.color', [3], ['foobar', 'bar', 'baz', 'foo']),
			$this->provide('links.title', [0], ['foo', 'foobar', 'bar', 'baz']),
			$this->provide('links.url', [3], ['baz', 'bar', 'foobar', 'foo']),
		];
	}

	public function testAnd(): void {
		$allResults = [
			SearchResult::make('foo', 'foo', ''),
			SearchResult::make('foo', 'bar', ''),
		];

		$builder = new StaticResultQueryBuilder($this->toGenerator($allResults));
		$builder->and(fn(ResultQueryBuilder $b) => $b
			->equals('title', 'foo')
			->equals('description', 'foo')
		);

		$this->assertSame([$allResults[0]], $builder->results(10));
	}

	public function testOr(): void {
		$allResults = [
			SearchResult::make('foo', 'bar', ''),
			SearchResult::make('bar', 'foo', ''),
		];

		$builder = new StaticResultQueryBuilder($this->toGenerator($allResults));
		$builder->or(fn(ResultQueryBuilder $b) => $b
			->equals('title', 'foo')
			->equals('description', 'foo')
		);

		$this->assertSame($allResults, $builder->results(10));
	}

	public function testLimit(): void {
		$allResults = $this->makeResults('title', ['foo', 'bar', 'baz', 'foobar']);

		$builder = new StaticResultQueryBuilder($this->toGenerator($allResults));
		$builder->contains('title', 'a');

		$this->assertSame([$allResults[1], $allResults[2]], $builder->results(2));
	}

	/**
	 * @param string $field
	 * @param string[] $values
	 * @return SearchResult[]
	 */
	private function makeResults(string $field, array $values): array {
		return array_map(fn(string $value) => $this->makeResult($field, $value), $values);
	}

	private function makeResult(string $field, string $value): SearchResult {
		$r = SearchResult::make('', '', '');

		return match ($field) {
			'title' => $r->setTitle($value),
			'description' => $r->setDescription($value),
			'url' => $r->setUrl($value),
			'thumbnailUrl' => $r->setThumbnailUrl($value),
			'helpText' => $r->setHelpText($value),
			'breadcrumbs.title' => $r->appendBreadcrumb(Breadcrumb::make($value, '')),
			'breadcrumbs.url' => $r->appendBreadcrumb(Breadcrumb::make('', $value)),
			'tags.title' => $r->appendTag(Tag::make($value, '')),
			'tags.url' => $r->appendTag(Tag::make('', $value)),
			'tags.color' => $r->appendTag(Tag::make('', '', $value)),
			'links.title' => $r->appendLink(Link::make($value, '')),
			'links.url' => $r->appendLink(Link::make('', $value)),
			default => throw new LogicException('Unknown field'),
		};
	}

	/**
	 * @param string $field
	 * @param int[] $expected
	 * @param string[] $values
	 * @return array{SearchResult[], Generator<SearchResult>, string}
	 */
	private function provide(string $field, array $expected, array $values): array {
		return $this->provideResults($field, $expected, $this->makeResults($field, $values));
	}

	/**
	 * @param string $field
	 * @param int[] $expected
	 * @param SearchResult[] $allResults
	 * @return array{SearchResult[], Generator<SearchResult>, string}
	 */
	private function provideResults(string $field, array $expected, array $allResults): array {
		return [
			$this->pick($expected, $allResults),
			$this->toGenerator($allResults),
			$field,
		];
	}

	/**
	 * @param int[] $expected
	 * @param SearchResult[] $allResults
	 * @return SearchResult[]
	 */
	private function pick(array $expected, array $allResults): array {
		return array_map(fn(int $index) => $allResults[$index], $expected);
	}

	/**
	 * @param SearchResult[] $results
	 * @return Generator<SearchResult>
	 */
	private function toGenerator(array $results): Generator {
		foreach ($results as $result) yield $result;
	}
}
