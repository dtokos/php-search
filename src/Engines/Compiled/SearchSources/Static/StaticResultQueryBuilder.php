<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Static;

use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;
use Artvys\Search\Result\Breadcrumb;
use Artvys\Search\Result\Link;
use Artvys\Search\Result\Tag;
use Artvys\Search\SearchResult;
use CallbackFilterIterator;
use Generator;
use LimitIterator;
use Stringable;

class StaticResultQueryBuilder implements ResultQueryBuilder {
	/** @var Generator<SearchResult> */
	protected Generator $results;
	protected bool $groupUsingAnd = true;
	/** @var array<callable(SearchResult): bool> */
	protected array $predicates;

	/**
	 * @param Generator<SearchResult> $results
	 */
	public function __construct(Generator $results) {
		$this->results = $results;
	}

	/** @inheritDoc */
	public function and(callable $buildingBlock): static {
		$builder = $this->subBuilder(true);
		$buildingBlock($builder);
		$this->predicates[] = $builder->makeFilterPredicate();

		return $this;
	}

	/** @inheritDoc */
	public function or(callable $buildingBlock): static {
		$builder = $this->subBuilder(false);
		$buildingBlock($builder);
		$this->predicates[] = $builder->makeFilterPredicate();

		return $this;
	}

	public function equals(string $field, string $token): static {
		return $this->add($field, fn(string $value) => $value === $token);
	}

	public function contains(string $field, string $token): static {
		return $this->add($field, fn(string $value) => mb_stripos($value, $token) !== false);
	}

	public function startsWith(string $field, string $token): static {
		return $this->add($field, fn(string $value) => mb_stripos($value, $token) === 0);
	}

	public function endsWith(string $field, string $token): static {
		return $this->add($field, fn(string $value) => mb_strripos($value, $token) === mb_strlen($value) - mb_strlen($token));
	}

	protected function add(string $field, callable $valuePredicate): static {
		if ($fieldPredicate = $this->makeFieldPredicate($field, $valuePredicate)) {
			$this->predicates[] = $fieldPredicate;
		}

		return $this;
	}

	/**
	 * @param string $field
	 * @param callable(string): bool $valuePredicate
	 * @return ?callable(SearchResult): bool
	 */
	protected function makeFieldPredicate(string $field, callable $valuePredicate): ?callable {
		$retriever = $this->valuesRetriever($field);

		if(!$retriever) return null;

		return function(SearchResult $result) use ($retriever, $valuePredicate) {
			foreach ($retriever($result) as $value)
				if (($stringValue = $this->toString($value)) && $valuePredicate($stringValue))
					return true;

			return false;
		};
	}

	protected function toString(mixed $value): ?string {
		if (!is_scalar($value) && ! ($value instanceof Stringable)) return null;

		$stringValue = trim((string)$value);

		return $stringValue !== '' ? $stringValue : null;
	}

	/**
	 * @param string $field
	 * @return ?callable(SearchResult): mixed[]
	 */
	protected function valuesRetriever(string $field): ?callable {
		return match ($field) {
			'title' => fn(SearchResult $r) => [$r->title()],
			'description' => fn(SearchResult $r) => [$r->description()],
			'url' => fn(SearchResult $r) => [$r->url()],
			'thumbnailUrl' => fn(SearchResult $r) => [$r->thumbnailUrl()],
			'helpText' => fn(SearchResult $r) => [$r->helpText()],
			'breadcrumbs.title' => fn(SearchResult $r) => array_map(fn(Breadcrumb $b) => $b->title(), $r->breadcrumbs()),
			'breadcrumbs.url' => fn(SearchResult $r) => array_map(fn(Breadcrumb $b) => $b->url(), $r->breadcrumbs()),
			'tags.title' => fn(SearchResult $r) => array_map(fn(Tag $t) => $t->title(), $r->tags()),
			'tags.url' => fn(SearchResult $r) => array_map(fn(Tag $t) => $t->url(), $r->tags()),
			'tags.color' => fn(SearchResult $r) => array_map(fn(Tag $t) => $t->color(), $r->tags()),
			'links.title' => fn(SearchResult $r) => array_map(fn(Link $l) => $l->title(), $r->links()),
			'links.url' => fn(SearchResult $r) => array_map(fn(Link $l) => $l->url(), $r->links()),
			default => null,
		};
	}

	public function results(int $limit): array {
		return iterator_to_array(
			new LimitIterator(new CallbackFilterIterator($this->results, $this->makeFilterPredicate()), 0, $limit),
			false
		);
	}

	protected function subBuilder(bool $groupUsingAnd): StaticResultQueryBuilder {
		$builder = new self($this->results);
		$builder->groupUsingAnd = $groupUsingAnd;

		return $builder;
	}

	/** @return callable(SearchResult): bool */
	protected function makeFilterPredicate(): callable {
		return $this->groupUsingAnd ? $this->andFilterPredicate(...) : $this->orFilterPredicate(...);
	}

	protected function andFilterPredicate(SearchResult $result): bool {
		foreach ($this->predicates as $predicate)
			if (!$predicate($result))
				return false;

		return true;
	}

	protected function orFilterPredicate(SearchResult $result): bool {
		foreach ($this->predicates as $predicate)
			if ($predicate($result))
				return true;

		return false;
	}
}
