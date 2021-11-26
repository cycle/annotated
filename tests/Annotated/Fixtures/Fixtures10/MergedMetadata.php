<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures10;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * @Entity(role="MergedMeta")
 * @Table(
 *      columns = {
 *          "name": @Column(type="string"),
 *      }
 * )
 */
#[Index(columns: ['name', 'id DESC'])]
class MergedMetadata
{
    #[Column(type: 'primary')]
    protected $id;
}
