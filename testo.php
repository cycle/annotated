<?php

declare(strict_types=1);

use Cycle\Testing\Config;
use Cycle\Testing\Database\Layout;
use Cycle\Testing\ORM\OrmPlugin;

return Config::forPackage('cycle/annotated', root: __DIR__)
    ->unit('tests/Unit')
    ->feature(
        'tests/Feature',
        layout: Layout::prefixed(['secondary' => 'secondary_']),
        plugins: [new OrmPlugin()],
    )
    ->regression('tests/Regression', plugins: [new OrmPlugin()])
    ->build();
