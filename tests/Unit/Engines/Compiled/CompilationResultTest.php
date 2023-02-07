<?php

namespace Tests\Unit\Engines\Compiled;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\CompiledBinding;
use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSource;
use PHPUnit\Framework\TestCase;

class CompilationResultTest extends TestCase {
	/** @var array<string, SearchSource> */
	private array $sources;
	/** @var CompiledBinding[] */
	private array $bindings;
	private CompilationResult $result;

	protected function setUp(): void {
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
		$this->bindings = [
			new CompiledBinding(new CompiledQuery(['foo']), [$this->sources['@']]),
			new CompiledBinding(new CompiledQuery(['bar', 'baz']), [$this->sources['#']]),
			new CompiledBinding(new CompiledQuery(['lorem', 'ipsum']), [$this->sources['@'], $this->sources['api']]),
		];
		$this->result = new CompilationResult($this->bindings);
	}

	public function testBindings(): void {
		$this->assertSame($this->bindings, $this->result->bindings());
	}

	public function testSources(): void {
		$this->assertSame([
			$this->sources['@'],
			$this->sources['#'],
			$this->sources['@'],
			$this->sources['api'],
		], $this->result->sources());
	}

	public function testQueries(): void {
		$this->assertSame([
			$this->bindings[0]->query(),
			$this->bindings[1]->query(),
			$this->bindings[2]->query(),
		], $this->result->queries());
	}
}
