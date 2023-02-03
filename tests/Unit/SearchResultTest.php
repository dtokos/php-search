<?php

namespace Tests\Unit;

use Artvys\Search\Result\Breadcrumb;
use Artvys\Search\Result\Link;
use Artvys\Search\Result\Tag;
use Artvys\Search\SearchResult;
use PHPUnit\Framework\TestCase;

class SearchResultTest extends TestCase {
	public function testTitle(): void {
		$result = $this->makeResult(title: 'foo');
		$this->assertSame('foo', $result->title());
	}

	public function testSetTitle(): void {
		$result = $this->makeResult(title: 'foo');
		$result->setTitle('bar');
		$this->assertSame('bar', $result->title());
	}

	public function testDescription(): void {
		$result = $this->makeResult(description: 'foo');
		$this->assertSame('foo', $result->description());
	}

	public function testSetDescription(): void {
		$result = $this->makeResult(description: 'foo');
		$result->setDescription('bar');
		$this->assertSame('bar', $result->description());
	}

	public function testUrl(): void {
		$result = $this->makeResult(url: 'https://foo.foo');
		$this->assertSame('https://foo.foo', $result->url());
	}

	public function testSetUrl(): void {
		$result = $this->makeResult(url: 'https://foo.foo');
		$result->setUrl('https://bar.bar');
		$this->assertSame('https://bar.bar', $result->url());
	}

	public function testThumbnailUrl(): void {
		$result = $this->makeResult(thumbnailUrl: 'https://foo.foo');
		$this->assertSame('https://foo.foo', $result->thumbnailUrl());
	}

	public function testSetThumbnailUrl(): void {
		$result = $this->makeResult(thumbnailUrl: 'https://foo.foo');
		$result->setThumbnailUrl('https://bar.bar');
		$this->assertSame('https://bar.bar', $result->thumbnailUrl());
	}

	public function testRemoveThumbnailUrl(): void {
		$result = $this->makeResult(thumbnailUrl: 'https://foo.foo');
		$result->removeThumbnailUrl();
		$this->assertNull($result->thumbnailUrl());
	}

	public function testHelpText(): void {
		$result = $this->makeResult(helpText: 'foo');
		$this->assertSame('foo', $result->helpText());
	}

	public function testSetHelpText(): void {
		$result = $this->makeResult(helpText: 'foo');
		$result->setHelpText('bar');
		$this->assertSame('bar', $result->helpText());
	}

	public function testRemoveHelpText(): void {
		$result = $this->makeResult(helpText: 'foo');
		$result->removeHelpText();
		$this->assertSame('', $result->helpText());
	}

	public function testBreadcrumbs(): void {
		$breadcrumbs = $this->makeBreadcrumbs();
		$result = $this->makeResult(breadcrumbs: $breadcrumbs);
		$this->assertSame($breadcrumbs, $result->breadcrumbs());
	}

	public function testSetBreadcrumbs(): void {
		$breadcrumbs = [$this->makeBreadcrumb(title: 'lorem')];
		$result = $this->makeResult(breadcrumbs: $this->makeBreadcrumbs());
		$result->setBreadcrumbs($breadcrumbs);
		$this->assertSame($breadcrumbs, $result->breadcrumbs());
	}

	public function testRemoveBreadcrumbs(): void {
		$result = $this->makeResult(breadcrumbs: $this->makeBreadcrumbs());
		$result->removeBreadcrumbs();
		$this->assertSame([], $result->breadcrumbs());
	}

	public function testPrependBreadcrumb(): void {
		$breadcrumbs = $this->makeBreadcrumbs();
		$newBreadcrumb = $this->makeBreadcrumb(title: 'lorem');
		$result = $this->makeResult(breadcrumbs: $breadcrumbs);
		$result->prependBreadcrumb($newBreadcrumb);
		$this->assertSame([$newBreadcrumb, ...$breadcrumbs], $result->breadcrumbs());
	}

	public function testPrependBreadcrumbs(): void {
		$breadcrumbs = $this->makeBreadcrumbs();
		$newBreadcrumbs = [$this->makeBreadcrumb(title: 'lorem'), $this->makeBreadcrumb(title: 'ipsum')];
		$result = $this->makeResult(breadcrumbs: $breadcrumbs);
		$result->prependBreadcrumbs($newBreadcrumbs);
		$this->assertSame([...$newBreadcrumbs, ...$breadcrumbs], $result->breadcrumbs());
	}

	public function testAppendBreadcrumb(): void {
		$breadcrumbs = $this->makeBreadcrumbs();
		$newBreadcrumb = $this->makeBreadcrumb(title: 'lorem');
		$result = $this->makeResult(breadcrumbs: $breadcrumbs);
		$result->appendBreadcrumb($newBreadcrumb);
		$this->assertSame([...$breadcrumbs, $newBreadcrumb], $result->breadcrumbs());
	}

	public function testAppendBreadcrumbs(): void {
		$breadcrumbs = $this->makeBreadcrumbs();
		$newBreadcrumbs = [$this->makeBreadcrumb(title: 'lorem'), $this->makeBreadcrumb(title: 'ipsum')];
		$result = $this->makeResult(breadcrumbs: $breadcrumbs);
		$result->appendBreadcrumbs($newBreadcrumbs);
		$this->assertSame([...$breadcrumbs, ...$newBreadcrumbs], $result->breadcrumbs());
	}

	public function testTags(): void {
		$tags = $this->makeTags();
		$result = $this->makeResult(tags: $tags);
		$this->assertSame($tags, $result->tags());
	}

	public function testSetTags(): void {
		$tags = [$this->makeTag(title: 'lorem')];
		$result = $this->makeResult(tags: $this->makeTags());
		$result->setTags($tags);
		$this->assertSame($tags, $result->tags());
	}

	public function testRemoveTags(): void {
		$result = $this->makeResult(tags: $this->makeTags());
		$result->removeTags();
		$this->assertSame([], $result->tags());
	}

	public function testPrependTag(): void {
		$tags = $this->makeTags();
		$newTag = $this->makeTag(title: 'lorem');
		$result = $this->makeResult(tags: $tags);
		$result->prependTag($newTag);
		$this->assertSame([$newTag, ...$tags], $result->tags());
	}

	public function testPrependTags(): void {
		$tags = $this->makeTags();
		$newTags = [$this->makeTag(title: 'lorem'), $this->makeTag(title: 'ipsum')];
		$result = $this->makeResult(tags: $tags);
		$result->prependTags($newTags);
		$this->assertSame([...$newTags, ...$tags], $result->tags());
	}

	public function testAppendTag(): void {
		$tags = $this->makeTags();
		$newTag = $this->makeTag(title: 'lorem');
		$result = $this->makeResult(tags: $tags);
		$result->appendTag($newTag);
		$this->assertSame([...$tags, $newTag], $result->tags());
	}

	public function testAppendTags(): void {
		$tags = $this->makeTags();
		$newTags = [$this->makeTag(title: 'lorem'), $this->makeTag(title: 'ipsum')];
		$result = $this->makeResult(tags: $tags);
		$result->appendTags($newTags);
		$this->assertSame([...$tags, ...$newTags], $result->tags());
	}

	public function testLinks(): void {
		$links = $this->makeLinks();
		$result = $this->makeResult(links: $links);
		$this->assertSame($links, $result->links());
	}

	public function testSetLinks(): void {
		$links = [$this->makeLink(title: 'lorem')];
		$result = $this->makeResult(links: $this->makeLinks());
		$result->setLinks($links);
		$this->assertSame($links, $result->links());
	}

	public function testRemoveLinks(): void {
		$result = $this->makeResult(links: $this->makeLinks());
		$result->removeLinks();
		$this->assertSame([], $result->links());
	}

	public function testPrependLink(): void {
		$links = $this->makeLinks();
		$newLink = $this->makeLink(title: 'lorem');
		$result = $this->makeResult(links: $links);
		$result->prependLink($newLink);
		$this->assertSame([$newLink, ...$links], $result->links());
	}

	public function testPrependLinks(): void {
		$links = $this->makeLinks();
		$newLinks = [$this->makeLink(title: 'lorem'), $this->makeLink(title: 'ipsum')];
		$result = $this->makeResult(links: $links);
		$result->prependLinks($newLinks);
		$this->assertSame([...$newLinks, ...$links], $result->links());
	}

	public function testAppendLink(): void {
		$links = $this->makeLinks();
		$newLink = $this->makeLink(title: 'lorem');
		$result = $this->makeResult(links: $links);
		$result->appendLink($newLink);
		$this->assertSame([...$links, $newLink], $result->links());
	}

	public function testAppendLinks(): void {
		$links = $this->makeLinks();
		$newLinks = [$this->makeLink(title: 'lorem'), $this->makeLink(title: 'ipsum')];
		$result = $this->makeResult(links: $links);
		$result->appendLinks($newLinks);
		$this->assertSame([...$links, ...$newLinks], $result->links());
	}

	public function testJsonSerialize(): void {
		$expected = [
			'title' => 'foo title',
			'description' => 'foo desc',
			'url' => 'https://foo.foo',
			'thumbnailUrl' => 'https://foo.foo/thumbnail',
			'helpText' => 'foo help',
			'breadcrumbs' => [['title' => 'b1', 'url' => 'https://foo.foo/b1']],
			'tags' => [['title' => 't1', 'url' => 'https://foo.foo/t1', 'color' => '#f00']],
			'links' => [['title' => 'l1', 'url' => 'https://foo.foo/l1']],
		];
		$result = $this->makeResult(
			title: $expected['title'],
			description: $expected['description'],
			url: $expected['url'],
			thumbnailUrl: $expected['thumbnailUrl'],
			helpText: $expected['helpText'],
			breadcrumbs: [$this->makeBreadcrumb(title: $expected['breadcrumbs'][0]['title'], url: $expected['breadcrumbs'][0]['url'])],
			tags: [$this->makeTag(title: $expected['tags'][0]['title'], url: $expected['tags'][0]['url'], color: $expected['tags'][0]['color'])],
			links: [$this->makeLink(title: $expected['links'][0]['title'], url: $expected['links'][0]['url'])]
		);

		$this->assertJsonStringEqualsJsonString((string)json_encode($expected), (string)json_encode($result));
	}

	/**
	 * @param string $title
	 * @param string $description
	 * @param string $url
	 * @param ?string $thumbnailUrl
	 * @param string $helpText
	 * @param Breadcrumb[] $breadcrumbs
	 * @param Tag[] $tags
	 * @param Link[] $links
	 * @return SearchResult
	 */
	public function makeResult(string $title = '', string $description = '', string $url = '', ?string $thumbnailUrl = null, string $helpText = '', array $breadcrumbs = [], array $tags = [], array $links = []): SearchResult {
		return SearchResult::make($title, $description, $url, $thumbnailUrl, $helpText, $breadcrumbs, $tags, $links);
	}

	/** @return Breadcrumb[] */
	private function makeBreadcrumbs(): array {
		return [$this->makeBreadcrumb(title: 'foo'), $this->makeBreadcrumb(title: 'bar')];
	}

	private function makeBreadcrumb(string $title = '', string $url = ''): Breadcrumb {
		return Breadcrumb::make($title, $url);
	}

	/** @return Tag[] */
	private function makeTags(): array {
		return [$this->makeTag(title: 'foo'), $this->makeTag(title: 'bar')];
	}

	private function makeTag(string $title = '', string $url = '', mixed $color = null): Tag {
		return Tag::make($title, $url, $color);
	}

	/** @return Link[] */
	private function makeLinks(): array {
		return [$this->makeLink(title: 'foo'), $this->makeLink(title: 'bar')];
	}

	private function makeLink(string $title = '', string $url = ''): Link {
		return Link::make($title, $url);
	}
}
