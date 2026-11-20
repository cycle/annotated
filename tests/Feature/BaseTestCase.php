<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature;

use Cycle\Testing\ORM\OrmTestCase;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Composite\SelectiveReader;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Lifecycle\BeforeTest;

abstract class BaseTestCase extends OrmTestCase
{
    protected ClassesInterface $locator;

    public static function singularReadersProvider(): \Traversable
    {
        yield 'Annotation reader' => [new AnnotationReader()];
        yield 'Attribute reader' => [new AttributeReader()];
    }

    public static function allReadersProvider(): \Traversable
    {
        yield from static::singularReadersProvider();
        yield 'Selective reader' => [new SelectiveReader([new AttributeReader(), new AnnotationReader()])];
    }

    #[BeforeTest]
    public function initLocator(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../Fixtures/Fixtures1'],
            'exclude' => [],
        ]));

        $this->locator = $tokenizer->classLocator();
    }
}
