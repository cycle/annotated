<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\ChildEntity;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\Named;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\Subject;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectMapper;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectRepository;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectScope;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectSource;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Registry;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Test;

#[Test]
final class MergeColumnsTest extends BaseTestCase
{
    public function copiesColumnsDeclaredOnRelatedClasses(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'subject', Subject::class, table: 'subjects');
        $entity->setMapper(SubjectMapper::class);
        $entity->setRepository(SubjectRepository::class);
        $entity->setSource(SubjectSource::class);
        $entity->setScope(SubjectScope::class);

        (new MergeColumns(new AttributeReader()))->run($registry);

        Assert::same($entity->getFields()->getNames(), ['fromMapper', 'fromRepository', 'fromSource', 'fromScope']);
    }

    public function copiesColumnsDeclaredOnEveryParentEntity(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'child', ChildEntity::class, table: 'children');

        (new MergeColumns(new AttributeReader()))->run($registry);

        Assert::array($entity->getFields()->getNames())->sameElementsAs(['fromParent', 'fromGrandParent']);
    }

    public function findsParentsWithTheGivenUtils(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'child', ChildEntity::class, table: 'children');

        (new MergeColumns(new AttributeReader(), new EntityUtils(new AnnotationReader())))->run($registry);

        Assert::same($entity->getFields()->getNames(), []);
    }

    public function keepsAFieldTheEntityAlreadyHas(): void
    {
        $registry = new Registry($this->dbal);
        $entity = $this->entity($registry, 'named', Named::class, table: 'named');
        $entity->getFields()->set('name', (new Field())->setType('string')->setColumn('name_column'));

        (new MergeColumns(new AttributeReader()))->run($registry);

        Assert::same($entity->getFields()->get('name')->getColumn(), 'name_column');
    }

    public function skipsEntitiesWithoutTable(): void
    {
        $registry = new Registry($this->dbal);
        $withoutTable = $this->entity($registry, 'withoutTable', Named::class, table: null);
        $withTable = $this->entity($registry, 'withTable', Named::class, table: 'named');

        (new MergeColumns(new AttributeReader()))->run($registry);

        Assert::same($withoutTable->getFields()->getNames(), []);
        Assert::same($withTable->getFields()->getNames(), ['name']);
    }

    /**
     * @param class-string $class
     */
    private function entity(Registry $registry, string $role, string $class, ?string $table): Entity
    {
        $entity = (new Entity())->setRole($role)->setClass($class);
        $registry->register($entity);
        if ($table !== null) {
            $registry->linkTable($entity, null, $table);
        }

        return $entity;
    }
}
