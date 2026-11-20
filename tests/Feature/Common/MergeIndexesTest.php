<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\ChildEntity;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\CompositeKey;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\Subject;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectMapper;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectRepository;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectScope;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectSource;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Test;

#[Test]
final class MergeIndexesTest extends BaseTestCase
{
    public function rendersIndexesDeclaredOnRelatedClasses(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'subject', Subject::class);
        $entity->setMapper(SubjectMapper::class);
        $entity->setRepository(SubjectRepository::class);
        $entity->setSource(SubjectSource::class);
        $entity->setScope(SubjectScope::class);
        foreach (['mapper', 'repository', 'source', 'scope'] as $name) {
            $registry->getTableSchema($entity)->string($name . '_column');
        }

        (new MergeIndexes(new AttributeReader()))->run($registry);

        $table = $registry->getTableSchema($entity);
        Assert::true($table->hasIndex(['mapper_column']));
        Assert::true($table->hasIndex(['repository_column']));
        Assert::true($table->hasIndex(['source_column']));
        Assert::true($table->hasIndex(['scope_column']));
    }

    public function rendersIndexesOfAChildIntoTheParentTable(): void
    {
        $registry = new Registry($this->dbal);
        $parent = $this->entity($registry, 'subject', Subject::class);
        $registry->registerChildWithoutMerge($parent, (new Entity())->setRole('child')->setClass(ChildEntity::class));
        $registry->getTableSchema($parent)->string('child_column');

        (new MergeIndexes(new AttributeReader()))->run($registry);

        Assert::true($registry->getTableSchema($parent)->hasIndex(['child_column']));
    }

    public function setsThePrimaryColumnsOfTheEntity(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'compositeKey', CompositeKey::class);
        $entity->getFields()->set('a', (new Field())->setType('integer')->setColumn('a'));
        $entity->getFields()->set('b', (new Field())->setType('integer')->setColumn('b'));

        (new MergeIndexes(new AttributeReader()))->run($registry);

        Assert::same($entity->getPrimaryFields()->getNames(), ['a', 'b']);
        Assert::same($registry->getTableSchema($entity)->getPrimaryKeys(), ['a', 'b']);
    }

    /**
     * @param class-string $class
     */
    private function entity(Registry $registry, string $role, string $class): Entity
    {
        $entity = (new Entity())->setRole($role)->setClass($class);
        $registry->register($entity);
        $registry->linkTable($entity, null, $role);

        return $entity;
    }
}
