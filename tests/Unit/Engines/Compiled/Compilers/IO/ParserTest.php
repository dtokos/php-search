<?php

namespace Tests\Unit\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\Parser;
use Artvys\Search\Engines\Compiled\Compilers\IO\SearchSourceProvider;
use Artvys\Search\Engines\Compiled\SearchSource;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Engines\Compiled\Compilers\IO\ResultBuilderStub;

class ParserTest extends TestCase {
	/** @var array<string, SearchSource> */
	private array $sources = [];
	private ResultBuilderStub $builder;
	private Parser $parser;

	protected function setUp(): void {
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
		$this->builder = new ResultBuilderStub();
		$provider = $this->createStub(SearchSourceProvider::class);
		$provider->method('has')->willReturnCallback($this->has(...));
		$provider->method('unaliased')->willReturn($this->sources(['@', '#']));
		$provider->method('for')->willReturnCallback($this->sources(...));
		$this->parser = new Parser($this->builder, $provider);
	}

	public function testExample1(): void {
		$this->parser->eof();
		$this->assertSame([], $this->builder->queries);
	}

	public function testExample2(): void {
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['@', '#']);
	}

	public function testExample3(): void {
		$this->parser->token('foo');
		$this->parser->token('bar');
		$this->parser->eof();
		$this->assertResult(['foo', 'bar'], ['@', '#']);
	}

	public function testExample4(): void {
		$this->parser->symbol('@');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['@']);
	}

	public function testExample5(): void {
		$this->parser->symbol('@');
		$this->parser->symbol('#');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['@', '#']);
	}

	public function testExample6(): void {
		$this->parser->token('api');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['api']);
	}

	public function testExample7(): void {
		$this->parser->token('api');
		$this->parser->colon(':');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['api']);
	}

	public function testExample8(): void {
		$this->parser->token('api');
		$this->parser->comma(',');
		$this->parser->symbol('@');
		$this->parser->symbol('#');
		$this->parser->colon(':');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['api', '@', '#']);
	}

	public function testExample9(): void {
		$this->parser->comma(',');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult([',', 'foo'], ['@', '#']);
	}

	public function testExample10(): void {
		$this->parser->colon(':');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult([':', 'foo'], ['@', '#']);
	}

	public function testExample11(): void {
		$this->parser->symbol('#');
		$this->parser->symbol('$');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['$', 'foo'], ['#']);
	}

	public function testExample12(): void {
		$this->parser->symbol('@');
		$this->parser->comma(',');
		$this->parser->symbol('#');
		$this->parser->comma(',');
		$this->parser->token('api');
		$this->parser->token('foo');
		$this->parser->eof();
		$this->assertResult(['foo'], ['@', '#', 'api']);
	}

	/**
	 * @param string[] $tokens
	 * @param string[] $aliases
	 * @return void
	 */
	private function assertResult(array $tokens, array $aliases): void {
		$this->assertSame([['query' => $tokens, 'sources' => $this->sources($aliases)]], $this->builder->queries);
	}

	/**
	 * @param string[] $aliases
	 * @return SearchSource[]
	 */
	private function sources(array $aliases): array {
		$result = [];

		foreach ($aliases as $alias) {
			if ($source = $this->sources[$alias] ?? false)
				$result[] = $source;
		}

		return $result;
	}

	private function has(string $alias): bool {
		return array_key_exists($alias, $this->sources);
	}
}
