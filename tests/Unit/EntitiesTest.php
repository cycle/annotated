<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Fixtures\ForeignKey\Customer;
use Cycle\Annotated\Tests\Fixtures\ForeignKey\Order;
use Cycle\Schema\Registry;
use Cycle\Testing\Database\Memory;
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Test;

#[Test]
final class EntitiesTest
{
    public function pointsAForeignKeyWithoutOuterKeyAtThePrimaryKeyOfItsTarget(): void
    {
        $reader = new AttributeReader();
        $entities = new Entities(new TokenizerEntityLocator(new ClassList(Order::class, Customer::class), $reader), $reader);

        $registry = $entities->run(new Registry(Memory::manager()));

        $keys = \iterator_to_array($registry->getEntity('order')->getForeignKeys(), false);
        Assert::count($keys, 1);
        Assert::same($keys[0]->getTarget(), 'customer');
        Assert::same($keys[0]->getInnerColumns(), ['customer_ref']);
        Assert::same($keys[0]->getOuterColumns(), ['customer_id']);
    }
}
