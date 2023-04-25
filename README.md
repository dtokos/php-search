artvys/search
=============

A simple search library written in php with no external dependencies. This package facilitates search functionality
whilst being framework-agnostic. First party framework specific packages can be used to ease the integration process.

# 1. Installation

This section describes installation of this package. If you wish to install framework specific adapter then read its
installation instructions first. If you have [composer](https://getcomposer.org) installed, then simply run:

```shell
composer require artvys/search
```

# 2. Configuration

To use this package, first you need to assemble a [SearchEngine](src/SearchEngine.php). You can do it in place like so:

```php
use Your\Project\SearchSources\UsersSearchSource;
use Your\Project\SearchSources\InvoicesSearchSource;
use Your\Project\SearchSources\ExternalAPISearchSource;

use Artvys\Search\Engines\Compiled\SearchSourceRegistry;
use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompilerFactory;
use Artvys\Search\Engines\Compiled\FetchingStrategies\FirstFitFetchingStrategy;
use Artvys\Search\Engines\Compiled\CompiledSearchEngine;

$registry = new SearchSourceRegistry();
$registry->register(new UsersSearchSource(), ['@']);
$registry->register(new InvoicesSearchSource(), ['#']);
$registry->register(new ExternalAPISearchSource(), ['api'], false);
$compilerFactory = new IOCompilerFactory($registry);
$fetchingStrategy = new FirstFitFetchingStrategy();
$engine = new CompiledSearchEngine($compilerFactory, $fetchingStrategy);
```

But usually you will configure it once in some kind of dependency injection container and then just let the container
build it:

```php
use Your\Project\SearchSources\UsersSearchSource;
use Your\Project\SearchSources\InvoicesSearchSource;
use Your\Project\SearchSources\ExternalAPISearchSource;

use Artvys\Search\Engines\Compiled\SearchSourceRegistry;
use Artvys\Search\Engines\Compiled\Compilers\IO\IOCompilerFactory;
use Artvys\Search\Engines\Compiled\FetchingStrategies\FirstFitFetchingStrategy;
use Artvys\Search\Engines\Compiled\CompiledSearchEngine;
use Artvys\Search\Engines\Compiled\Compilers\IO\SearchSourceProvider;
use Artvys\Search\SearchEngine;

$container->add(SearchSourceProvider::class, function() {
    $registry = new SearchSourceRegistry();
    $registry->register(new UsersSearchSource(), ['@']);
    $registry->register(new InvoicesSearchSource(), ['#']);
    $registry->register(new ExternalAPISearchSource(), ['api'], false);

    return $registry;
});

$container->add(SearchEngine::class, function($c) {
    $compilerFactory = new IOCompilerFactory($c->get(SearchSourceProvider::class));
    $fetchingStrategy = new FirstFitFetchingStrategy();

    return new CompiledSearchEngine($compilerFactory, $fetchingStrategy);
});
```

# 3. Usage

To get search results simply call `search` method on the [SearchEngine](src/SearchEngine.php). Given the
[SearchEngine](src/SearchEngine.php) built in previous section, you can do the following.

```php
$results = $engine->search('Foo bar', 10);
```

The search engine will look up to 10 [SearchResults](src/SearchResult.php) matching the query "Foo bar".

Let's look at a realistic use case, that could be required by your project:

```php
use Artvys\Search\SearchEngine;

class SearchController {
    public SearchEngine $searchEngine;

    public function __construct(SearchEngine $searchEngine) {
        $this->searchEngine = $searchEngine;
    }

    public function search(Request $request): Response {
        return new JsonResponse($this->searchEngine->search($request->query('search'), $request->query('limit', 10)));
    }
}
```

The example illustrates how to use the [SearchEngine](src/SearchEngine.php) in Controller class. Better input handling
is recommended. The [SearchEngine](src/SearchEngine.php) gets injected into `SearchController` by the dependency
injection container. We take the users query from the request and specify limit, which is optional (10 by default).
[SearchEngine](src/SearchEngine.php) will look for [SearchResults](src/SearchResult.php) and pass then to the
`JsonResponse`. [SearchResult](src/SearchResult.php) implements
[JsonSerializable](https://www.php.net/manual/en/class.jsonserializable.php) interface by default to ease your
integration.

# 4. Core concepts

To use this package to it's full potential, you need to learn just 4 concepts that the package is composed of. They are
[SearchEngine](src/SearchEngine.php), [SearchResult](src/SearchResult.php),
[SearchSource](src/Engines/Compiled/SearchSource.php) and simple query language.

## 4.1 The [SearchEngine](src/SearchEngine.php)

Objects implementing the [SearchEngine](src/SearchEngine.php) interface serve the purpose of facade. The internal
searching, filtering and aggregation logic can be very complicated and is hidden behind the
[SearchEngine](src/SearchEngine.php). This package ships with
[CompiledSearchEngine](src/Engines/Compiled/CompiledSearchEngine.php) which uses simple query language to aggregate
[SearchResults](src/SearchResult.php) from different sources.

When the `search` method is called, it will spin up new instance of [Compiler](src/Engines/Compiled/Compiler.php) which
has the responsibility of compiling the user query into tokens and choosing
[SearchSources](src/Engines/Compiled/SearchSource.php). Please notice that the
[Compiler](src/Engines/Compiled/Compiler.php) is an interface and therefore the query language can be completely
replaced by one of your liking.

## 4.2 The [SearchResult](src/SearchResult.php)

All [SearchEngines](src/SearchEngine.php) and all [SearchSources](src/Engines/Compiled/SearchSource.php) should return
arrays of [SearchResult](src/SearchResult.php) instances.

This package settled to use one concrete implementation of a [SearchResult](src/SearchResult.php). It is a generalized
object designed to provide common features that you can expect from search result. It provides fluent interface, so you
can build it with ease.

Except for the usual attributes it comes with [Breadcrumbs](src/Result/Breadcrumb.php), [Tags](src/Result/Tag.php) and
[Links](src/Result/Link.php). You can use these to categorize your search results and to provide auxiliary info to your
users.

## 4.3 The [SearchSource](src/Engines/Compiled/SearchSource.php)

The actual providers of [SearchResults](src/SearchResult.php) are called
[SearchSources](src/Engines/Compiled/SearchSource.php). They have the responsibility of taking the compiled query and
returning array of [SearchResults](src/SearchResult.php).

It is again an interface, so we don't care where are the [SearchResults](src/SearchResult.php) coming from. They can
come from database, from external API, from filesystem - you name it. Majority of the integration with this package will
reside in implementing your subclasses of [SearchSources](src/Engines/Compiled/SearchSource.php). Two subclasses are
provided to you out of the box: [StaticSearchSource](src/Engines/Compiled/SearchSources/Static/StaticSearchSource.php)
and [FieldSearchSource](src/Engines/Compiled/SearchSources/Field/FieldSearchSource.php). They are abstract classes meant
to be extended, and they implement boilerplate code, so you can just focus on the task at hand.

## 4.4 The query language

This package allows you to use simple query language, implemented by the [Compiler](src/Engines/Compiled/Compiler.php),
to narrow down your [SearchResults](src/SearchResult.php). The language allows the user to optionally specify list of
aliases, followed by the search query.

The most basic query is without any special parameters. For example:

```
Foo bar
```

No aliases were specified so this query would be transformed into tokens `Foo`, `Bar` and sent to
[SearchSources](src/Engines/Compiled/SearchSource.php) registered with parameter `$allowUnaliased` having value `true`
in [SearchSourceRegistry](src/Engines/Compiled/SearchSourceRegistry.php).

Let's explain aliases now. When you register your [SearchSources](src/Engines/Compiled/SearchSource.php), you can give
them aliases. These are simple names used to identify them. The user can then use those aliases to limit the scope of
the search. Then there is the before mentioned argument `$allowUnaliased`. It controls whether the
[SearchSource](src/Engines/Compiled/SearchSource.php) can be queried without forcing the user to explicitly type the
alias. It can be useful for slow external APIs, or for niche sources of results that would usually clutter.

Given the configuration examples from previous sections, let's go back to the simplest query:

```
Foo bar
```

Look how those [SearchSources](src/Engines/Compiled/SearchSource.php) are registered. We don't have any aliases present
in the query, so `UsersSearchSource` and `InvoicesSearchSource` would be used.

To use an alias, simply start query with it:

```
@foo bar
```

We have one alias `@` present, which will get resolved to `UsersSearchSource` and tokens `foo`, `bar`. So only the
`UsersSearchSource` would be queried using those two tokens.

You can even combine multiple aliases together:

```
#@foo bar
```

This query would be parsed into `InvoicesSearchSource` and `UsersSearchSource`. The order of parsed sources matches the
order in query.

Now let's focus on the difference between single-character and multi-character aliases. Single-character aliases can be
combined without separator, because they are unique and easy to identify. Multi-character aliases are not. They need to
be separated using a comma, otherwise you would get one bigger alias. So let's illustrate nearly all the possible forms
on examples:

```
foo bar             //                    | Tokens: foo bar
api foo bar         // Aliases: api       | Tokens: foo bar
api:foo bar         // Aliases: api       | Tokens: foo bar
api: foo bar        // Aliases: api       | Tokens: foo bar
@foo bar            // Aliases: @         | Tokens: foo bar
@# foo bar          // Aliases: @, #      | Tokens: foo bar
@,#,api: foo bar    // Aliases: @, #, api | Tokens: foo bar
#@,api foo bar      // Aliases: #, @, api | Tokens: foo bar
```

Nice little feature is that you can use the same alias for multiple
[SearchSources](src/Engines/Compiled/SearchSource.php). For example, if you want to search for invoices and tickets with
the alias `#`, simply register them like this:

```php
use Your\Project\SearchSources\TicketsSearchSource;
use Your\Project\SearchSources\InvoicesSearchSource;

use Artvys\Search\Engines\Compiled\SearchSourceRegistry;

$registry = new SearchSourceRegistry();
$registry->register(new TicketsSearchSource(), ['#']);
$registry->register(new InvoicesSearchSource(), ['#']);
```

And query them both like this:

```
#12345
```

If you want to know exactly how the query language works, make sure to take a look how the
[Lexer](src/Engines/Compiled/Compilers/IO/Lexer.php) and [Parser](src/Engines/Compiled/Compilers/IO/Parser.php) are
implemented.

# 5. Implementing your [SearchSource](src/Engines/Compiled/SearchSource.php)

The majority of your work will reside in implementing your own [SearchSources](src/Engines/Compiled/SearchSource.php).
[SearchSource](src/Engines/Compiled/SearchSource.php) on its own is an interface, but this package provides two abstract
base classes to ease your integration:
[StaticSearchSource](src/Engines/Compiled/SearchSources/Static/StaticSearchSource.php),
[FieldSearchSource](src/Engines/Compiled/SearchSources/Field/FieldSearchSource.php). More sources can be found in first
party adapters.

The [StaticSearchSource](src/Engines/Compiled/SearchSources/Static/StaticSearchSource.php) is useful for static
[SearchResults](src/SearchResult.php). Maybe you have static pages and want them searchable? Simply extend the
[StaticSearchSource](src/Engines/Compiled/SearchSources/Static/StaticSearchSource.php) base class and implement the
`allResults` method. Just `yield` all your possible results and the base class will take care of the rest. You can
customize which attributes are used for the search by overriding the `fields` method.

```php
use Artvys\Search\Engines\Compiled\SearchSources\Static\StaticSearchSource;
use Artvys\Search\SearchResult;
use Generator;

class UsersNavigationSearchSource extends StaticSearchSource {
    protected function allResults(): Generator {
        yield SearchResult::make('All users', 'Index page for all Users.', 'https://foo.bar');
        yield SearchResult::make('Create a users', 'Page for the creation of a new user.', 'https://foo.bar');
    }
}
```

The [FieldSearchSource](src/Engines/Compiled/SearchSources/Field/FieldSearchSource.php) uses simple declarations, that
specify which fields should be used for search. You need to implement 2 methods. The first one is called `fields` and
in it, which fields you want to use. The second one is `makeResultQueryBuilder` and you just need to return an instance
of [ResultQueryBuilder](src/Engines/Compiled/SearchSources/Field/ResultQueryBuilder.php). Implementations of the
aforementioned interface are provided by first party adapters.

```php
use Your\Project\DBResultQueryBuilder;

use Artvys\Search\Engines\Compiled\SearchSources\Field\FieldSearchSource;
use Artvys\Search\Engines\Compiled\SearchSources\Field\SearchFieldBuilder;
use Artvys\Search\Engines\Compiled\CompiledQuery;
use Artvys\Search\Engines\Compiled\SearchSources\Field\Field;
use Artvys\Search\Engines\Compiled\SearchSources\Field\ResultQueryBuilder;

class UsersSearchSource extends FieldSearchSource {
    protected function fields(SearchFieldBuilder $builder, CompiledQuery $query, int $limit): void {
        $builder->add(Field::contains('name'))
            ->add(Field::contains('description'));
    }

    protected function makeResultQueryBuilder(): ResultQueryBuilder {
        return new DBResultQueryBuilder('users');
    }
}
```

# 6. Extending

This package uses a lot of interfaces and abstractions to be flexible as possible. You can basically extend or replace
anything. Extend existing class, or implement the interface directly if you need more control. Then just update the
construction logic, usually inside a dependency injection container.

Let's illustrate it on a caching example. Immediately, two options are presenting themselves to us. We could cache the
entire result set or individual sources. The former one might yield better performance at the expense of complicated
cache invalidation. The latter offers greater flexibility. You could for example cache only results coming from external
APIs.

Let's start with the first example and implement cached [SearchEngine](src/SearchEngine.php) using decorator pattern:

```php
use Artvys\Search\SearchEngine;

class CachedSearchEngine implements SearchEngine {
    private SearchEngine $engine;
    private Cache $cache;
    private CacheKeyGenerator $cacheKeyGenerator;
    private int $expiresInSeconds;

    public function __construct(SearchEngine $engine, Cache $cache, CacheKeyGenerator $cacheKeyGenerator, int $expiresInSeconds = 3600) {
        $this->engine = $engine;
        $this->cache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->expiresInSeconds = $expiresInSeconds;
    }

    public function search(string $query, int $limit): array {
        $key = $this->cacheKeyGenerator->generate($query, $limit);

        if (!$this->cache->has($key)) {
            $results = $this->engine->search($query, $limit);
            $this->cache->remember($key, $results, $this->expiresInSeconds);
        }

        return $this->cache->get($key);
    }
}
```

And then just adjust the construction logic:

```php
use Your\Project\CachedSearchEngine;

use Artvys\Search\Engines\Compiled\CompiledSearchEngine;

$engine = new CachedSearchEngine(
    new CompiledSearchEngine(...),
    new Cache(...),
    new CacheKeyGenerator(...),
    600
);
```

We will do something very similar for the second example. We will again utilize decorator pattern to implement a cached
[SearchSource](src/Engines/Compiled/SearchSource.php):

```php
use \Artvys\Search\Engines\Compiled\SearchSource;

class CachedSearchSource implements SearchSource {
    private SearchSource $source;
    private Cache $cache;
    private CacheKeyGenerator $cacheKeyGenerator;
    private int $expiresInSeconds;

    public function __construct(SearchSource $source, Cache $cache, CacheKeyGenerator $cacheKeyGenerator, int $expiresInSeconds = 3600) {
        $this->source = $source;
        $this->cache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->expiresInSeconds = $expiresInSeconds;
    }
    public function search(CompiledQuery $query, int $limit): array {
        $key = $this->cacheKeyGenerator->generate((string)$query, $limit);

        if (!$this->cache->has($key)) {
            $results = $this->source->search($query, $limit);
            $this->cache->remember($key, $results, $this->expiresInSeconds);
        }

        return $this->cache->get($key);
    }
}
```

Pretty much the same. Now just showcase the usage:

```php
use Your\Project\SearchSources\UsersSearchSource;

use Artvys\Search\Engines\Compiled\SearchSourceRegistry;

$registry = new SearchSourceRegistry();
$registry->register(new CachedSearchSource(new UsersSearchSource()), ['#']);
```

# 7. First party adapters

Yet to be written.
