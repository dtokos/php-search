<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

interface CompilerInput {
	public function process(string $query): void;
}
