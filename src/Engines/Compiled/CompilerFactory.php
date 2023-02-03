<?php

namespace Artvys\Search\Engines\Compiled;

interface CompilerFactory {
	public function make(): Compiler;
}
