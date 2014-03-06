# Wikibase QueryEngine

[![Build Status](https://secure.travis-ci.org/wmde/WikibaseQueryEngine.png?branch=master)](http://travis-ci.org/wmde/WikibaseQueryEngine)
[![Coverage Status](https://coveralls.io/repos/wmde/WikibaseQueryEngine/badge.png?branch=master)](https://coveralls.io/r/wmde/WikibaseQueryEngine?branch=master)
[![Dependency Status](https://www.versioneye.com/package/php--wikibase--query-engine/badge.png)](https://www.versioneye.com/package/php--wikibase--query-engine)

On [Packagist](https://packagist.org/packages/wikibase/query-engine):
[![Latest Stable Version](https://poser.pugx.org/wikibase/query-engine/version.png)](https://packagist.org/packages/wikibase/query-engine)
[![Download count](https://poser.pugx.org/wikibase/query-engine/d/total.png)](https://packagist.org/packages/wikibase/query-engine)

Component containing query answering code for
[Ask](https://www.mediawiki.org/wiki/Extension:Ask)
queries against a collection of
[Wikibase](https://www.mediawiki.org/wiki/Wikibase)
entities.

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `wikibase/query-engine` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Wikibase QueryEngine 1.0:

    {
        "require": {
            "wikibase/query-engine": "1.0.*"
        }
    }

### Manual

Get the Wikibase QueryEngine code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Wikibase QueryEngine library by including its entry point:
WikibaseQueryEngine.php.

## Usage

The public interfaces in this component are everything directly in the Wikibase\QueryEngine.
Other classes and interfaces are typically package private, and should not be used or known
about outside of the package. Each store implementation has its own list of additional public
classes.

### SQLStore

Public classes of the SQLStore:

Needed for construction:

* SQLStore\Store
* SQLStore\StoreConfig
* SQLStore\DataValueHandlers

Needed for extension:

* SQLStore\DataValueTable
* SQLStore\DataValueHandler

Constructing an SQLStore:

```php
use Wikibase\QueryEngine\SQLStore\Store;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;

$dvHandlers = new DataValueHandlers();

$config = new StoreConfig(
	'My awesome query store',
	'nyan_',
	$dvHandlers->getHandlers()
);

$store = new Store( $config, $queryInterface, $tableBuilder );
```

Where

* $queryInterface is a Wikibase\Database\QueryInterface\QueryInterface
* $tableBuilder is a Wikibase\Database\Schema\TableBuilder

## SQLStore internal structure

### Table: entities

* id, string: serialization of the entities id
* type, string: type of the entity

### Snak tables

All snak tables have the following fields:

* row_id, int
* subject_id, string
* subject_type, string
* property_id, string
* statement_rank, int

The schema definition is dynamically build in Schema.php.

#### Value snak tables

There is a value snak table per type of data value the store is configured to support.

All data value tables have a set of additional fields that are specific to the type of
data value they store. For the types of data value natively supported by the store,
you can find the table definitions (without the common fields) in the
Wikibase\QueryEngine\SQLStore\DataValueHandlers class.

#### Valueless snak tables

Additional fields:

* snak_type, int: type of the snak, ie "no value"
* snak_role, int: role of the snak, ie "qualifier" or "main snak"

## Tests

This library comes with a set up PHPUnit tests that cover all non-trivial code. You can run these
tests using the PHPUnit configuration file found in the root directory. The tests can also be run
via TravisCI, as a TravisCI configuration file is also provided in the root directory.

## Authors

Wikibase QueryEngine has been written by [Jeroen De Dauw](https://www.mediawiki.org/wiki/User:Jeroen_De_Dauw)
as [Wikimedia Germany](https://wikimedia.de) employee for the [Wikidata project](https://wikidata.org/).

## Links

* [Wikibase QueryEngine on Packagist](https://packagist.org/packages/wikibase/query-engine)
* [Wikibase QueryEngine on Ohloh](https://www.ohloh.net/p/wikibasequeryengine/)
* [Wikibase QueryEngine on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Wikibase_QueryEngine)
* [TravisCI build status](https://travis-ci.org/wmde/WikibaseQueryEngine)
* [Latest version of the readme file](https://github.com/wmde/WikibaseQueryEngine/blob/master/README.md)

## Related projects

* [Ask JavaScript implementation](https://github.com/JeroenDeDauw/AskJS)
* [Wikibase](https://www.mediawiki.org/wiki/Wikibase)
* [Semantic MediaWiki](https://semantic-mediawiki.org/)