<?php

namespace Fabis94\Twiddle;

use Fabis94\Twiddle\Internal\Exceptions\TwiddleException;
use Generator;

/**
 * Chase's Twiddle algorithm for returning all combinations of m out of n objects.
 *
 * Algorithm in C: http://www.netlib.no/netlib/toms/382
 */
class Twiddle
{
    /** @var int */
    private int $m;
    /** @var int */
    private int $n;
    /** @var array<int> */
    private array $p;

    /**
     * Twiddle constructor.
     * @param int $setSize Size of set
     * @param int $combinationSize Combination size
     * @throws TwiddleException
     */
    public function __construct(int $setSize, int $combinationSize)
    {
        if (!$setSize || $setSize <= 0) {
            throw new TwiddleException("Set size must be a positive integer.");
        }

        if (!$combinationSize || $combinationSize <= 0) {
            throw new TwiddleException("Combination size must be a positive integer.");
        }

        if ($combinationSize > $setSize) {
            throw new TwiddleException("Set size must but equal to or larger than combination size.");
        }

        $this->m = $combinationSize;
        $this->n = $setSize;
        $this->p = [];
        $this->p[0] = $setSize + 1;

        for ($i = 1; $i !== $setSize - $combinationSize + 1; $i ++) {
            $this->p[$i] = 0;
        }

        while ($i != $setSize + 1) {
            $this->p[$i] = $i + $combinationSize - $setSize;
            $i++;
        }

        $this->p[$setSize+1] = -2;
    }

    /**
     * Retrieve generator for iteratively building combinations. This way you can stop building when needed.
     * @param array $values Array of values of size 'n'
     * @return Generator
     * @throws TwiddleException
     */
    public function getCombinationGenerator(array $values): Generator
    {
        if (count($values) !== $this->n) {
            throw new TwiddleException("The value array must have the same length as 'n' - " . $this->n);
        }

        // Create init params
        $x = 0;
        $y = 0;
        $z = 0;
        $p = $this->p;

        // Build first element, from which the other ones will be built
        // Same item gets adjusted on each iteration and its copy added to the array
        $item = array_slice($values, $this->n - $this->m);

        // Yield first element
        yield $item;

        while (!$this->twiddle($x, $y, $z, $p)) {
            $item[$z] = $values[$x];
            yield $item;
        }
    }

    /**
     * Get all combinations
     * @param array $values Array of values of size 'n'
     * @return array Combinations
     * @throws TwiddleException
     */
    public function getAllCombinations(array $values): array
    {
        $results = [];
        $generator = $this->getCombinationGenerator($values);
        foreach ($generator as $result) {
            $results[] = $result;
        }

        return $results;
    }

    /**
     * The actual Twiddle algorithm
     * @param int $x
     * @param int $y
     * @param int $z
     * @param array $p
     * @return bool
     */
    private function twiddle(int &$x, int &$y, int &$z, array &$p): bool
    {
        $j = 1;

        while ($p[$j] <= 0) {
            $j++;
        }

        if ($p[$j-1] === 0) {
            for ($i = $j - 1; $i !== 1; $i--) {
                $p[$i] = -1;
            }

            $p[$j] = 0;
            $x = 0;
            $z = 0;
            $p[1] = 1;
            $y = $j - 1;
        } else {
            if ($j > 1) {
                $p[$j - 1] = 0;
            }

            do {
                $j++;
            } while ($p[$j] > 0);

            $k = $j - 1;
            $i = $j;

            while ($p[$i] === 0) {
                $p[$i++] = -1;
            }

            if ($p[$i] === -1) {
                $p[$j] = $p[$k];
                $z = $p[$k] - 1;
                $x = $i - 1;
                $y = $k - 1;
                $p[$k] = -1;
            } else {
                if ($i === $p[0]) {
                    return true;
                }

                $p[$j] = $p[$i];
                $z = $p[$i] - 1;
                $p[$i] = 0;
                $x = $j - 1;
                $y = $i - 1;
            }
        }

        return false;
    }
}