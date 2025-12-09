# Shards

[![Unit Tests](https://github.com/boltci/shards/actions/workflows/unit-tests.yml/badge.svg)](https://github.com/boltci/shards/actions/workflows/unit-tests.yml)
[![Integration Tests](https://github.com/boltci/shards/actions/workflows/integration-tests.yml/badge.svg)](https://github.com/boltci/shards/actions/workflows/integration-tests.yml)

Patch PHPUnit and Paratest to shard your Laravel test suite.

## Caveats

Both [Pest](https://github.com/pestphp/pest/) (as
of [version 4.0.0](https://github.com/pestphp/pest/releases/tag/v4.0.0)) and
[ParaTest](https://github.com/paratestphp/paratest) (as
of [version 7.13.0](https://github.com/paratestphp/paratest/releases/tag/v7.13.0)) support test sharding natively, in
more robust ways
than this package provides.

You should only use this as a last resort if your version of testing framework does not support sharding natively.

## Supported Versions

- PHP 8.2 or higher
- Laravel 10.x or higher
- PHPUnit 10.x or higher
- ParaTest 7.2.0 or higher

## Installation

Install the package via Composer:

```bash
composer require boltci/shards --dev
```

## Usage

### 1. Patch PHPUnit

Before running your tests, patch PHPUnit to enable sharding:

```bash
php artisan shards:patch-phpunit
```

This command modifies the PHPUnit test suite builder to shard tests based on your environment configuration.

### 2. Patch ParaTest

If you're using ParaTest versions between 7.2.0 and 7.4.2, you may need to apply a compatibility patch:

```bash
php artisan shards:patch-paratest
```

**Note**: ParaTest 7.4.3 and above have the issue fixed and don't require patching.

### 3. Run Your Tests

Run your tests with the `SHARD` environment variable set to specify which shard to run. The format is `x/y`,
where `x` is the shard number and `y` is the total number of shards.

```bash
SHARD=1/5 php artisan test
```

You can optionally set the `SEED` variable to any positive integers to shuffle the test order using a
deterministic seed (i.e. the same seed will always produce the same test order):

```bash
SHARD=1/5 SEED=1 php artisan test
```

When using ParaTest (by passing `--parallel`), include the `--functional` flag to parallelize by Test to avoid overlaps
between shards:

```bash
SHARD=1/5 SEED=1 php artisan test --parallel --functional
```

This is useful to help distribute tests more evenly across shards.

## Credits

- The first version of this code was first written for internal use at [Springloaded](https://springloaded.co).
- The use of the word "shard" and the "x/y" shard notation was later adapted
  from [Pest](https://pestphp.com/docs/4.x/sharding).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

