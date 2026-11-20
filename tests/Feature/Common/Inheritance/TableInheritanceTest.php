<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common\Inheritance;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Employee;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Executive;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Person;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Tool;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Car;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Vehicle;
use Cycle\Schema\Registry;
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Test;

#[Test]
final class TableInheritanceTest extends BaseTestCase
{
    public function turnsTheInheritedPrimaryKeyOfAJoinedTableIntoAPlainColumn(): void
    {
        $registry = $this->register(Person::class, Employee::class, Executive::class, Tool::class);

        $id = $registry->getEntity('executive')->getFields()->get('foo_id');
        Assert::same($id->getType(), 'integer');
        Assert::true($id->isPrimary());
    }

    public function placesASingleTableChildIntoTheDatabaseOfItsParent(): void
    {
        $registry = $this->register(Vehicle::class, Car::class);

        Assert::same($registry->getDatabase($registry->getEntity('car')), 'secondary');
    }

    /**
     * @param class-string ...$classes
     */
    private function register(string ...$classes): Registry
    {
        $reader = new AttributeReader();
        $registry = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator(new ClassList(...$classes), $reader), $reader))->run($registry);
        (new TableInheritance($reader))->run($registry);

        return $registry;
    }
}
