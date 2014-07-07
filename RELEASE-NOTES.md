# Wikibase QueryEngine release notes

## Version 0.3 (dev)

#### Breaking changes

* The data value tables no longer have a "label field"
* The hash used for LatLong values has been changed
* The hash used for IRI values has been changed
* The `CliApplicationFactory` class is now package private

#### Additions and improvements

* Added indexes for number, string and monoligual values
* Added `EntitiesImporter` service object for importing entities with `ImportEntitiesCommand` as CLI foundation

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
