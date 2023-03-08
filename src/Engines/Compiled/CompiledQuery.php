<?php

namespace Artvys\Search\Engines\Compiled;

use Stringable;

class CompiledQuery implements Stringable {
	/** @var string[] */
	protected array $tokens;

	/**
	 * @param string[] $tokens
	 */
	public function __construct(array $tokens) {
		$this->tokens = $tokens;
	}

	/** @return string[] */
	public function tokens(): array {
		return $this->tokens;
	}

	public function text(): string {
		return $this->joined($this->separator());
	}

	public function joined(string $separator): string {
		return implode($separator, $this->tokens());
	}

	protected function separator(): string {
		return ' ';
	}

	public function __toString(): string {
		return $this->text();
	}
}
