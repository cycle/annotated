<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Tests\Unit\Attribute\SingleTable\StringEnum;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public const ENUM_VALUE_A = 'a';
    public const ENUM_VALUE_B = 'b';

    #[Column('integer', nullable: true, unsigned: true)]
    private $column1;

    #[Column('smallInt', unsigned: true, zerofill: true)]
    private $column2;

    #[Column('string(32)', size: 128)]
    private $column3;

    #[Column('string(32)', readonlySchema: true)]
    private mixed $column4;

    #[Column(type: 'enum(a,b)')]
    private string $column5;

    #[Column(
        type: 'enum',
        default: self::ENUM_VALUE_A,
        values: [self::ENUM_VALUE_A, self::ENUM_VALUE_B],
    )]
    private string $column6 = self::ENUM_VALUE_A;

    #[Column(
        type: 'enum',
        default: 'a',
        typecast: StringEnum::class,
        values: StringEnum::class,
    )]
    private StringEnum $column7 = StringEnum::A;

    #[Column(
        type: 'enum',
        default: 'a',
        values: [StringEnum::A, StringEnum::B],
    )]
    private string $column8 = 'a';

    #[Column(type: 'string', length: 255, charset: 'ascii', collation: 'ascii_bin')]
    private string $column9;

    public function testOneAttribute(): void
    {
        $column = $this->getColumn('column1');

        $this->assertSame(['unsigned' => true], $column->getAttributes());
    }

    public function testTwoAttributes(): void
    {
        $column = $this->getColumn('column2');

        $this->assertSame(['unsigned' => true, 'zerofill' => true], $column->getAttributes());
    }

    public function testCustomSizeAttribute(): void
    {
        $column = $this->getColumn('column3');

        $this->assertSame(['size' => 128], $column->getAttributes());
    }

    public function testDefaultReadonlySchema(): void
    {
        $column = $this->getColumn('column1');

        $this->assertFalse($column->isReadonlySchema());
    }

    public function testReadonlySchema(): void
    {
        $column = $this->getColumn('column4');

        $this->assertTrue($column->isReadonlySchema());
    }

    public function testEnumTypeString(): void
    {
        $column = $this->getColumn('column5');

        $this->assertSame('enum(a,b)', $column->getType());
    }

    public function testEnumTypeArray(): void
    {
        $column = $this->getColumn('column6');

        $this->assertSame('enum(a,b)', $column->getType());
        $this->assertSame('a', $column->getDefault());
        $this->assertArrayNotHasKey('values', $column->getAttributes());
    }

    public function testEnumTypeBackedEnum(): void
    {
        $column = $this->getColumn('column7');

        $this->assertSame('enum(a,b)', $column->getType());
        $this->assertSame('a', $column->getDefault());
        $this->assertArrayNotHasKey('values', $column->getAttributes());
    }

    public function testEnumTypeArrayBackedEnum(): void
    {
        $column = $this->getColumn('column8');

        $this->assertSame('enum(a,b)', $column->getType());
        $this->assertSame('a', $column->getDefault());
        $this->assertArrayNotHasKey('values', $column->getAttributes());
    }

    public function testCharsetAndCollationAttributes(): void
    {
        $column = $this->getColumn('column9');

        $this->assertSame('string', $column->getType());
        $this->assertSame([
            'length' => 255,
            'charset' => 'ascii',
            'collation' => 'ascii_bin',
        ], $column->getAttributes());
    }

    private function getColumn(string $field): Column
    {
        $ref = new \ReflectionClass(static::class);
        return $ref->getProperty($field)->getAttributes(Column::class)[0]->newInstance();
    }
}
