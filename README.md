## Regularity - Regular expressions for humans

Regularity is a fork friendly regular expression builder for php based in [andrewberls Regularity](https://github.com/andrewberls/regularity) . Regular expressions are a powerful way of
pattern-matching against text, but too often they are 'write once, read never'. After all, who wants to try and deciper

```php
if (preg_match("/^[0-9]{3}\-[A-Za-z]{2}#?(a|b)a{2,4}\$$/", $string))
{
    // cool stuff
}
```

when you could express it as:

```php
use Zetta\Regularity\Regularity;

$regularity = new Regularity()
$regularity
    ->startWith(3, ':digits')
    ->then('-')
    ->then(2, ':letters')
    ->maybe('#')
    ->oneOf(['a','b'])
    ->between(2, 4, 'a')
    ->endWith('$')
;
if ($regularity->test($string))
{
    // cool stuff
}
```
or using the Pattern Object

```php
use Zetta\Regularity\Regularity;
use Zetta\Regularity\Pattern;

$regularity = new Regularity()
$regularity
    ->startWith(3, Pattern:DIGITS)
    ->then('-')
    ->then(2, Pattern:LETTERS)
    ->maybe('#')
    ->oneOf(['a','b'])
    ->between(2, 4, 'a')
    ->endWith('$')
;
```

While taking up a bit more space, Regularity expressions are much more readable than their cryptic counterparts.

### Installation

add the `zetta/regularity` dependency to your composer.json file
```json
    "require": {
        "zetta/regularity" : "*"
    }
```
and run the installer

```bash
composer install
```

If you dont use composer you can simply copy the contents of `src` folder to your vendors

### Usage

The usage is simple you only need to create an instance of `Regularity` object and start writing rules. See [Methods](#methods)



### Methods

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

`test(string)`: The test() method tests for a match in a string

The methods are chainable, meaning they return `Regularity` object.