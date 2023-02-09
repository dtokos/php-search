<?php

namespace Tests\Unit\Engines\Compiled\Compilers\IO;

use Artvys\Search\Engines\Compiled\CompilationResult;
use Artvys\Search\Engines\Compiled\Compilers\IO\CompilerInput;
use Artvys\Search\Engines\Compiled\Compilers\IO\CompilerOutput;
use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompiler;
use PHPUnit\Framework\TestCase;

class IOCompilerTest extends TestCase {
	public function testCompile(): void {
		$expected = new CompilationResult([]);
		$input = $this->createStub(CompilerInput::class);
		$output = $this->createStub(CompilerOutput::class);
		$output->method('result')->willReturn($expected);

		$compiler = new IOCompiler($input, $output);
		$this->assertSame($expected, $compiler->compile(''));
	}
}
