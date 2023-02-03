<?php

namespace Tests\Unit\Result;

use Artvys\Search\Result\Breadcrumb;
use PHPUnit\Framework\TestCase;

class BreadcrumbTest extends TestCase {
	public function testTitle(): void {
		$breadcrumb = $this->makeBreadcrumb(title: 'foo');
		$this->assertSame('foo', $breadcrumb->title());
	}

	public function testSetTitle(): void {
		$breadcrumb = $this->makeBreadcrumb(title: 'foo');
		$breadcrumb->setTitle('bar');
		$this->assertSame('bar', $breadcrumb->title());
	}

	public function testUrl(): void {
		$breadcrumb = $this->makeBreadcrumb(url: 'https://foo.foo');
		$this->assertSame('https://foo.foo', $breadcrumb->url());
	}

	public function testSetUrl(): void {
		$breadcrumb = $this->makeBreadcrumb(url: 'https://foo.foo');
		$breadcrumb->setUrl('https://bar.bar');
		$this->assertSame('https://bar.bar', $breadcrumb->url());
	}

	public function testJsonSerialize(): void {
		$expected = ['title' => 'foo', 'url' => 'https://foo.foo'];
		$breadcrumb = $this->makeBreadcrumb(title: $expected['title'], url: $expected['url']);
		$this->assertJsonStringEqualsJsonString((string)json_encode($expected), (string)json_encode($breadcrumb));
	}

	private function makeBreadcrumb(string $title = '', string $url = ''): Breadcrumb {
		return Breadcrumb::make($title, $url);
	}
}
