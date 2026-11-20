<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures30;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table\Index;

#[Column(type: 'string', property: 'fromRepository')]
#[Index(columns: ['repository_column'])]
class SubjectRepository {}
