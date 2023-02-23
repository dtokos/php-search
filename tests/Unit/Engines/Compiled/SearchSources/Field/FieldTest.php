<?php

namespace Tests\Unit\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Field;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Engines\Compiled\SearchSources\Field\ResultQueryBuilderStub;

class FieldTest extends TestCase {
	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('startsWithProvider')]
	public function testStartsWith(array $tokens, string $expected): void {
		$this->assertResult(Field::startsWith('foo'), $tokens, $expected);
	}

	/** @return array{array{string[], string}} */
	public function startsWithProvider(): array {
		return [
			[[], ''],
			[['lorem'], 'foo startsWith lorem'],
			[['lorem', 'ipsum'], 'OR (foo startsWith lorem, foo startsWith ipsum)'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('equalsProvider')]
	public function testEq(array $tokens, string $expected): void {
		$this->assertResult(Field::eq('foo'), $tokens, $expected);
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('equalsProvider')]
	public function testEquals(array $tokens, string $expected): void {
		$this->assertResult(Field::equals('foo'), $tokens, $expected);
	}

	/** @return array{array{string[], string}} */
	public function equalsProvider(): array {
		return [
			[[], ''],
			[['lorem'], 'foo == lorem'],
			[['lorem', 'ipsum'], 'OR (foo == lorem, foo == ipsum)'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('endsWithProvider')]
	public function testEndsWith(array $tokens, string $expected): void {
		$this->assertResult(Field::endsWith('foo'), $tokens, $expected);
	}

	/** @return array{array{string[], string}} */
	public function endsWithProvider(): array {
		return [
			[[], ''],
			[['lorem'], 'foo endsWith lorem'],
			[['lorem', 'ipsum'], 'OR (foo endsWith lorem, foo endsWith ipsum)'],
		];
	}

	/**
	 * @param string[] $tokens
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('containsProvider')]
	public function testContains(array $tokens, string $expected): void {
		$this->assertResult(Field::contains('foo'), $tokens, $expected);
	}

	/** @return array{array{string[], string}} */
	public function containsProvider(): array {
		return [
			[[], ''],
			[['lorem'], 'foo contains lorem'],
			[['lorem', 'ipsum'], 'OR (foo contains lorem, foo contains ipsum)'],
		];
	}

	private function assertResult(Field $field, array $tokens, string $expected): void {
		$builder = new ResultQueryBuilderStub();
		$field->apply($builder, new CompiledQuery($tokens));
		$this->assertSame($expected, $builder->query());
	}
}
