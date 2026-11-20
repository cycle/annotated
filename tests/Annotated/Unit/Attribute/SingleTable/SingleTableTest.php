<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute\SingleTable;

use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SingleTableTest
{
    public static function dataBase(): iterable
    {
        yield [null, null];
        yield ['test', 'test'];
        yield ['42', 42];
        yield ['36.6', 36.6];
        yield ['test', new StringableObject('test')];
        yield ['1', IntegerEnum::ONE];
        yield ['a', StringEnum::A];
    }

    #[\Testo\Data\DataProvider('dataBase')]
    #[\Testo\Test]
    public function testBase1(?string $expected, mixed $value): void
    {
        $attribute = new SingleTable($value);

        \Testo\Assert::same($attribute->getValue(), $expected);
    }
}
