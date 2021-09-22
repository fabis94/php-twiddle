# php-twiddle

Chase's Twiddle algorithm for returning all `m`-combinations of an array of size `n`. 
More info about the original algorithm can be found [here](http://dl.acm.org/citation.cfm?id=362502) and a C
implementation can be found [here](http://www.netlib.org/toms-2014-06-10/382).

Unlike other recursive algorithms for building combinations this one allows you to build combinations iteratively and
thus stop at any point when you've had enough without having to build all of the combinations first. 
This package provides both a PHP `\Generator` and a standard method for retrieving

## Requirements

PHP7.4 & PHP8

## How to use

The following code will initialize a new Twiddle instance for building 2-combinations of a set of size 10.

```php
$setSize = 10;
$combinationSize = 2;
$twiddle = new Twiddle($setSize, $combinationSize);
```

After initializing the instance you can run the algorithm on all arrays of size `n` (10 in the example).

```php
// Get all possible combinations
$values = [1,2,3,4,5,6,7,8,9,0]
$allCombinations = $twiddle->getAllCombinations($values);

// Get first three combinations
$values = ['a','b','c','d','e','f','g','h','j','k']
$combinationGenerator = $twiddle->getCombinationGenerator($values);
$firstThree = [];
$i = 0;
foreach ($combinationGenerator as $combination) {
    if ($i >= 3) break;
    
    $firstThree[] = $combination;
    $i++;
}
```

## Local development

Run `docker-compose up -d` to run the docker environment. You can then SSH into one of the PHP containers (one has php7.4 and the other one has php8.0) to run `composer`, `tests` etc.: `docker-compose exec php7 bash` or `docker-compose exec php8 bash`

The codebase is going to be mounted onto `/app` inside the containers.

Run tests inside container with `composer test`.