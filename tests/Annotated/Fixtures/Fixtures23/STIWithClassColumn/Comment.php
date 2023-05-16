<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures23\STIWithClassColumn;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 * @Column(type="primary", property="id")
 * @Column(type="string", property="body", nullable=true)
 */
#[Entity]
#[Column(type: 'primary', property: 'id')]
#[Column(type: 'string', property: 'body', nullable: true)]
final class Comment implements EventEmitterInterface
{
    public int $id;
    public ?string $body = null;
}
