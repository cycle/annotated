<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures6;

use Stringable;

final class City implements Stringable
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
