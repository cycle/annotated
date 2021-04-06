<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\ReaderFactory;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use PHPUnit\Framework\TestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\Composite\MergeReader;
use Spiral\Attributes\Composite\SelectiveReader;

class ReaderFactoryTest extends TestCase
{
    public function testCreateFromNull(): void
    {
        $reader = ReaderFactory::create(null);

        $this->assertInstanceOf(SelectiveReader::class, $reader);
    }

    public function testCreateFromDoctrineReader(): void
    {
        $reader = ReaderFactory::create(new DoctrineAnnotationReader());

        $this->assertInstanceOf(AnnotationReader::class, $reader);
    }

    public function testCreateFromCustomReader(): void
    {
        $mergeReader = new MergeReader([]);
        $reader = ReaderFactory::create($mergeReader);

        $this->assertSame($mergeReader, $reader);
    }

    public function testCreateFromBadObjectValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument $reader must be an instance of Spiral\Attributes\ReaderInterface '
            . 'or Doctrine\Common\Annotations\Reader, but instance of DateTimeImmutable passed.'
        );

        ReaderFactory::create(new \DateTimeImmutable());
    }

    public function testCreateFromBadValueAnonymousClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument $reader must be an instance of Spiral\Attributes\ReaderInterface '
            . 'or Doctrine\Common\Annotations\Reader, but instance of class@anonymous passed.'
        );

        ReaderFactory::create(new class() {});
    }
}
