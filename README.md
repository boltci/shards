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
composer require boltci/shard --dev
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

Run your tests with the `BOLTCI_SHARD` environment variable set to specify which shard to run. The format is `x/y`,
where `x` is the shard number and `y` is the total number of shards.

```bash
BOLTCI_SHARD=1/5 php artisan test
```

You can optionally set the `BOLTCI_SHARD_SEED` variable to any positive integers to shuffle the test order using a
deterministic seed (i.e. the same seed will always produce the same test order):

```bash
BOLTCI_SHARD=1/5 BOLTCI_SHARD_SEED=1 php artisan test
```

This is useful to help distribute tests more evenly across shards.

## Credits

- The first version of this code was first written for internal use at [Springloaded](https://springloaded.co).
- The use of the word "shard" and the "x/y" shard notation was later adapted
  from [Pest](https://pestphp.com/docs/4.x/sharding).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

