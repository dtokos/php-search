<?php

namespace Tests\Unit\Engines\Compiled;

use Artvys\Search\Engines\Compiled\SearchSource;
use Artvys\Search\Engines\Compiled\SearchSourceRegistry;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class SearchSourceRegistryTest extends TestCase {
	/** @var array<string, SearchSource> */
	private array $sources;
	private SearchSourceRegistry $registry;

	/**
	 * @throws Exception
	 */
	protected function setUp(): void {
		$this->sources = [
			'@' => $this->createStub(SearchSource::class),
			'#' => $this->createStub(SearchSource::class),
			'api' => $this->createStub(SearchSource::class),
		];
		$this->registry = (new SearchSourceRegistry())
			->register($this->sources['@'], ['@'])
			->register($this->sources['#'], ['#'])
			->register($this->sources['api'], ['api'], false);
	}

	public function testHas(): void {
		$this->assertTrue($this->registry->has('@'));
	}

	public function testFor(): void {
		$this->assertSame([
			$this->sources['@'],
			$this->sources['#'],
			$this->sources['api'],
		], $this->registry->for(['@', '#', 'api']));
	}

	public function testAll(): void {
		$this->assertSame([
			$this->sources['@'],
			$this->sources['#'],
			$this->sources['api'],
		], $this->registry->all());
	}

	public function testUnaliased(): void {
		$this->assertSame([
			$this->sources['@'],
			$this->sources['#'],
		], $this->registry->unaliased());
	}

	public function testForExample1(): void {
		$this->assertSame([
			$this->sources['#'],
			$this->sources['@'],
		], $this->registry->for(['#', '@']));
	}

	public function testForExample2(): void {
		$this->assertSame([
			$this->sources['api'],
			$this->sources['@'],
			$this->sources['#'],
		], $this->registry->for(['api', '@', '#']));
	}
}
