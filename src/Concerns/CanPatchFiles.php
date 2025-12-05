<?php

namespace BoltCI\Shards\Concerns;

trait CanPatchFiles
{
    protected function patch($path, $find, $replace): void
    {
        $content = file_get_contents($path);

        $content = str_replace($find, $replace, $content);

        file_put_contents($path, $content);
    }
}
