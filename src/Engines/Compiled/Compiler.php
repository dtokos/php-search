<?php

namespace Artvys\Search\Engines\Compiled;

interface Compiler {
	public function compile(string $query): CompilationResult;
}
