<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures28;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * Composite PK where one part is application-supplied (uuid) and the other is
 * database-generated (bigserial). Mirrors cycle/orm Case8.
 *
 * @Entity(role="event", table="event")
 */
#[Entity(role: 'event', table: 'event')]
class Event
{
    /**
     * Application-supplied UUID7 (partition key).
     *
     * Note: a primary column is auto-flagged as GeneratedField::ON_INSERT (see
     * Configurator::isOnInsertGeneratedField()), so this app-supplied value is
     * also echoed back via the RETURNING clause. Harmless for a plain uuid
     * string — the value round-trips unchanged. To opt out, add an empty
     * #[GeneratedValue].
     *
     * @Column(type="uuid", primary=true)
     */
    #[Column(type: 'uuid', primary: true)]
    public string $parentId;

    /**
     * Database-generated bigserial, returned on INSERT.
     *
     * No #[GeneratedValue] needed: a primary column is auto-flagged as
     * GeneratedField::ON_INSERT (see Configurator::isOnInsertGeneratedField()).
     *
     * @Column(type="bigPrimary")
     */
    #[Column(type: 'bigPrimary')]
    public ?int $id = null;

    /**
     * @Column(type="string", nullable=true)
     */
    #[Column(type: 'string', nullable: true)]
    public ?string $payload = null;
}
