<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\ReaderFactory;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\MergeReader;
use Testo\Assert;
use Testo\Test;

#[Test]
final class ReaderFactoryTest
{
    public function testCreateFromNull(): void
    {
        $reader = ReaderFactory::create(null);

        Assert::instanceOf($reader, AttributeReader::class);
    }

    public function testCreateFromDoctrineReader(): void
    {
        $reader = ReaderFactory::create(new DoctrineAnnotationReader());

        Assert::instanceOf($reader, AnnotationReader::class);
    }

    public function testCreateFromCustomReader(): void
    {
        $mergeReader = new MergeReader([]);
        $reader = ReaderFactory::create($mergeReader);

        Assert::same($reader, $mergeReader);
    }
}
