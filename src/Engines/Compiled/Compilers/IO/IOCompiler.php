<?php

namespace Artvys\Search\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\Compiler;

class IOCompiler implements Compiler {
	private readonly CompilerInput $input;
	private readonly CompilerOutput $output;

	public function __construct(CompilerInput $input, CompilerOutput $output) {
		$this->input = $input;
		$this->output = $output;
	}

	public function compile(string $query): CompilationResult {
		$this->input->process($query);
		return $this->output->result();
	}
}
