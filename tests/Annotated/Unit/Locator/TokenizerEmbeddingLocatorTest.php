<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Locator;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Locator\Embedding;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Tests\Fixtures\Fixtures1\Child;
use Cycle\Annotated\Tests\Fixtures\Fixtures7\Address;
use PHPUnit\Framework\TestCase;
use Spiral\Tokenizer\ClassesInterface;

final class TokenizerEmbeddingLocatorTest extends TestCase
{
    /**
     * @dataProvider classesDataProvider
     */
    public function testGetEmbeddings(array $expected, ClassesInterface $classes): void
    {
        $locator = new TokenizerEmbeddingLocator($classes);

        $this->assertEquals($expected, $locator->getEmbeddings());
    }

    public function classesDataProvider(): \Traversable
    {
        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([]);
        yield [[], $mock];

        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([Child::class => new \ReflectionClass(Child::class)]);
        yield [[], $mock];

        $mock = $this->createMock(ClassesInterface::class);
        $mock->method('getClasses')->willReturn([
            Address::class => new \ReflectionClass(Address::class),
            Child::class => new \ReflectionClass(Child::class),
        ]);
        yield [
            [
                new Embedding(
                    new Embeddable(role: 'address', columnPrefix: 'address_'),
                    new \ReflectionClass(Address::class)
                ),
            ],
            $mock
        ];
    }
}
