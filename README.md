# <img src=".github/logo.svg?sanitize=true" width="24" height="24" alt="Similar PHP"> Similar

This is an elementary library for working on identifying similar strings in PHP without using machine learning. It allows you to get groups of one topic from the transferred set of sentences. For example, combine news headlines from different publications, as Google News does. Want to see real use? No problem, this is just used by the Russian news aggregator https://tsmi.live

## Installation

Run this at the command line:

```php
$ composer require tabuna/similar
```

## Usage

You pass as input a set of rows and a minimum probability *(default 51%)*

```php
use Tabuna\Similar\Similar;

Similar::build([
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

## Array keys

The input array stores its keys so that you can do additional processing:

```php
Similar::build([
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

## Similarity percentage

Terms and conditions and proposals submitted can vary greatly from project to project. What worked great for one may be worse for another. To adapt to your conditions, you can pass the second argument, the value %, which will be the similarity and unification of groups.

```php
Similar::build([
    "Make or break approaching for EU-UK trade talks",
    "Make or break approaching for EU-UK trade talks 2",
], 95);
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
