# <img src=".github/logo.svg?sanitize=true" width="24" height="24" alt="Similar PHP"> Similar

This is an elementary library for working on identifying similar strings in PHP without using machine learning. It allows you to get groups of one topic from the transferred set of sentences. For example, combine news headlines from different publications, as Google News does. Want to see real use? No problem, this is just used by the Russian news aggregator https://tsmi.live

## Installation

Run this at the command line:

```php
$ composer require tabuna/similar
```

## Usage

You pass as input a set of rows and a minimum probability (default 51%)

```php
use Tabuna\Similar\Similar;

$group = Similar::build([
    'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
    'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',

    // Superfluous word
    'Can Trump win with ‘fantasy’ electors bid? State GOP says no'
]);
```

As a result, there will be only one group containing headers:

```php
'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
```


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
