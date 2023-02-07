<?php

namespace Tests\Unit\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\Builder;
use Artvys\Search\Engines\Compiled\SearchSource;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase {
	private Builder $builder;
	/** @var array<string, SearchSource> */
	private array $sources;

	protected function setUp(): void {
		$this->builder = new Builder();
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
	}

	public function testExample1(): void {
		$result = $this->builder->result();
		$this->assertSame([], $result->bindings());
	}

	public function testExample2(): void {
		$this->builder->addToken('foo');
		$result = $this->builder->result();
		$this->assertSame([], $result->bindings());
	}

	public function testExample3(): void {
		$this->builder->addQuery([]);
		$result = $this->builder->result();
		$this->assertSame([], $result->bindings());
	}

	public function testExample4(): void {
		$this->builder->addToken('foo');
		$this->builder->addQuery([]);
		$result = $this->builder->result();
		$this->assertSame([], $result->bindings());
	}

	public function testExample5(): void {
		$this->builder->addToken('foo');
		$this->builder->addQuery([$this->sources['@']]);
		$result = $this->builder->result();
		$this->assertCount(1, $result->bindings());
		$this->assertSame(['foo'], $result->queries()[0]->tokens());
		$this->assertSame([$this->sources['@']], $result->sources());
	}
}
