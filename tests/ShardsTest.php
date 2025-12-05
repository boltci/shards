<?php

namespace BoltCI\Shards\Tests;

use BoltCI\Shards\Shards;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class ShardsTest extends TestCase
{
    #[Test, DataProvider('provider')]
    public function it_shards_tests(
        array $tests,
        string $key,
        array $expectedTests
    ): void {
        // Given
        $suite = $this->createTestSuite('Test Suite', $tests);

        // When
        $shards = new Shards($suite);
        $shard = $shards->get($key);

        // Then
        $this->assertEqualsCanonicalizing($expectedTests, collect($shard->tests())->pluck('id')->all());
    }

    public static function provider(): array
    {
        return [
            [
                'tests' => ['t1', 't2', 't3', 't4'],
                'key' => '1/1',
                'expectedTests' => ['t1', 't2', 't3', 't4'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4'],
                'key' => '1/2',
                'expectedTests' => ['t1', 't2'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4'],
                'key' => '2/2',
                'expectedTests' => ['t3', 't4'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5'],
                'key' => '1/2',
                'expectedTests' => ['t1', 't2'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5'],
                'key' => '2/2',
                'expectedTests' => ['t3', 't4', 't5'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5'],
                'key' => '1/3',
                'expectedTests' => ['t1'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5'],
                'key' => '2/3',
                'expectedTests' => ['t2'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5', 't6', 't7', 't8', 't9', 't10'],
                'key' => '3/3',
                'expectedTests' => ['t7', 't8', 't9', 't10'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5', 't6', 't7', 't8', 't9', 't10'],
                'key' => '9/10',
                'expectedTests' => ['t9'],
            ],
            [
                'tests' => ['t1', 't2', 't3', 't4', 't5', 't6', 't7', 't8', 't9', 't10'],
                'key' => '10/10',
                'expectedTests' => ['t10'],
            ],
        ];
    }

    protected function createTestSuite(string $name, array $tests): TestSuite
    {
        $suite = TestSuite::empty($name);

        collect($tests)->each(fn (string $id) => $suite->addTest($this->createTestCase($id)));

        return $suite;
    }

    protected function createTestCase(string $id): TestCase
    {
        return new class('test_it_works', $id) extends TestCase
        {
            public function __construct(string $name, public string $id)
            {
                parent::__construct($name);
            }

            public function test_it_works() {}
        };
    }
}
