<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Traits;

use Cycle\Annotated\Annotation\Relation\Inverse;

trait InverseTrait
{
    /** @var Inverse */
    protected $inverse;

    /**
     * @return Inverse|null
     */
    public function getInverse(): ?Inverse
    {
        return $this->inverse;
    }
}
