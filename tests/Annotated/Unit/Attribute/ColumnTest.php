<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Tests\Unit\Attribute\SingleTable\StringEnum;
use PHPUnit\Framework\TestCase;

class ColumnTest
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

    #[Column(
        type: 'enum',
        default: StringEnum::A,
        values: StringEnum::class,
    )]
    private StringEnum $column10 = StringEnum::A;

    #[\Testo\Test]
    public function testOneAttribute(): void
    {
        $column = $this->getColumn('column1');

        \Testo\Assert::same($column->getAttributes(), ['unsigned' => true]);
    }

    #[\Testo\Test]
    public function testTwoAttributes(): void
    {
        $column = $this->getColumn('column2');

        \Testo\Assert::same($column->getAttributes(), ['unsigned' => true, 'zerofill' => true]);
    }

    #[\Testo\Test]
    public function testCustomSizeAttribute(): void
    {
        $column = $this->getColumn('column3');

        \Testo\Assert::same($column->getAttributes(), ['size' => 128]);
    }

    #[\Testo\Test]
    public function testDefaultReadonlySchema(): void
    {
        $column = $this->getColumn('column1');

        \Testo\Assert::false($column->isReadonlySchema());
    }

    #[\Testo\Test]
    public function testReadonlySchema(): void
    {
        $column = $this->getColumn('column4');

        \Testo\Assert::true($column->isReadonlySchema());
    }

    #[\Testo\Test]
    public function testEnumTypeString(): void
    {
        $column = $this->getColumn('column5');

        \Testo\Assert::same($column->getType(), 'enum(a,b)');
    }

    #[\Testo\Test]
    public function testEnumTypeArray(): void
    {
        $column = $this->getColumn('column6');

        \Testo\Assert::same($column->getType(), 'enum(a,b)');
        \Testo\Assert::same($column->getDefault(), 'a');
        \Testo\Assert::array($column->getAttributes())->doesNotHaveKeys('values');
    }

    #[\Testo\Test]
    public function testEnumTypeBackedEnum(): void
    {
        $column = $this->getColumn('column7');

        \Testo\Assert::same($column->getType(), 'enum(a,b)');
        \Testo\Assert::same($column->getDefault(), 'a');
        \Testo\Assert::array($column->getAttributes())->doesNotHaveKeys('values');
    }

    #[\Testo\Test]
    public function testEnumTypeArrayBackedEnum(): void
    {
        $column = $this->getColumn('column8');

        \Testo\Assert::same($column->getType(), 'enum(a,b)');
        \Testo\Assert::same($column->getDefault(), 'a');
        \Testo\Assert::array($column->getAttributes())->doesNotHaveKeys('values');
    }

    #[\Testo\Test]
    public function testEnumTypeBackedEnumAsDefault(): void
    {
        $column = $this->getColumn('column10');

        \Testo\Assert::same($column->getType(), 'enum(a,b)');
        \Testo\Assert::same($column->getDefault(), 'a');
        \Testo\Assert::array($column->getAttributes())->doesNotHaveKeys('values');
    }

    #[\Testo\Test]
    public function testCharsetAndCollationAttributes(): void
    {
        $column = $this->getColumn('column9');

        \Testo\Assert::same($column->getType(), 'string');
        \Testo\Assert::same($column->getAttributes(), [
            'length' => 255,
            'charset' => 'ascii',
            'collation' => 'ascii_bin',
        ]);
    }

    private function getColumn(string $field): Column
    {
        $ref = new \ReflectionClass(static::class);
        return $ref->getProperty($field)->getAttributes(Column::class)[0]->newInstance();
    }
}
