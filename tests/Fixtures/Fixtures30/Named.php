<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures30;

use Cycle\Annotated\Annotation\Column;

#[Column(type: 'string', property: 'name', name: 'from_attribute')]
class Named {}
