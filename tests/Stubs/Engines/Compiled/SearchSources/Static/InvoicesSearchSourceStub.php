<?php

namespace Tests\Stubs\Engines\Compiled\SearchSources\Static;

use Artvys\Search\Engines\Compiled\SearchSources\Static\StaticSearchSource;
use Artvys\Search\SearchResult;
use Generator;

class InvoicesSearchSourceStub extends StaticSearchSource {
	/** @var SearchResult[] */
	public array $allResults;

	public function __construct() {
		$this->allResults = [
			'0001' => SearchResult::make('Invoice 0001', 'Invoice issued to user Foo on 14. 01.', 'https://foo.bar/invoices/0001'),
			'0002' => SearchResult::make('Invoice 0002', 'Invoice issued to user Bar on 13. 04.', 'https://foo.bar/invoices/0002'),
			'0003' => SearchResult::make('Invoice 0003', 'Invoice issued to user Baz on 22. 04.', 'https://foo.bar/invoices/0003'),
			'0004' => SearchResult::make('Invoice 0004', 'Invoice issued to user Qux on 22. 06.', 'https://foo.bar/invoices/0004'),
			'0005' => SearchResult::make('Invoice 0005', 'Invoice issued to user Foo on 10. 07.', 'https://foo.bar/invoices/0005'),
			'0006' => SearchResult::make('Invoice 0006', 'Invoice issued to user Bar on 04. 08.', 'https://foo.bar/invoices/0006'),
			'0007' => SearchResult::make('Invoice 0007', 'Invoice issued to user Foo on 11. 09.', 'https://foo.bar/invoices/0007'),
			'0008' => SearchResult::make('Invoice 0008', 'Invoice issued to user Baz on 21. 09.', 'https://foo.bar/invoices/0008'),
			'0009' => SearchResult::make('Invoice 0009', 'Invoice issued to user Baz on 15. 12.', 'https://foo.bar/invoices/0009'),
			'0010' => SearchResult::make('Invoice 0010', 'Invoice issued to user Qux on 19. 12.', 'https://foo.bar/invoices/0010'),
		];
	}

	/** @inheritDoc */
	protected function allResults(): Generator {
		foreach ($this->allResults as $result) yield $result;
	}
}
