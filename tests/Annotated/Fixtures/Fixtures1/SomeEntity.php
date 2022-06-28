<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures1;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/** * @Entity(table="SomeEntity") */
#[Entity(table: "SomeEntity")]
class SomeEntity implements LabelledInterface
{
    /** @Column(type="primary") */
    #[Column(type: "primary")]
    public $id;

    /** @Column */
    #[Column]
    public int $idificator;

    /** @Column */
    #[Column]
    public ?string $nullableString;

    /** @Column */
    #[Column]
    public ?string $nullableStringWithDefault = "123";

    /** @Column */
    #[Column]
    public \DateTimeImmutable $dateTime;
}