<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Locator;

use Cycle\Annotated\Annotation\Entity as Attribute;
use Cycle\Annotated\Locator\Entity;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\AnotherClass;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Tag;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Typecast\Typecaster;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Typecast\UuidTypecaster;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\WithTable;
use Cycle\Testing\ORM\Schema\ClassList;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

#[Test]
final class TokenizerEntityLocatorTest
{
    public static function classesDataProvider(): \Traversable
    {
        yield [[], []];
        yield [[], [AnotherClass::class => new \ReflectionClass(AnotherClass::class)]];
        yield [
            [
                new Entity(
                    new Attribute(typecast: [Typecaster::class, UuidTypecaster::class, 'foo']),
                    new \ReflectionClass(Tag::class),
                ),
                new Entity(
                    new Attribute(),
                    new \ReflectionClass(WithTable::class),
                ),
            ],
            [
                Tag::class => new \ReflectionClass(Tag::class),
                WithTable::class => new \ReflectionClass(WithTable::class),
            ],
        ];
    }

    #[DataProvider('classesDataProvider')]
    public function testGetEntities(array $expected, array $classes): void
    {
        $source = new ClassList(...\array_keys($classes));

        $locator = new TokenizerEntityLocator($source);

        Assert::equals($locator->getEntities(), $expected);
    }
}
