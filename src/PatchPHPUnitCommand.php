<?php

namespace BoltCI\Shards;

use BoltCI\Shards\Concerns\CanPatchFiles;
use Illuminate\Console\Command;

class PatchPHPUnitCommand extends Command
{
    use CanPatchFiles;

    protected $signature = 'boltci:patch-phpunit';

    public function handle(): int
    {
        $this->patch(
            base_path('vendor').'/phpunit/phpunit/src/TextUI/Configuration/TestSuiteBuilder.php',
            'return $testSuite;',
            'return (new \BoltCI\Shards\Shards($testSuite))->get(getenv("BOLTCI_SHARD") ?: "1/1", getenv("BOLTCI_SEED") ?: null );'
        );

        $this->info('PHPUnit patched successfully.');

        return Command::SUCCESS;
    }
}
