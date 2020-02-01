<?php

declare(strict_types=1);

namespace Spiral\Tests\fixtures;

use PHPUnit\Framework\TestCase;

class NotFormattedClass
{
    private $property;

    public function setUp(): void
    {
        $this->property = 'some string';
    }

    public function testWrongFormattedClass(): void
    {
        $this->assertEquals();
    }
}
