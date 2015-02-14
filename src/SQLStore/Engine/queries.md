### AnyValue

Human query: all entities

```php
new AnyValue()
```

Not supported, since we do not have a table with all subject ids.

### SomeProperty with AnyValue

Human query: entities that have a value for p42

```php
new SomeProperty(
	new PropertyValue( 'p42' ),
	new AnyValue()
);
```

```sql
SELECT subject_id FROM mainsnak_string WHERE property_id = "P42";
```

### SomeProperty with ValueDescription

Human query: entities that have kittens as value for p42

```php
new SomeProperty(
	new PropertyValue( 'p42' ),
	new ValueDescription( new StringValue( 'kittens' ) )
);
```

```sql
SELECT subject_id FROM mainsnak_string WHERE property_id = "P42" AND hash = "kittens";
```

### SomeProperty with Disjunction

Human query: entities that have kittens or bunnies as value for p42

```php
new SomeProperty(
	new PropertyValue( 'p42' ),
	new Disjunction( [
		new ValueDescription( new StringValue( 'kittens' ) ),
		new ValueDescription( new StringValue( 'bunnies' ) )
	] )
);
```

```sql
SELECT subject_id FROM mainsnak_string
WHERE property_id = "P42" AND ( hash = "kittens" OR hash = "bunnies" );
```

### Disjunction with two SomeProperty

Human query: entities that have kittens as value for p42 or 1337 as value for p23

```php
new Disjunction( [
	new SomeProperty(
		new PropertyValue( 'p42' ),
		new ValueDescription( new StringValue( 'kittens' ) )
	),
	new SomeProperty(
		new PropertyValue( 'p23' ),
		new ValueDescription( new NumberValue( 1337 ) )
	)
] )
```

```sql
SELECT subject_id FROM mainsnak_string WHERE property_id = "P42" AND hash = "kittens"
UNION
SELECT subject_id FROM mainsnak_number WHERE property_id = "P23" AND value = 1337;
```

### Conjunction with two SomeProperty

Human query: entities that have kittens as value for p42 and 1337 as value for p23

```php
new Conjunction( [
	new SomeProperty(
		new PropertyValue( 'p42' ),
		new ValueDescription( new StringValue( 'kittens' ) )
	),
	new SomeProperty(
		new PropertyValue( 'p23' ),
		new ValueDescription( new NumberValue( 1337 ) )
	)
] )
```

```sql
SELECT mainsnak_string.subject_id FROM mainsnak_string
INNER JOIN mainsnak_number ON mainsnak_string.subject_id = mainsnak_number.subject_id
WHERE mainsnak_string.property_id = "P42" AND mainsnak_string.hash = "kittens"
AND mainsnak_number.property_id = "P23" AND mainsnak_number.value = 1337;
```

### Disjunction with nested Conjunction

Human query: entities with a value for p1 or with a value for both p42 and p23

```php
new Disjunction( [
	new SomeProperty(
		new PropertyValue( 'p1' ),
		new AnyValue()
	),
	new Conjunction( [
		new SomeProperty(
			new PropertyValue( 'p42' ),
			new AnyValue()
		),
		new SomeProperty(
			new PropertyValue( 'p23' ),
			new AnyValue()
		)
	] )
] )
```

```sql
SELECT subject_id FROM qe_mainsnak_string WHERE property_id = "P1"
UNION
SELECT qe_mainsnak_string.subject_id FROM qe_mainsnak_string
INNER JOIN qe_mainsnak_number ON qe_mainsnak_string.subject_id = qe_mainsnak_number.subject_id
WHERE qe_mainsnak_string.property_id = "P42" AND qe_mainsnak_number.property_id = "P23";
```