<?php

namespace Tests\Integration\Engines\Compiled;

use Artvys\Search\Engines\Compiled\CompiledSearchEngine;
use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompilerFactory;
use Artvys\Search\Engines\Compiled\FetchingStrategies\FirstFitFetchingStrategy;
use Artvys\Search\Engines\Compiled\SearchSourceRegistry;
use Artvys\Search\SearchResult;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Engines\Compiled\SearchSources\Static\InvoicesSearchSourceStub;
use Tests\Stubs\Engines\Compiled\SearchSources\Static\UsersSearchSourceStub;

class CompiledSearchEngineTest extends TestCase {
	private UsersSearchSourceStub $usersSource;
	private InvoicesSearchSourceStub $invoicesSource;
	private CompiledSearchEngine $engine;

	protected function setUp(): void {
		$this->usersSource = new UsersSearchSourceStub();
		$this->invoicesSource = new InvoicesSearchSourceStub();

		$registry = new SearchSourceRegistry();
		$registry->register($this->usersSource, ['@']);
		$registry->register($this->invoicesSource, ['#']);
		$factory = new IOCompilerFactory($registry);
		$this->engine = new CompiledSearchEngine($factory, new FirstFitFetchingStrategy());
	}

	public function testExample1(): void {
		$results = $this->engine->search('foo', 10);
		$this->assertSame([...$this->users('foo'), ...$this->invoices('0001', '0005', '0007')], $results);
	}

	public function testExample2(): void {
		$results = $this->engine->search('@foo', 10);
		$this->assertSame($this->users('foo'), $results);
	}

	public function testExample3(): void {
		$results = $this->engine->search('#0005', 10);
		$this->assertSame($this->invoices('0005'), $results);
	}

	public function testExample4(): void {
		$results = $this->engine->search('@IT', 10);
		$this->assertSame($this->users('bar', 'qux'), $results);
	}

	public function testExample5(): void {
		$results = $this->engine->search('@#bar', 10);
		$this->assertSame([...$this->users('bar'), ...$this->invoices('0002', '0006')], $results);
	}

	public function testExample6(): void {
		$results = $this->engine->search('#,@baz', 10);
		$this->assertSame([...$this->invoices('0003', '0008', '0009'), ...$this->users('baz')], $results);
	}

	public function testExample7(): void {
		$results = $this->engine->search('foo', 1);
		$this->assertSame($this->users('foo'), $results);
	}

	public function testExample8(): void {
		$results = $this->engine->search('#IT', 10);
		$this->assertSame([], $results);
	}

	public function testExample9(): void {
		$results = $this->engine->search('04', 10);
		$this->assertSame($this->invoices('0002', '0003', '0004', '0006'), $results);
	}

	/**
	 * @param string ...$keys
	 * @return SearchResult[]
	 */
	private function users(string ...$keys): array {
		return array_map(fn(string $key) => $this->usersSource->allResults[$key], $keys);
	}

	/**
	 * @param string ...$keys
	 * @return SearchResult[]
	 */
	private function invoices(string ...$keys): array {
		return array_map(fn(string $key) => $this->invoicesSource->allResults[$key], $keys);
	}
}
