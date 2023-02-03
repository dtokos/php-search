<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\CompilationResult;

interface CompilerOutput {
	public function result(): CompilationResult;
}
