#!/bin/bash

# Remove any existing project directory
rm -rf project

# Get Laravel version from first argument or default to 12.0
LARAVEL_VERSION=${1:-12.0}

# Install Laravel
composer create-project laravel/laravel project $LARAVEL_VERSION

# Change to the project directory
cd project

# Install Paratest
composer require brianium/paratest --dev

# Install BoltCI's Runner
branch=${2:-main}

if [ -n "$3" ]; then
  version="dev-$branch#$3"
else
  version="dev-$branch"
fi

composer config repositories.boltci/shards path ../
composer require "boltci/shards:$version"

# Patch PHPUnit and Paratest
php artisan shards:patch-phpunit
php artisan shards:patch-paratest

# Define color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
NO_COLOR='\033[0m'

assert() {
    local cmd="$1"
    local pattern="$2"
    local message="$3"

    output="$(eval "$cmd" 2>&1 | tee /dev/stderr)"

    printf "\n"

    if echo "$output" | grep -Eq "$pattern"; then
        printf "[${GREEN}✓ Pass${NO_COLOR}] Found $message.\n"
    else
        printf "[${RED}x Fail${NO_COLOR}] Did not find $message.\n"
        exit 1
    fi

    printf "\n"
}

# Usage

assert \
    "php artisan test --parallel --functional" \
    "Tests: 2, Assertions: 2|2 tests, 2 assertions" \
    "2 tests and 2 assertions without BOLT"

assert \
    "SHARD=1/2 php artisan test --parallel --functional" \
    "Tests: 1, Assertions: 1|1 test, 1 assertion" \
    "1 test and 1 assertion with BOLT and --parallel"

assert \
    "php artisan test" \
    "2 passed" \
    "2 passed without BOLT and without --parallel"

assert \
    "SHARD=1/2 php artisan test" \
    "1 passed" \
    "1 passed with BOLT without --parallel"
