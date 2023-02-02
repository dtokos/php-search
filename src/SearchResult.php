<?php

namespace Artvys\Search;

use Artvys\Search\Result\Breadcrumb;
use Artvys\Search\Result\Link;
use Artvys\Search\Result\Tag;

class SearchResult {
	private string $title;
	private string $description;
	private string $url;

	private ?string $thumbnailUrl;
	private string $helpText;
	/** @var Breadcrumb[] */
	private array $breadcrumbs;
	/** @var Tag[] */
	private array $tags;
	/** @var Link[] */
	private array $links;

	/**
	 * @param string $title
	 * @param string $description
	 * @param string $url
	 * @param ?string $thumbnailUrl
	 * @param string $helpText
	 * @param Breadcrumb[] $breadcrumbs
	 * @param Tag[] $tags
	 * @param Link[] $links
	 * @return self
	 */
	public static function make(string $title, string $description, string $url, ?string $thumbnailUrl = null, string $helpText = '', array $breadcrumbs = [], array $tags = [], array $links = []): self {
		return new self($title, $description, $url, $thumbnailUrl, $helpText, $breadcrumbs, $tags, $links);
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
	 */
	public function __construct(string $title, string $description, string $url, ?string $thumbnailUrl = null, string $helpText = '', array $breadcrumbs = [], array $tags = [], array $links = []) {
		$this->title = $title;
		$this->description = $description;
		$this->url = $url;
		$this->thumbnailUrl = $thumbnailUrl;
		$this->helpText = $helpText;
		$this->breadcrumbs = $breadcrumbs;
		$this->tags = $tags;
		$this->links = $links;
	}

	public function title(): string {
		return $this->title;
	}

	public function setTitle(string $title): static {
		$this->title = $title;
		return $this;
	}

	public function description(): string {
		return $this->description;
	}

	public function setDescription(string $description): static {
		$this->description = $description;
		return $this;
	}

	public function url(): string {
		return $this->url;
	}

	public function setUrl(string $url): static {
		$this->url = $url;
		return $this;
	}

	public function thumbnailUrl(): ?string {
		return $this->thumbnailUrl;
	}

	public function setThumbnailUrl(?string $thumbnailUrl): static {
		$this->thumbnailUrl = $thumbnailUrl;
		return $this;
	}

	public function removeThumbnailUrl(): static {
		return $this->setThumbnailUrl(null);
	}

	public function helpText(): string {
		return $this->helpText;
	}

	public function setHelpText(string $helpText): static {
		$this->helpText = $helpText;
		return $this;
	}

	public function removeHelpText(): static {
		return $this->setHelpText('');
	}

	/** @return Breadcrumb[] */
	public function breadcrumbs(): array {
		return $this->breadcrumbs;
	}

	/**
	 * @param Breadcrumb[] $breadcrumbs
	 * @return $this
	 */
	public function setBreadcrumbs(array $breadcrumbs): static {
		$this->breadcrumbs = $breadcrumbs;
		return $this;
	}

	public function removeBreadcrumbs(): static {
		return $this->setBreadcrumbs([]);
	}

	public function prependBreadcrumb(Breadcrumb $breadcrumb): static {
		return $this->prependBreadcrumbs([$breadcrumb]);
	}

	/**
	 * @param Breadcrumb[] $breadcrumbs
	 * @return $this
	 */
	public function prependBreadcrumbs(array $breadcrumbs): static {
		return $this->setBreadcrumbs($this->append($breadcrumbs, $this->breadcrumbs()));
	}

	public function appendBreadcrumb(Breadcrumb $breadcrumb): static {
		return $this->appendBreadcrumbs([$breadcrumb]);
	}

	/**
	 * @param Breadcrumb[] $breadcrumbs
	 * @return $this
	 */
	public function appendBreadcrumbs(array $breadcrumbs): static {
		return $this->setBreadcrumbs($this->append($this->breadcrumbs(), $breadcrumbs));
	}

	/** @return Tag[] */
	public function tags(): array {
		return $this->tags;
	}

	/**
	 * @param Tag[] $tags
	 * @return $this
	 */
	public function setTags(array $tags): static {
		$this->tags = $tags;
		return $this;
	}

	public function removeTags(): static {
		return $this->setTags([]);
	}

	public function prependTag(Tag $tag): static {
		return $this->prependTags([$tag]);
	}

	/**
	 * @param Tag[] $tags
	 * @return $this
	 */
	public function prependTags(array $tags): static {
		return $this->setTags($this->append($tags, $this->tags()));
	}

	public function appendTag(Tag $tag): static {
		return $this->appendTags([$tag]);
	}

	/**
	 * @param Tag[] $tags
	 * @return $this
	 */
	public function appendTags(array $tags): static {
		return $this->setTags($this->append($this->tags(), $tags));
	}

	/** @return Link[] */
	public function links(): array {
		return $this->links;
	}

	/**
	 * @param Link[] $links
	 * @return $this
	 */
	public function setLinks(array $links): static {
		$this->links = $links;
		return $this;
	}

	public function removeLinks(): static {
		return $this->setLinks([]);
	}

	public function prependLink(Link $link): static {
		return $this->prependLinks([$link]);
	}

	/**
	 * @param Link[] $links
	 * @return $this
	 */
	public function prependLinks(array $links): static {
		return $this->setLinks($this->append($links, $this->links()));
	}

	public function appendLink(Link $link): static {
		return $this->appendLinks([$link]);
	}

	/**
	 * @param Link[] $links
	 * @return $this
	 */
	public function appendLinks(array $links): static {
		return $this->setLinks($this->append($this->links(), $links));
	}

	/**
	 * @template T
	 * @param T[] $first
	 * @param T[] $second
	 * @return T[]
	 */
	private function append(array $first, array $second): array {
		return array_merge(array_values($first), array_values($second));
	}
}
