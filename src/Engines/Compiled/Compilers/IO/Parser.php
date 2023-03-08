<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

class Parser implements TokenCollector {
	protected const S_START = 's_start';
	protected const S_SHORT_ALIAS = 's_short_alias';
	protected const S_ALIAS = 's_alias';
	protected const S_COMMA = 's_comma';
	protected const S_QUERY = 's_query';
	protected const S_END = 's_end';

	protected const E_SHORT_ALIAS = 'e_short_alias';
	protected const E_ALIAS = 'e_alias';
	protected const E_TOKEN = 'e_token';
	protected const E_COMMA = 'e_comma';
	protected const E_COLON = 'e_colon';
	protected const E_EOF = 'e_eof';

	protected readonly ResultBuilder $builder;
	protected readonly SearchSourceProvider $provider;

	protected string $state = self::S_START;
	protected string $token = '';
	/** @var string[] */
	protected array $aliases = [];

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

	protected function handle(string $event, string $token = ''): void {
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
	protected function handleStart(string $event): array {
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
	protected function handleShortAlias(string $event): array {
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
	protected function handleAlias(string $event): array {
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
	protected function handleComma(string $event): array {
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
	protected function handleQuery(string $event): array {
		return match ($event) {
			self::E_EOF => [self::S_END, $this->addQuery(...)],
			default => [self::S_QUERY, $this->addToken(...)],
		};
	}

	protected function addAlias(): void {
		$this->aliases[] = $this->token;
	}

	protected function addToken(): void {
		$this->builder->addToken($this->token);
	}

	protected function addQuery(): void {
		$this->builder->addQuery(
			empty($this->aliases) ? $this->provider->unaliased() : $this->provider->for($this->aliases)
		);
	}

	protected function noOp(): void {}
}
