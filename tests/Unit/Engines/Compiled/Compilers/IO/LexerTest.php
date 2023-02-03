<?php

namespace Tests\Unit\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\Compilers\IO\Lexer;
use Artvys\Search\Engines\Compiled\Compilers\IO\TokenCollector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase {
	private const SYMBOLS = [
		'!', '#', '$', '%', '&', '*', '+', '-', '.', '/', ';', '<', '=', '>', '?', '@', '\\', '^', '_', '|', '~',
	];
	private TokenCollectorStub $collector;
	private Lexer&MockObject $lexer;

	protected function setUp(): void {
		$this->collector = new TokenCollectorStub();
		$this->lexer = $this->getMockBuilder(Lexer::class)
			->setConstructorArgs([$this->collector])
			->onlyMethods(['failsafeSkip'])
			->getMock();

		$this->lexer->expects($this->never())->method('failsafeSkip');
	}

	public function testProcessEmptyQuery(): void {
		$this->lexer->process('');
		$this->assertSame(['eof'], $this->collector->tokens);
	}

	public function testProcessWhiteSpaceQuery(): void {
		$this->lexer->process(" \t\n");
		$this->assertSame(['eof'], $this->collector->tokens);
	}

	#[DataProvider('symbolsProvider')]
	public function testProcessSingleSymbol(string $symbol): void {
		$this->lexer->process($symbol);
		$this->assertSame([$symbol, 'eof'], $this->collector->tokens);
	}

	/** @return array<int, string[]> */
	public static function symbolsProvider(): array {
		return array_map(fn(string $symbol) => [$symbol], self::SYMBOLS);
	}

	public function testProcessMultipleSymbols(): void {
		$this->lexer->process('!#$%&*+-./;<=>?@\\^_|~');
		$this->assertSame([...self::SYMBOLS, 'eof'], $this->collector->tokens);
	}

	#[DataProvider('tokensProvider')]
	public function testProcessSingleToken(string $token): void {
		$this->lexer->process($token);
		$this->assertSame([$token, 'eof'], $this->collector->tokens);
	}

	/** @return array<int, string[]> */
	public static function tokensProvider(): array {
		return [['foo'], ['bar'], ['bar'], ['lorem'], ['ipsum']];
	}

	public function testProcessMultipleTokens(): void {
		$this->lexer->process('foo bar baz lorem ipsum');
		$this->assertSame(['foo', 'bar', 'baz', 'lorem', 'ipsum', 'eof'], $this->collector->tokens);
	}

	public function testProcessExample1(): void {
		$this->lexer->process('@user');
		$this->assertSame(['@', 'user', 'eof'], $this->collector->tokens);
	}

	public function testProcessExample2(): void {
		$this->lexer->process('@#user or ticket');
		$this->assertSame(['@', '#', 'user', 'or', 'ticket', 'eof'], $this->collector->tokens);
	}

	public function testProcessExample3(): void {
		$this->lexer->process('users,tickets: user or ticket');
		$this->assertSame(['users', ',', 'tickets', ':', 'user', 'or', 'ticket', 'eof'], $this->collector->tokens);
	}
}
