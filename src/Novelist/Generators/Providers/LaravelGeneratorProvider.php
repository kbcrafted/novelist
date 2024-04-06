<?php

namespace Novelist\Generators\Providers;

class LaravelGeneratorProvider
{
    protected array $phpGenerators = [
        'model' => 'ModelGenerator',
        'policy' => 'PolicyGenerator'
    ];
}