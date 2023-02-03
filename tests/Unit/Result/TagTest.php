<?php

namespace Tests\Unit\Result;

use Artvys\Search\Result\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase {
	public function testTitle(): void {
		$tag = $this->makeTag(title: 'foo');
		$this->assertSame('foo', $tag->title());
	}

	public function testSetTitle(): void {
		$tag = $this->makeTag(title: 'foo');
		$tag->setTitle('bar');
		$this->assertSame('bar', $tag->title());
	}

	public function testUrl(): void {
		$tag = $this->makeTag(url: 'https://foo.foo');
		$this->assertSame('https://foo.foo', $tag->url());
	}

	public function testSetUrl(): void {
		$tag = $this->makeTag(url: 'https://foo.foo');
		$tag->setUrl('https://bar.bar');
		$this->assertSame('https://bar.bar', $tag->url());
	}

	public function testColor(): void {
		$tag = $this->makeTag(color: '#000');
		$this->assertSame('#000', $tag->color());
	}

	public function testSetColor(): void {
		$tag = $this->makeTag(color: '#000');
		$tag->setColor('#111');
		$this->assertSame('#111', $tag->color());
	}

	public function testHasColor(): void {
		$tag = $this->makeTag(color: '#000');
		$this->assertTrue($tag->hasColor());
		$tag = $this->makeTag(color: null);
		$this->assertFalse($tag->hasColor());
	}

	private function makeTag(string $title = '', string $url = '', mixed $color = null): Tag {
		return Tag::make($title, $url, $color);
	}
}
