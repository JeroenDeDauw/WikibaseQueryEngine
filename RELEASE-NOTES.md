# Wikibase QueryEngine release notes

## Version 0.5 (2015-11-04)

* Wikibase DataModel 4.x is now required
* Dropped PHP 5.3 and PHP 5.4 support
* The methods in QueryStoreWriter now accept `EntityDocument` rather than just `Entity`
* The `SQLStore` now accepts an optional `LoggerInterface` in its constructor
* Errors during installation or removal of the SQLStore are now logged and no longer abort execution
* Errors during transcations will now cause the transactions to be rolled back
* Renamed `QueryEngine` to `DescriptionMatchFinder`
* Renamed `SQLStore::newQueryEngine` to `SQLStore::newDescriptionMatchFinder`
* Renamed `QueryStoreWithDependencies::newQueryEngine` to `QueryStoreWithDependencies::newDescriptionMatchFinder`

## Version 0.3.1 (2014-08-25)

* Allow installation with Wikibase DataModel 0.9.x

## Version 0.3 (2014-08-22)

#### Breaking changes

* The data value tables no longer have a "label field"
* The hash used for LatLong values has been changed
* The hash used for IRI values has been changed
* The field lengths for EntityId values have been increased
* The field types for GlobeCoordinate, LatLong, Number and Quantity values have been changed to FLOAT
* The `CliApplicationFactory` class is now package private
* The lengths of the indexes for Iri values have been changed
* The table for Quanity values now has an additional `value_unit` field

#### Additions and improvements

* Added indexes for number, string and monoligual values
* Added `EntitiesImporter` service object for importing entities

## Version 0.2 (2014-06-21)

Initial release with these features:

* Indexing of main snak values of type
    * BooleanValue
    * EntityIdValue
    * IriValue
    * LatLongValue
    * MonolingualTextValue
    * NumberValue
    * StringValue and TimeValue
* Support for simple equality queries
