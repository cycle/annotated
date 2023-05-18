<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Traits;

use Cycle\Annotated\Annotation\Relation\Inverse;

trait InverseTrait
{
    protected ?Inverse $inverse = null;

    public function getInverse(): ?Inverse
    {
        return $this->inverse;
    }
}
