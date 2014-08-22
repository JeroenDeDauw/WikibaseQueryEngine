# Wikibase QueryEngine release notes

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
