<?php

namespace Tests\Unit;

use Fabis94\Twiddle\Internal\Exceptions\TwiddleException;
use Fabis94\Twiddle\Twiddle;
use PHPUnit\Framework\TestCase;

final class TwiddleTestCase extends TestCase
{
    public function testCantInstantiateWithNegativeN()
    {
        // Set up
        $this->expectException(TwiddleException::class);

        // Test
        $twiddle = new Twiddle(-1, 1);
    }

    public function testCantInstantiateWithNegativeM()
    {
        // Set up
        $this->expectException(TwiddleException::class);

        // Test
        $twiddle = new Twiddle(1, -1);
    }

    public function testCantInstantiateWithMLargerThanN()
    {
        // Set up
        $this->expectException(TwiddleException::class);

        // Test
        $twiddle = new Twiddle(1, 2);
    }

    public function testCantRunWithValueSizeDifferentThanN()
    {
        // Set up
        $this->expectException(TwiddleException::class);
        $twiddle = new Twiddle(5, 2);

        // Test
        $twiddle->getAllCombinations([1,2]);
    }

    /**
     * @dataProvider combinationProvider
     */
    public function testAllCombinations($values, $combinationSize, $expectedResult)
    {
        // Set up
        $twiddle = new Twiddle(count($values), $combinationSize);

        // Test
        $output = $twiddle->getAllCombinations($values);

        // Check results
        $this->assertCombinationsEqual($expectedResult, $output);
    }

    /**
     * @dataProvider combinationProvider
     */
    public function testCombinationGenerator($values, $combinationSize, $expectedResult)
    {
        // Set up
        $twiddle = new Twiddle(count($values), $combinationSize);
        $sortedExpectedResult = $this->normalizeCombinationsArray($expectedResult);

        // Test and check results
        $generator = $twiddle->getCombinationGenerator($values);
        foreach ($generator as $combination) {
            $this->assertContains(sort($combination), $expectedResult);
        }
    }

    public function testMultipleRunsUsingSameInstance()
    {
        // Set up
        $values1 = [1,2,3];
        $expectedResult1 = $this->normalizeCombinationsArray([
            [1,2], [2,3], [3,1]
        ]);
        $values2 = ['a', 'b', 'c'];
        $expectedResult2 = $this->normalizeCombinationsArray([
           ['a','b'], ['b', 'c'], [ 'c', 'a']
        ]);
        $twiddle = new Twiddle(3, 2);

        // Test first values
        $combinations1 = $twiddle->getAllCombinations($values1);
        $this->assertCombinationsEqual($expectedResult1, $combinations1);
        $generator1 = $twiddle->getCombinationGenerator($values1);
        foreach ($generator1 as $combination) {
            $this->assertContains(sort($combination), $expectedResult1);
        }

        // Test second values
        $combinations2 = $twiddle->getAllCombinations($values2);
        $this->assertCombinationsEqual($expectedResult2, $combinations2);
        $generator2 = $twiddle->getCombinationGenerator($values2);
        foreach ($generator2 as $combination) {
            $this->assertContains(sort($combination), $expectedResult2);
        }
    }

    /**
     * Provides some combinations and expected results to test
     * @return array
     */
    public function combinationProvider()
    {
        // $values, $combinationSize, $expectedResult
        return [
            [[1,0], 2, [[1,0]]],
            [[1,2,3,4,5], 2, [[1,2],[1,3],[1,4],[1,5],[2,3],[2,4],[2,5],[3,4],[3,5],[4,5]]],
            [[1,2,3,4,5], 3, [
                [1,2,3],[1,2,4],[1,2,5],[1,3,4],[1,3,5],[1,4,5],[2,3,4],[2,3,5],[2,4,5],[3,4,5]
            ]],
            [['AB', 'BC', 'CD'], 2, [
                ['AB','BC'],['AB','CD'],['BC','CD']
            ]]
        ];
    }

    /**
     * Assert that combinations are equal
     * @param array $expectedResult
     * @param array $output
     */
    protected function assertCombinationsEqual($expectedResult, $output)
    {
        $expectedResult = $this->normalizeCombinationsArray($expectedResult);
        $output = $this->normalizeCombinationsArray($output);
        $this->assertEquals($expectedResult, $output);
    }

    /**
     * Normalize combinations array to the same sorting
     * @param $array
     * @return mixed
     */
    protected function normalizeCombinationsArray($array)
    {
        // Sort combination values
        foreach ($array as &$combination) {
            sort($combination);
        }

        // Sort wrapping array
        usort($array, function ($combA, $combB) {
            $keyA = implode("", $combA);
            $keyB = implode("", $combB);
            return $keyA > $keyB;
        });

        return $array;
    }
}
