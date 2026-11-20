<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\ReaderFactory;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;

class ReaderFactoryTest
{
    #[\Testo\Test]
    public function testCreateFromNull(): void
    {
        $reader = ReaderFactory::create(null);

        \Testo\Assert::instanceOf($reader, AttributeReader::class);
    }

    #[\Testo\Test]
    public function testCreateFromDoctrineReader(): void
    {
        $reader = ReaderFactory::create(new DoctrineAnnotationReader());

        \Testo\Assert::instanceOf($reader, AnnotationReader::class);
    }

    #[\Testo\Test]
    public function testCreateFromCustomReader(): void
    {
        $mergeReader = new MergeReader([]);
        $reader = ReaderFactory::create($mergeReader);

        \Testo\Assert::same($reader, $mergeReader);
    }
}
