<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures25\PostgreSQL;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(role="withGeneratedSerial", table="with_generated_serial")
 */
#[Entity(role: 'withGeneratedSerial', table: 'with_generated_serial')]
class WithGeneratedSerial
{
    /**
     * @Column(type="primary")
     */
    #[Column(type: 'primary')]
    public int $id;

    /**
     * @Column(type="smallserial", name="small_serial")
     */
    #[Column(type: 'smallserial', name: 'small_serial')]
    public int $smallSerial;

    /**
     * @Column(type="serial")
     */
    #[Column(type: 'serial')]
    public int $serial;

    /**
     * @Column(type="bigserial", name="big_serial")
     */
    #[Column(type: 'bigserial', name: 'big_serial')]
    public int $bigSerial;
}
