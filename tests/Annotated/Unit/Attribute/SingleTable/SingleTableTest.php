<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute\SingleTable;

use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SingleTableTest extends TestCase
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

    #[DataProvider('dataBase')]
    public function testBase1(?string $expected, mixed $value): void
    {
        $attribute = new SingleTable($value);

        $this->assertSame($expected, $attribute->getValue());
    }
}
