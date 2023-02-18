<?php

namespace Tests\Unit\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\SearchSources\Field\ScopeGroupFactory;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Groups\AndResultQueryScopeGroup;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Groups\OrResultQueryScopeGroup;
use PHPUnit\Framework\TestCase;

class ScopeGroupFactoryTest extends TestCase {
	private ScopeGroupFactory $factory;

	protected function setUp(): void {
		$this->factory = new ScopeGroupFactory();
	}

	public function testMakeAnd(): void {
		$this->assertInstanceOf(AndResultQueryScopeGroup::class, $this->factory->makeAnd());
	}

	public function testMakeOr(): void {
		$this->assertInstanceOf(OrResultQueryScopeGroup::class, $this->factory->makeOr());
	}
}
