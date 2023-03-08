<?php

namespace Artvys\Search\Result;

use JsonSerializable;

class Breadcrumb implements JsonSerializable {
	private string $title;
	private string $url;

	public static function make(string $title, string $url): self {
		return new self($title, $url);
	}

	public function __construct(string $title, string $url) {
		$this->title = $title;
		$this->url = $url;
	}

	public function title(): string {
		return $this->title;
	}

	public function setTitle(string $title): static {
		$this->title = $title;
		return $this;
	}

	public function url(): string {
		return $this->url;
	}

	public function setUrl(string $url): static {
		$this->url = $url;
		return $this;
	}

	/** @return array<string, mixed> */
	public function jsonSerialize(): array {
		return [
			'title' => $this->title(),
			'url' => $this->url(),
		];
	}
}
