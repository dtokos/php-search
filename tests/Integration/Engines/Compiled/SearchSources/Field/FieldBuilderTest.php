<?php

namespace Tests\Integration\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Field;
use Artvys\Search\Engines\Compiled\SearchSources\Field\FieldBuilder;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ScopeGroupFactory;
use Artvys\Search\Engines\Compiled\SearchSources\Field\SearchFieldBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Engines\Compiled\SearchSources\Field\ResultQueryBuilderStub;

class FieldBuilderTest extends TestCase {
	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('andProvider')]
	public function testAnd(array $tokens, string $expected): void {
		$this->assertResult($expected, $tokens, fn(SearchFieldBuilder $b) => $b
			->add(Field::equals('foo'))
			->add(Field::contains('bar'))
		);
	}

	/** @return array{array{string[], string}} */
	public function andProvider(): array {
		return [
			[[], 'AND ()'],
			[['lorem'], 'AND (foo == lorem, bar contains lorem)'],
			[['lorem', 'ipsum'], 'AND (OR (foo == lorem, foo == ipsum), OR (bar contains lorem, bar contains ipsum))'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('orProvider')]
	public function testOr(array $tokens, string $expected): void {
		$this->assertResult($expected, $tokens, fn(SearchFieldBuilder $b) => $b
			->add(Field::equals('foo'))
			->add(Field::contains('bar'))
		, false);
	}

	/** @return array{array{string[], string}} */
	public function orProvider(): array {
		return [
			[[], 'OR ()'],
			[['lorem'], 'OR (foo == lorem, bar contains lorem)'],
			[['lorem', 'ipsum'], 'OR (OR (foo == lorem, foo == ipsum), OR (bar contains lorem, bar contains ipsum))'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('unlessProvider')]
	public function testUnless(array $tokens, string $expected): void {
		$this->assertResult($expected, $tokens, fn(SearchFieldBuilder $b) => $b
			->unless(true, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::equals('foo'))
			)
			->unless(false, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::contains('bar'))
			)
			->unless(fn() => true, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::startsWith('baz'))
			)
			->unless(fn() => false, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::endsWith('qux'))
			)
		);
	}

	/** @return array{array{string[], string}} */
	public function unlessProvider(): array {
		return [
			[[], 'AND ()'],
			[['lorem'], 'AND (bar contains lorem, qux endsWith lorem)'],
			[['lorem', 'ipsum'], 'AND (OR (bar contains lorem, bar contains ipsum), OR (qux endsWith lorem, qux endsWith ipsum))'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('ifProvider')]
	public function testIf(array $tokens, string $expected): void {
		$this->assertResult($expected, $tokens, fn(SearchFieldBuilder $b) => $b
			->if(true, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::equals('foo'))
			)
			->if(false, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::contains('bar'))
			)
			->if(fn() => true, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::startsWith('baz'))
			)
			->if(fn() => false, fn(SearchFieldBuilder $ub) => $ub
				->add(Field::endsWith('qux'))
			)
		);
	}

	/** @return array{array{string[], string}} */
	public function ifProvider(): array {
		return [
			[[], 'AND ()'],
			[['lorem'], 'AND (foo == lorem, baz startsWith lorem)'],
			[['lorem', 'ipsum'], 'AND (OR (foo == lorem, foo == ipsum), OR (baz startsWith lorem, baz startsWith ipsum))'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('example1Provider')]
	public function testExample1(array $tokens, string $expected): void {
		$this->assertResult($expected, $tokens, fn(SearchFieldBuilder $b1) => $b1
			->add(Field::eq('foo'))
			->and(fn(SearchFieldBuilder $b2) => $b2
				->if(1, fn(SearchFieldBuilder $b2) => $b2
					->unless(0, fn(SearchFieldBuilder $b3) => $b3
						->or(fn(SearchFieldBuilder $b4) => $b4
							->add(Field::contains('bar'))
							->add(Field::startsWith('baz'))
						)
					)
				)
			)
		);
	}

	/** @return array{array{string[], string}} */
	public function example1Provider(): array {
		return [
			[[], 'AND (AND (OR ()))'],
			[['lorem'], 'AND (foo == lorem, AND (OR (bar contains lorem, baz startsWith lorem)))'],
			[['lorem', 'ipsum'], 'AND (OR (foo == lorem, foo == ipsum), AND (OR (OR (bar contains lorem, bar contains ipsum), OR (baz startsWith lorem, baz startsWith ipsum))))'],
		];
	}

	/**
	 * @param string $expected
	 * @param string[] $tokens
	 * @param callable(SearchFieldBuilder): SearchFieldBuilder $buildingBlock
	 * @param bool $rootAnd
	 * @return void
	 */
	private function assertResult(string $expected, array $tokens, callable $buildingBlock, bool $rootAnd = true): void {
		$factory = new ScopeGroupFactory();
		$builder = new FieldBuilder($factory, $rootAnd ? $factory->makeAnd() : $factory->makeOr());
		$buildingBlock($builder);

		$queryBuilder = new ResultQueryBuilderStub();
		$builder->build()->apply($queryBuilder, new CompiledQuery($tokens));

		$this->assertSame($expected, $queryBuilder->query());
	}
}
