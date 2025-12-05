<?php

namespace BoltCI\Shards;

class TestNameFilterTransformer
{
    public function transform(string $name): string
    {
        if (str_contains($name, '#') || str_contains($name, '@') || str_contains($name, '$')) {
            return $name;
        }

        return "$name$";
    }
}
