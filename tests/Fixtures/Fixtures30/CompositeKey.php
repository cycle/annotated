<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures30;

use Cycle\Annotated\Annotation\Table\PrimaryKey;

#[PrimaryKey(['a', 'b'])]
class CompositeKey {}
