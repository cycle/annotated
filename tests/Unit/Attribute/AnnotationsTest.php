<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\GeneratedValue;
use Cycle\Annotated\Annotation\Inheritance\JoinedTable;
use Cycle\Annotated\Annotation\Inheritance\SingleTable;
use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Table\PrimaryKey;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema\GeneratedField;
use Testo\Assert;
use Testo\Data\DataSet;
use Testo\Test;

#[Test]
final class AnnotationsTest
{
    public function entityKeepsSeveralTypecastHandlers(): void
    {
        $entity = new Entity(typecast: ['Foo', 'Bar']);

        Assert::same($entity->getTypecast(), ['Foo', 'Bar']);
    }

    public function entityUnwrapsASingleTypecastHandler(): void
    {
        $entity = new Entity(typecast: ['Foo']);

        Assert::same($entity->getTypecast(), 'Foo');
    }

    #[DataSet([false, false, false, null], 'nothing generated')]
    #[DataSet([true, false, false, GeneratedField::BEFORE_INSERT], 'before insert')]
    #[DataSet([false, true, false, GeneratedField::ON_INSERT], 'on insert')]
    #[DataSet([false, false, true, GeneratedField::BEFORE_UPDATE], 'before update')]
    #[DataSet([true, true, true, GeneratedField::BEFORE_INSERT | GeneratedField::ON_INSERT | GeneratedField::BEFORE_UPDATE], 'everything')]
    public function generatedValueCombinesFlags(bool $beforeInsert, bool $onInsert, bool $beforeUpdate, ?int $flags): void
    {
        $generated = new GeneratedValue($beforeInsert, $onInsert, $beforeUpdate);

        Assert::same($generated->getFlags(), $flags);
    }

    public function joinedTableHasItsType(): void
    {
        Assert::same((new JoinedTable())->getType(), 'joined');
    }

    public function singleTableHasItsType(): void
    {
        Assert::same((new SingleTable())->getType(), 'single');
    }

    #[DataSet(['eager', Relation::LOAD_EAGER])]
    #[DataSet([Relation::LOAD_EAGER, Relation::LOAD_EAGER])]
    #[DataSet(['promise', Relation::LOAD_PROMISE])]
    #[DataSet(['lazy', Relation::LOAD_PROMISE])]
    #[DataSet([Relation::LOAD_PROMISE, Relation::LOAD_PROMISE])]
    #[DataSet([null, null])]
    #[DataSet(['unknown', null])]
    public function inverseResolvesTheLoadMethod(string|int|null $load, ?int $method): void
    {
        $inverse = new Inverse(as: 'posts', type: 'hasMany', load: $load);

        Assert::same($inverse->getLoadMethod(), $method);
    }

    public function primaryKeyIsAUniqueIndex(): void
    {
        $key = new PrimaryKey(['id', 'type']);

        Assert::true($key->isUnique());
        Assert::same($key->getColumns(), ['id', 'type']);
    }
}
