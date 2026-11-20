<?php

declare(strict_types=1);

use Cycle\Testing\Config;
use Cycle\Testing\Database\Layout;
use Cycle\Testing\ORM\OrmPlugin;

return Config::forPackage('cycle/annotated', root: __DIR__)
    ->unit('tests/Annotated/Unit')
    ->acceptance(
        'tests/Annotated/Acceptance',
        layout: Layout::prefixed(['secondary' => 'secondary_']),
        plugins: [new OrmPlugin()],
    )
    ->build();
