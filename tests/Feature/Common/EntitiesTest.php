<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Animal;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Author;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Book;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Dog;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\DogOwner;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Review;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\UnicornOwner;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\Registry;
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Expect;
use Testo\Test;

#[Test]
final class EntitiesTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    public function wrapsThroughKeysIntoArrays(ReaderInterface $reader): void
    {
        $registry = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($registry);

        $options = $registry->getEntity('withTable')->getRelations()->get('tags')->getOptions();
        Assert::same($options->get('throughInnerKey'), ['withTable_id']);
        Assert::same($options->get('throughOuterKey'), ['tag_id']);
    }

    public function resolvesTheTargetAndColumnsOfAForeignKey(): void
    {
        $registry = $this->register(Author::class, Book::class);

        $keys = \iterator_to_array($registry->getEntity('book')->getForeignKeys(), false);
        Assert::count($keys, 1);
        Assert::same($keys[0]->getTarget(), 'author');
        Assert::same($keys[0]->getInnerColumns(), ['author_id', 'author_kind']);
        Assert::same($keys[0]->getOuterColumns(), ['id', 'kind']);
    }

    public function rejectsAForeignKeyOnAnUnknownColumn(): never
    {
        Expect::exception(AnnotationException::class)->withMessage('Unable to resolve column name.');

        $this->register(Author::class, Review::class);
    }

    public function pointsARelationToAnInheritanceChildAtItsParent(): void
    {
        $registry = $this->register(Animal::class, Dog::class, DogOwner::class);

        Assert::same($registry->getEntity('dogOwner')->getRelations()->get('dog')->getTarget(), 'animal');
    }

    public function rejectsARelationToAnUnknownTarget(): never
    {
        Expect::exception(RelationException::class)->withMessageContaining('`unicornOwner`.`unicorn`');

        $this->register(Animal::class, Dog::class, UnicornOwner::class);
    }

    /**
     * @param class-string ...$classes
     */
    private function register(string ...$classes): Registry
    {
        $reader = new AttributeReader();
        $registry = new Registry($this->dbal);

        (new Entities(new TokenizerEntityLocator(new ClassList(...$classes), $reader), $reader))->run($registry);

        return $registry;
    }
}
