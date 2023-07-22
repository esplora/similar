# <img src=".github/logo.svg?sanitize=true" width="24" height="24" alt="Similar PHP"> Similar

![Unit tests](https://github.com/esplora/similar/workflows/Unit%20tests/badge.svg)

This is an elementary library for working on identifying similar strings in PHP without using machine learning. It allows you to get groups of one topic from the transferred set of sentences. For example, combine news headlines from different publications, as Google News does. 


<!-- Want to see real use? No problem, this is just used by the real news aggregator https://tsmi.live -->

## Installation

Run this at the command line:

```php
$ composer require esplora/similar
```

## Usage

We need to create an object by passing a closure function as an argument, which checks if two strings are similar:

```php
use Esplora\Similar\Similar;

$similar = new Similar(function (string $a, string $b) {
    similar_text($a, $b, $copy);

    return 51 < $copy;
});
```

> Note that you don't need to use `similar_text`. You can use other implementations like `soundex` or something else.


Then we have to call the `findOut` method passing it a one-dimensional array with strings:

```php
$similar->findOut([
    'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
    'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',

    // Superfluous word
    'Can Trump win with ‘fantasy’ electors bid? State GOP says no',
]);
```

As a result, there will be only one group containing headers:

```php
'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
```

## Keys

The input array stores its keys so that you can do additional processing:

```php
$similar->findOut([
  'kos' => "Trump acknowledges Biden's win in latest tweet",
  'foo' => 'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
  'baz' => 'Trump says Biden won but again refuses to concede',
  'bar' => 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
]);
```

The result will be two groups:

```php
[
  'foo' => 'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
  'bar' => 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
],
[
  'baz' => 'Trump says Biden won but again refuses to concede',
  'kos' => "Trump acknowledges Biden's win in latest tweet",
],
```

## Objects

It is also possible to pass objects to evaluate more complex conditions. Each passed object must be able to cast to a string via the `__toString()` method.


```php
$similar->findOut([
    new FixtureStingObject('Lorem ipsum dolor sit amet, consectetur adipiscing elit.'),
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
