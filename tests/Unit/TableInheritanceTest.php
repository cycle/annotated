<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Locator\FailingClassReader;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Animal;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Bird;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Cat;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Dog;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Fish;
use Cycle\Annotated\Tests\Fixtures\TableInheritance\Puppy;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Exception\TableInheritance\WrongParentKeyColumnException;
use Cycle\Schema\Registry;
use Cycle\Testing\Database\Memory;
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Expect;
use Testo\Test;

#[Test]
final class TableInheritanceTest
{
    public function turnsTheInheritedBigPrimaryKeyOfAJoinedTableIntoABigInteger(): void
    {
        $registry = $this->entities(Animal::class, Dog::class);

        (new TableInheritance(new AttributeReader()))->run($registry);

        $id = $registry->getEntity('dog')->getFields()->get('id');
        Assert::same($id->getType(), 'bigInteger');
        Assert::true($id->isPrimary());
        Assert::false($registry->getEntity('dog')->getFields()->has('name'));
    }

    public function rejectsAnOuterKeyTheParentDoesNotHave(): never
    {
        $registry = $this->entities(Animal::class, Cat::class);

        Expect::exception(WrongParentKeyColumnException::class);

        (new TableInheritance(new AttributeReader()))->run($registry);
    }

    public function leavesAnUnknownInheritanceTypeToOtherGenerators(): void
    {
        $registry = $this->entities(Animal::class, Bird::class);

        (new TableInheritance(new AttributeReader()))->run($registry);

        $bird = $registry->getEntity('bird');
        Assert::null($bird->getInheritance());
        Assert::null($registry->getEntity('animal')->getInheritance());
        Assert::same($registry->getTable($bird), 'birds');
    }

    public function dropsFieldsOfASingleTableThatNoEntityOfTheHierarchyOwns(): void
    {
        $registry = $this->entities(Animal::class, Fish::class);
        $animal = $registry->getEntity('animal');
        $animal->getFields()->set('foreign', (new Field())->setType('string')->setColumn('foreign'));

        (new TableInheritance(new AttributeReader()))->run($registry);

        Assert::false($animal->getFields()->has('foreign'));
        Assert::true($animal->getFields()->has('name'));
    }

    public function failsWhenTheParentEntityIsNotRegistered(): never
    {
        $registry = new Registry(Memory::manager());
        $animal = (new Entity())->setRole('animal')->setClass(Animal::class);
        $registry->register($animal);
        $registry->registerChildWithoutMerge($animal, (new Entity())->setRole('puppy')->setClass(Puppy::class));

        Expect::exception(AnnotationException::class)
            ->withMessage(\sprintf('The entity could not be found for the role `%s`.', Dog::class));

        (new TableInheritance(new AttributeReader()))->run($registry);
    }

    public function wrapsAnExceptionOfTheReader(): never
    {
        $registry = $this->entities(Animal::class, Dog::class);
        $previous = new \RuntimeException('broken', 42);

        Expect::exception(AnnotationException::class)->withMessage('broken')->withCode(42)->withPrevious($previous);

        (new TableInheritance(new FailingClassReader($previous)))->run($registry);
    }

    /**
     * @param class-string ...$classes
     */
    private function entities(string ...$classes): Registry
    {
        $reader = new AttributeReader();
        $registry = new Registry(Memory::manager());

        return (new Entities(new TokenizerEntityLocator(new ClassList(...$classes), $reader), $reader))->run($registry);
    }
}
