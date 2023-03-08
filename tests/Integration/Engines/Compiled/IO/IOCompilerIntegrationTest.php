<?php

namespace Tests\Integration\Engines\Compiled\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompiler;
use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompilerFactory;
use Artvys\Search\Engines\Compiled\SearchSource;
use Artvys\Search\Engines\Compiled\SearchSourceRegistry;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class IOCompilerIntegrationTest extends TestCase {
	/** @var array<string, SearchSource> */
	private array $sources;
	private IOCompiler $compiler;

	/**
	 * @throws Exception
	 */
	protected function setUp(): void {
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
		$registry = new SearchSourceRegistry();
		$registry->register($this->sources['@'], ['@']);
		$registry->register($this->sources['#'], ['#']);
		$registry->register($this->sources['api'], ['api'], false);
		$this->compiler = (new IOCompilerFactory($registry))->make();
	}

	public function testExample1(): void {
		$result = $this->compiler->compile('');
		$this->assertSame([], $result->bindings());
	}

	public function testExample2(): void {
		$this->assertResult('foo', ['foo'], ['@', '#']);
	}

	public function testExample3(): void {
		$this->assertResult(" foo\tbar ", ['foo', 'bar'], ['@', '#']);
	}

	public function testExample4(): void {
		$this->assertResult('@foo', ['foo'], ['@']);
	}

	public function testExample5(): void {
		$this->assertResult('#@foo', ['foo'], ['#', '@']);
	}

	public function testExample6(): void {
		$this->assertResult('api foo', ['foo'], ['api']);
	}

	public function testExample7(): void {
		$this->assertResult('api:foo', ['foo'], ['api']);
	}

	public function testExample8(): void {
		$this->assertResult('api,@#: foo', ['foo'], ['api', '@', '#']);
	}

	public function testExample9(): void {
		$this->assertResult(',foo', [',', 'foo'], ['@', '#']);
	}

	public function testExample10(): void {
		$this->assertResult(':foo', [':', 'foo'], ['@', '#']);
	}

	public function testExample11(): void {
		$this->assertResult('#$foo', ['$', 'foo'], ['#']);
	}

	public function testExample12(): void {
		$this->assertResult('@,#,api foo', ['foo'], ['@', '#', 'api']);
	}

	/**
	 * @param string $query
	 * @param string[] $tokens
	 * @param string[] $aliases
	 * @return void
	 */
	private function assertResult(string $query, array $tokens, array $aliases): void {
		$result = $this->compiler->compile($query);
		$sources = array_map(fn(string $alias) => $this->sources[$alias], $aliases);

		$this->assertCount(1, $result->bindings());
		$this->assertSame($tokens, $result->bindings()[0]->query()->tokens());
		$this->assertSame($sources, $result->bindings()[0]->sources());
	}
}
