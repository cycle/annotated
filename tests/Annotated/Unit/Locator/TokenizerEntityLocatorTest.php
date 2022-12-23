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
use PHPUnit\Framework\TestCase;
use Spiral\Tokenizer\ClassesInterface;

final class TokenizerEntityLocatorTest extends TestCase
{
    /**
     * @dataProvider classesDataProvider
     */
    public function testGetEntities(array $expected, ClassesInterface $classes): void
    {
        $locator = new TokenizerEntityLocator($classes);

        $this->assertEquals($expected, $locator->getEntities());
    }

    public function classesDataProvider(): \Traversable
    {
        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([]);
        yield [[], $mock];

        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([
            AnotherClass::class => new \ReflectionClass(AnotherClass::class),
        ]);
        yield [[], $mock];

        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([
            Tag::class => new \ReflectionClass(Tag::class),
            WithTable::class => new \ReflectionClass(WithTable::class),
        ]);
        yield [
            [
                new Entity(
                    new Attribute(typecast: [Typecaster::class, UuidTypecaster::class, 'foo']),
                    new \ReflectionClass(Tag::class)
                ),
                new Entity(
                    new Attribute(),
                    new \ReflectionClass(WithTable::class)
                ),
            ],
            $mock,
        ];
    }
}
