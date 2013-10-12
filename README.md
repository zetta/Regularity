## Regularity - Regular expressions for humans

Regularity is a fork friendly regular expression builder for php based in [andrewberls Regularity](https://github.com/andrewberls/regularity) . Regular expressions are a powerful way of
pattern-matching against text, but too often they are 'write once, read never'. After all, who wants to try and deciper

```php
/^[0-9]{3}-[A-Za-z]{2}#?[a|b]a{2,4}\$$/
```

when you could express it as:

```php
$regularity = new Regularity()
$regularity
    ->startWith(3, ':digits')
    ->then('-')
    ->then(2, ':letters')
    ->maybe('#')
    ->oneOf(['a','b'])
    ->between(2, 4, 'a')
    ->endWith('$')
```

While taking up a bit more space, Regularity expressions are much more readable than their cryptic counterparts.

### Installation

```
composer install zetta/regularity
```

### Usage

@todo

### DSL methods

Most methods accept the same pattern signature - you can either specify a patterned constraint such as `then("xyz")`,
or a numbered constraint such as `then(2, ':digits')`. The following special identifers are supported:

````
:digit        => '[0-9]'
:lowercase    => '[a-z]'
:uppercase    => '[A-Z]'
:letter       => '[A-Za-z]'
:alphanumeric => '[A-Za-z0-9]'
:whitespace   => '\s'
:space        => ' '
:tab          => '\t'
```

It doesn't matter if the identifier is pluralized, i.e. `then(2, ':letters')` works in addition to `then(1, ':letter')`


The following methods are supported:

`startWith(pattern)`: The line must start with the specified pattern

`append(pattern)`: Append a pattern to the end (Also aliased to `then`)

`endWith(pattern)`: The line must end with the specified pattern

`maybe(pattern)`: Zero or one of the specified pattern

`oneOf(values)`: Specify an alternation, e.g. `oneOf(['a', 'b', 'c'])`

`between(range, pattern)`: Specify a bounded repetition, e.g. `between(2, 4, ':digits')`

`zeroOrMore(pattern)`: Specify that the pattern or identifer should appear zero or many times

`oneOrMore(pattern)`: Specify that the pattern or identifer should appear one or many times

`atLeast(n, pattern)`: Specify that the pattern or identifer should appear n or more times

`atMost(n, pattern)`: Specify that the pattern or identifer should appear n or less times

The DSL methods are chainable, meaning they return `Regularity` object.