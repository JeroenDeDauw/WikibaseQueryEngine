# Wikibase QueryEngine

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/WikibaseQueryEngine.png?branch=master)](http://travis-ci.org/JeroenDeDauw/WikibaseQueryEngine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/WikibaseQueryEngine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/WikibaseQueryEngine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/WikibaseQueryEngine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/WikibaseQueryEngine/?branch=master)
[![Dependency Status](https://www.versioneye.com/php/jeroen:query-engine/dev-master/badge.svg)](https://www.versioneye.com/php/jeroen:query-engine/dev-master)

On [Packagist](https://packagist.org/packages/jeroen/query-engine):
[![Latest Stable Version](https://poser.pugx.org/jeroen/query-engine/version.png)](https://packagist.org/packages/jeroen/query-engine)
[![Download count](https://poser.pugx.org/jeroen/query-engine/d/total.png)](https://packagist.org/packages/jeroen/query-engine)

**Wikibase QueryEngine** is a library that supports running [Ask](https://github.com/JeroenDeDauw/Ask)
queries against a collection of [Wikibase](http://wikiba.se) entities.

Recent changes can be found in the [release notes](RELEASE-NOTES.md).

Note that this is a fork of the original Wikibase Query engine, which has the `wikibase/query-engine`
package name. This version is ahead of the original, which is no longer actively developed.

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `jeroen/query-engine` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Wikibase QueryEngine 1.0:

```js
    {
        "require": {
            "jeroen/query-engine": "1.0.*"
        }
    }
```

### Manual

Get the Wikibase QueryEngine code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Wikibase QueryEngine library by including its entry point:
WikibaseQueryEngine.php.

## Running the tests

For tests only

    composer test

For style checks only

	composer cs

For a full CI run

	composer ci

## Usage

## CLI

To get a list of available CLI commands, execute this in the root directory:

    php queryengine

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

The schema definition is dynamically build in StoreSchema.php.

#### Value snak tables

There is a value snak table per type of data value the store is configured to support.

All data value tables have a set of additional fields that are specific to the type of
data value they store. For the types of data value natively supported by the store.

#### Valueless snak tables

Additional fields:

* snak_type, int: type of the snak, ie "no value"

## Authors

Wikibase QueryEngine has been written by Jeroen De Dauw, and by the Wikidata team for the
[Wikidata project](https://wikidata.org/).

## Links

* [Wikibase QueryEngine on Packagist](https://packagist.org/packages/jeroen/query-engine)
* [Wikibase QueryEngine on Ohloh](https://www.ohloh.net/p/wikibasequeryengine/)
* [Wikibase QueryEngine on GitHub](https://github.com/JeroenDeDauw/WikibaseQueryEngine)
* [TravisCI build status](https://travis-ci.org/JeroenDeDauw/WikibaseQueryEngine)

## Related projects

* [Wikibase](http://wikiba.se)
* [Semantic MediaWiki](https://semantic-mediawiki.org/)
