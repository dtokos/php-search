<?php

namespace Tests\Unit\Engines\Compiled;

use Artvys\Search\Engines\Compiled\CompiledBinding;
use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSource;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CompiledBindingTest extends TestCase {
	private CompiledQuery $query;
	/** @var array<string, SearchSource> */
	private array $sources;
	private CompiledBinding $binding;

	/**
	 * @throws Exception
	 */
	protected function setUp(): void {
		$this->query = new CompiledQuery(['foo', 'bar', 'baz']);
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
		$this->binding = new CompiledBinding($this->query, $this->sources);
	}

	public function testQuery(): void {
		$this->assertSame($this->query, $this->binding->query());
	}

	public function testSources(): void {
		$this->assertSame($this->sources, $this->binding->sources());
	}
}
