<?php

namespace Tests\Unit\Result;

use Artvys\Search\Result\Link;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase {
	public function testTitle(): void {
		$link = $this->makeLink(title: 'foo');
		$this->assertSame('foo', $link->title());
	}

	public function testSetTitle(): void {
		$link = $this->makeLink(title: 'foo');
		$link->setTitle('bar');
		$this->assertSame('bar', $link->title());
	}

	public function testUrl(): void {
		$link = $this->makeLink(url: 'https://foo.foo');
		$this->assertSame('https://foo.foo', $link->url());
	}

	public function testSetUrl(): void {
		$link = $this->makeLink(url: 'https://foo.foo');
		$link->setUrl('https://bar.bar');
		$this->assertSame('https://bar.bar', $link->url());
	}

	private function makeLink(string $title = '', string $url = ''): Link {
		return Link::make($title, $url);
	}
}
