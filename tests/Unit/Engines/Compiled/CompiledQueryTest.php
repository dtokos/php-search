<?php

namespace Tests\Unit\Engines\Compiled;

use Artvys\Search\Engines\Compiled\CompiledQuery;
use PHPUnit\Framework\TestCase;

class CompiledQueryTest extends TestCase {
	private CompiledQuery $query;

	protected function setUp(): void {
		$this->query = new CompiledQuery(['foo', 'bar', 'baz']);
	}

	public function testText(): void {
		$this->assertSame('foo bar baz', $this->query->text());
	}

	public function testTokens(): void {
		$this->assertSame(['foo', 'bar', 'baz'], $this->query->tokens());
	}

	public function testJoined(): void {
		$this->assertSame('foo,bar,baz', $this->query->joined(','));
	}
}
