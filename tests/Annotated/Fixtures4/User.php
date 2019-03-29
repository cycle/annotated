<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Fixtures4;

/**
 * @entity
 */
class User
{
    /**
     * @column()
     * @var int
     */
    protected $id;
}