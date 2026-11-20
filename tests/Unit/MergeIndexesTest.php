<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\Subject;
use Cycle\Annotated\Tests\Fixtures\Fixtures30\SubjectRepository;
use Cycle\Annotated\Tests\Fixtures\Locator\FailingClassReader;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Registry;
use Cycle\Testing\Database\Memory;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Expect;
use Testo\Test;

#[Test]
final class MergeIndexesTest
{
    public function skipsARelatedClassThatDoesNotExist(): void
    {
        $registry = new Registry(Memory::manager());
        $entity = $this->subject($registry);
        $entity->setMapper('Cycle\Annotated\Tests\Fixtures\Fixtures30\MissingMapper');
        $entity->setRepository(SubjectRepository::class);
        $registry->getTableSchema($entity)->string('repository_column');

        (new MergeIndexes(new AttributeReader()))->run($registry);

        $indexes = $registry->getTableSchema($entity)->getIndexes();
        Assert::count($indexes, 1);
        Assert::same(\reset($indexes)->getColumns(), ['repository_column']);
    }

    public function wrapsAnExceptionOfTheReader(): never
    {
        $registry = new Registry(Memory::manager());
        $this->subject($registry);
        $previous = new \RuntimeException('broken', 42);

        Expect::exception(AnnotationException::class)->withMessage('broken')->withCode(42)->withPrevious($previous);

        (new MergeIndexes(new FailingClassReader($previous)))->run($registry);
    }

    private function subject(Registry $registry): Entity
    {
        $entity = (new Entity())->setRole('subject')->setClass(Subject::class);
        $registry->register($entity);
        $registry->linkTable($entity, null, 'subjects');

        return $entity;
    }
}
