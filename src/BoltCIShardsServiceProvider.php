<?php

namespace BoltCI\Shards;

use Illuminate\Support\ServiceProvider;

class BoltCIShardsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            PatchPHPUnitCommand::class,
            PatchParatestCommand::class,
        ]);
    }
}
