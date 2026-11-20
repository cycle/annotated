<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Locator;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Locator\Embedding;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Child;
use Cycle\Annotated\Tests\Fixtures\Fixtures7\Address;
use Cycle\Testing\ORM\Schema\ClassList;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

#[Test]
final class TokenizerEmbeddingLocatorTest
{
    public static function classesDataProvider(): \Traversable
    {
        yield [[], []];
        yield [[], [Child::class => new \ReflectionClass(Child::class)]];
        yield [
            [
                new Embedding(
                    new Embeddable(role: 'address', columnPrefix: 'address_'),
                    new \ReflectionClass(Address::class),
                ),
            ],
            [
                Address::class => new \ReflectionClass(Address::class),
                Child::class => new \ReflectionClass(Child::class),
            ],
        ];
    }

    #[DataProvider('classesDataProvider')]
    public function testGetEmbeddings(array $expected, array $classes): void
    {
        $source = new ClassList(...\array_keys($classes));

        $locator = new TokenizerEmbeddingLocator($source);

        Assert::equals($locator->getEmbeddings(), $expected);
    }
}
