### SomeProperty with AnyValue


```php
new SomeProperty(
	new PropertyValue( 'p42' ),
	new AnyValue()
);
```

```sql
SELECT subject_id FROM maisnak_string WHERE property_id = "P42";
```

### SomeProperty with ValueDescription


```php
new SomeProperty(
	new PropertyValue( 'p42' ),
	new ValueDescription( new StringValue( 'kittens' ) )
);
```

```sql
SELECT subject_id FROM maisnak_string WHERE property_id = "P42" AND hash = "kittens";
```

### SomeProperty with Disjunction


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
SELECT subject_id FROM maisnak_string WHERE property_id = "P42" AND ( hash = "kittens" OR hash = "bunnies" );
```

### Disjunction with two SomeProperty


```php
new Disjunction( [
		new SomeProperty(
        	new PropertyValue( 'p42' ),
        	new ValueDescription( new StringValue( 'kittens' ) )
        ),
        new SomeProperty(
			new PropertyValue( 'p23' ),
			new ValueDescription( new NumberValue( 1337 ) )
		),
	] )
```

```sql
SELECT subject_id FROM maisnak_string WHERE property_id = "P42" AND hash = "kittens"
UNION
SELECT subject_id FROM maisnak_number WHERE property_id = "P23" AND value = 1337;
```

### Conjunction with two SomeProperty


```php
new Conjunction( [
		new SomeProperty(
        	new PropertyValue( 'p42' ),
        	new ValueDescription( new StringValue( 'kittens' ) )
        ),
        new SomeProperty(
			new PropertyValue( 'p23' ),
			new ValueDescription( new NumberValue( 1337 ) )
		),
	] )
```

```sql

```