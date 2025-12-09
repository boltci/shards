<?php

namespace BoltCI\Shards;

use BoltCI\Shards\Concerns\CanPatchFiles;
use Composer\InstalledVersions;
use Illuminate\Console\Command;

class PatchParatestCommand extends Command
{
    use CanPatchFiles;

    protected $signature = 'shards:patch-paratest';

    public function handle(): int
    {
        $packageName = 'brianium/paratest';

        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled($packageName)) {
            $version = InstalledVersions::getPrettyVersion($packageName);
        }

        if (version_compare($version, 'v7.2.0', '<')) {
            $this->warn('Skipped patching as we currently only support v7.2.0 and above.');

            return Command::SUCCESS;
        }

        if (version_compare($version, 'v7.4.3', '>=')) {
            $this->info('Skipped patching as the known issue has been fixed since ParaTest v7.4.3.');

            return Command::SUCCESS;
        }

        $this->patch(
            base_path('vendor').'/brianium/paratest/src/WrapperRunner/SuiteLoader.php',
            '$tests[] = "$file\0$name";',
            '$tests[] = (new \BoltCI\Shards\TestNameFilterTransformer())->transform("$file\0$name");',
        );

        $this->info('Paratest patched successfully.');

        return Command::SUCCESS;
    }
}
