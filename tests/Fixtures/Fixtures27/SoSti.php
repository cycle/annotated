<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures27;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;

#[Entity]
#[SingleTable]
class SoSti extends SoParent {}
