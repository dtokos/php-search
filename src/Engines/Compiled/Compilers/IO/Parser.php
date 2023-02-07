<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

class Parser implements TokenCollector {
	private const S_START = 's_start';
	private const S_SHORT_ALIAS = 's_short_alias';
	private const S_ALIAS = 's_alias';
	private const S_COMMA = 's_comma';
	private const S_QUERY = 's_query';
	private const S_END = 's_end';

	private const E_SHORT_ALIAS = 'e_short_alias';
	private const E_ALIAS = 'e_alias';
	private const E_TOKEN = 'e_token';
	private const E_COMMA = 'e_comma';
	private const E_COLON = 'e_colon';
	private const E_EOF = 'e_eof';

	private readonly ResultBuilder $builder;
	private readonly SearchSourceProvider $provider;

	private string $state = self::S_START;
	private string $token = '';
	/** @var string[] */
	private array $aliases = [];

	public function __construct(ResultBuilder $builder, SearchSourceProvider $provider) {
		$this->builder = $builder;
		$this->provider = $provider;
	}

	public function token(string $token): void {
		$this->handle($this->provider->has($token) ? self::E_ALIAS : self::E_TOKEN, $token);
	}

	public function symbol(string $symbol): void {
		$this->handle($this->provider->has($symbol) ? self::E_SHORT_ALIAS : self::E_TOKEN, $symbol);
	}

	public function comma(string $symbol): void {
		$this->handle(self::E_COMMA, $symbol);
	}

	public function colon(string $symbol): void {
		$this->handle(self::E_COLON, $symbol);
	}

	public function eof(): void {
		$this->handle(self::E_EOF);
	}

	private function handle(string $event, string $token = ''): void {
		$this->token = $token;

		list($newState, $action) = match ($this->state) {
			self::S_START => $this->handleStart($event),
			self::S_SHORT_ALIAS => $this->handleShortAlias($event),
			self::S_ALIAS => $this->handleAlias($event),
			self::S_COMMA => $this->handleComma($event),
			self::S_QUERY => $this->handleQuery($event),
			default => [$this->state, $this->noOp(...)],
		};

		$this->state = $newState;
		$action();
	}

	/**
	 * @param string $event
	 * @return array{string, callable}
	 */
	private function handleStart(string $event): array {
		return match ($event) {
			self::E_SHORT_ALIAS => [self::S_SHORT_ALIAS, $this->addAlias(...)],
			self::E_ALIAS => [self::S_ALIAS, $this->addAlias(...)],
			self::E_EOF => [self::S_END, $this->noOp(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	/**
	 * @param string $event
	 * @return array{string, callable}
	 */
	private function handleShortAlias(string $event): array {
		return match ($event) {
			self::E_SHORT_ALIAS => [self::S_SHORT_ALIAS, $this->addAlias(...)],
			self::E_COMMA => [self::S_COMMA, $this->noOp(...)],
			self::E_COLON => [self::S_QUERY, $this->noOp(...)],
			self::E_EOF => [self::S_END, $this->noOp(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	/**
	 * @param string $event
	 * @return array{string, callable}
	 */
	private function handleAlias(string $event): array {
		return match ($event) {
			self::E_COMMA => [self::S_COMMA, $this->noOp(...)],
			self::E_COLON => [self::S_QUERY, $this->noOp(...)],
			self::E_EOF => [self::S_END, $this->noOp(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	/**
	 * @param string $event
	 * @return array{string, callable}
	 */
	private function handleComma(string $event): array {
		return match ($event) {
			self::E_SHORT_ALIAS => [self::S_SHORT_ALIAS, $this->addAlias(...)],
			self::E_ALIAS => [self::S_ALIAS, $this->addAlias(...)],
			self::E_COMMA => [self::S_COMMA, $this->noOp(...)],
			self::E_COLON => [self::S_QUERY, $this->noOp(...)],
			self::E_EOF => [self::S_END, $this->noOp(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	/**
	 * @param string $event
	 * @return array{string, callable}
	 */
	private function handleQuery(string $event): array {
		return match ($event) {
			self::E_EOF => [self::S_END, $this->addQuery(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	private function addAlias(): void {
		$this->aliases[] = $this->token;
	}

	private function addToken(): void {
		$this->builder->addToken($this->token);
	}

	private function addQuery(): void {
		$this->builder->addQuery(
			empty($this->aliases) ? $this->provider->unaliased() : $this->provider->for($this->aliases)
		);
	}

	private function noOp(): void {}
}
