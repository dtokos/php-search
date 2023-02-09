<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\CompilerFactory;

class IOCompilerFactory implements CompilerFactory {
	private readonly SearchSourceProvider $provider;

	public function __construct(SearchSourceProvider $provider) {
		$this->provider = $provider;
	}

	public function make(): IOCompiler {
		$builder = new Builder();
		$parser = new Parser($builder, $this->provider);
		$lexer = new Lexer($parser);

		return new IOCompiler($lexer, $builder);
	}
}
