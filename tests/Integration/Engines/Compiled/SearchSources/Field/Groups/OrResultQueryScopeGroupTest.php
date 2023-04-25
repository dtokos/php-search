<?php

namespace Tests\Integration\Engines\Compiled\SearchSources\Field\Groups;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Field;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Groups\OrResultQueryScopeGroup;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Engines\Compiled\SearchSources\Field\ResultQueryBuilderStub;

class OrResultQueryScopeGroupTest extends TestCase {
	/**
	 * @param Field[] $fields
	 * @param string $expected
	 * @return void
	 */
	#[DataProvider('example1Provider')]
	public function testExample1(array $fields, string $expected): void {
		$group = new OrResultQueryScopeGroup();

		foreach ($fields as $field)
			$group->add($field);

		$builder = new ResultQueryBuilderStub();
		$group->apply($builder, new CompiledQuery(['lorem']));

		$this->assertSame($expected, $builder->query());
	}

	/** @return array{array{Field[], string}} */
	public static function example1Provider(): array {
		return [
			[[], ''],
			[[Field::equals('foo')], 'OR (foo == lorem)'],
			[[Field::equals('foo'), Field::contains('bar')], 'OR (foo == lorem, bar contains lorem)'],
		];
	}
}
