<?php

namespace BoltCI\Shards;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class Shards
{
    public function __construct(protected TestSuite $testSuite) {}

    /**
     * @param  string  $key  The shard key, e.g. "1/3"
     * @param  int|null  $shuffleSeed  An optional seed for shuffling the tests, e.g. 1
     */
    public function get(string $key, ?int $shuffleSeed = null): TestSuite
    {
        [$shardNumber, $shardsCount] = $this->parseKey($key);

        $allTests = $this->flatten($this->testSuite);

        if ($shuffleSeed) {
            $allTests = $this->shuffle($allTests, $shuffleSeed);
        }

        [$offset, $length] = $this->determineSlice(count($allTests), $shardNumber, $shardsCount);

        $shard = TestSuite::empty("Shard $shardNumber/$shardsCount");

        $shard->setTests(array_slice($allTests, $offset, $length));

        return $shard;
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function parseKey(string $key): array
    {
        if (! preg_match('/^(\d+)\/(\d+)$/', $key, $matches)) {
            throw new InvalidArgumentException('The shard key must be in the format "current/total".');
        }

        [$current, $total] = [$matches[1], $matches[2]];

        if ($current <= 0 || $total <= 0 || $current > $total) {
            throw new InvalidArgumentException('The current shard must be a non-negative integer less than the total number of shards.');
        }

        return [(int) $current, (int) $total];
    }

    protected function determineSlice(int $totalTests, int $shardNumber, int $shardsCount): array
    {
        $length = floor($totalTests / $shardsCount);
        $offset = ($shardNumber - 1) * $length;

        if ($shardNumber === $shardsCount) {
            $length += $totalTests % $shardsCount;
        }

        return [$offset, $length];
    }

    protected function flatten(TestCase|TestSuite $test): array
    {
        if ($test instanceof TestCase) {
            return [$test];
        }

        $flattened = [];

        foreach ($test->tests() as $nestedTest) {
            array_push($flattened, ...$this->flatten($nestedTest));
        }

        return $flattened;
    }

    protected function shuffle(array $items, int $seed): array
    {
        $generatePseudoRandomNumberUsingLinearCongruentialGenerator = function () use (&$seed) {
            $seed = ($seed * 1103515245 + 12345) & 0x7FFFFFFF;

            return $seed / 0x7FFFFFFF;
        };
        $shuffled = $items;
        for ($i = count($shuffled) - 1; $i > 0; $i--) {
            $randomIndex = (int) floor($generatePseudoRandomNumberUsingLinearCongruentialGenerator() * ($i + 1));
            [$shuffled[$i], $shuffled[$randomIndex]] = [$shuffled[$randomIndex], $shuffled[$i]];
        }

        return $shuffled;
    }
}
