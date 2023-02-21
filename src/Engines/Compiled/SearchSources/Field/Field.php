<?php

namespace Artvys\Search\Engines\Compiled\SearchSources\Field;

use Artvys\Search\Engines\Compiled\CompiledQuery;

class Field implements SearchField {
	private string $fieldName;
	/** @var callable(ResultQueryBuilder, string, string): ResultQueryBuilder */
	private $strategy;

	public static function eq(string $fieldName): self {
		return static::equals($fieldName);
	}

	public static function equals(string $fieldName): self {
		return new self(
			$fieldName,
			fn(ResultQueryBuilder $builder, string $field, string $token) => $builder->equals($field, $token)
		);
	}

	public static function contains(string $fieldName): self {
		return new self(
			$fieldName,
			fn(ResultQueryBuilder $builder, string $field, string $token) => $builder->contains($field, $token),
		);
	}

	public static function startsWith(string $fieldName): self {
		return new self(
			$fieldName,
			fn(ResultQueryBuilder $builder, string $field, string $token) => $builder->startsWith($field, $token),
		);
	}

	public static function endsWith(string $fieldName): self {
		return new self(
			$fieldName,
			fn(ResultQueryBuilder $builder, string $field, string $token) => $builder->endsWith($field, $token),
		);
	}

	private function __construct(string $fieldName, callable $strategy) {
		$this->fieldName = $fieldName;
		$this->strategy = $strategy;
	}

	public function apply(ResultQueryBuilder $builder, CompiledQuery $query): ResultQueryBuilder {
		return match (count($query->tokens())) {
			0 => $builder,
			1 => $this->applyToken($builder, $query->tokens()[0]),
			default => $this->applyAllTokens($builder, $query)
		};
	}

	private function applyToken(ResultQueryBuilder $builder, string $token): ResultQueryBuilder {
		return ($this->strategy)($builder, $this->fieldName, $token);
	}

	private function applyAllTokens(ResultQueryBuilder $builder, CompiledQuery $query): ResultQueryBuilder {
		return $builder->or(function(ResultQueryBuilder $b) use ($query) {
			foreach ($query->tokens() as $token)
				$this->applyToken($b, $token);
		});
	}
}
