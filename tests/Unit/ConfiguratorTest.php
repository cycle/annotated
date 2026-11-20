<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Configurator;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Exception\AnnotationWrongTypeArgumentException;
use Cycle\Annotated\Tests\Fixtures\Configurator\Address;
use Cycle\Annotated\Tests\Fixtures\Configurator\AddressMapper;
use Cycle\Annotated\Tests\Fixtures\Configurator\Author;
use Cycle\Annotated\Tests\Fixtures\Configurator\Post;
use Cycle\Annotated\Tests\Fixtures\Configurator\Sub\Comments;
use Cycle\Annotated\Tests\Fixtures\Configurator\Sub\Typecaster as SubTypecaster;
use Cycle\Annotated\Tests\Fixtures\Configurator\Typecaster;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Table\Column as ColumnOption;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\Reader;
use Testo\Assert;
use Testo\Data\DataSet;
use Testo\Expect;
use Testo\Test;

#[Test]
final class ConfiguratorTest
{
    public function resolvesEveryEntityTypecastHandler(): void
    {
        $class = new \ReflectionClass(Post::class);

        $entity = $this->configurator()->initEntity(new Entity(typecast: ['Typecaster', 'Sub/Typecaster']), $class);

        Assert::same($entity->getTypecast(), [Typecaster::class, SubTypecaster::class]);
    }

    public function configuresAnEmbeddingFromItsAttribute(): void
    {
        $class = new \ReflectionClass(Address::class);
        $embeddable = $class->getAttributes(Embeddable::class)[0]->newInstance();

        $entity = $this->configurator()->initEmbedding($embeddable, $class);

        Assert::same($entity->getRole(), 'postal');
        Assert::same($entity->getMapper(), AddressMapper::class);
        Assert::same($entity->getTypecast(), [Typecaster::class, SubTypecaster::class]);
    }

    public function prefixesTheColumnsOfAnEmbedding(): void
    {
        $entity = new EntitySchema();

        $this->configurator()->initFields($entity, new \ReflectionClass(Address::class), 'address_');

        Assert::same($entity->getFields()->get('street')->getColumn(), 'address_street');
    }

    public function wrapsAnExceptionOfTheReader(): never
    {
        $reader = $this->throwingReader(new \RuntimeException('broken', 42));

        Expect::exception(AnnotationException::class)->withMessage('broken')->withCode(42);

        (new Configurator($reader))->initFields(new EntitySchema(), new \ReflectionClass(Address::class));
    }

    public function reportsAnArgumentOfAWrongType(): never
    {
        $reader = $this->throwingReader(new \TypeError('not a string'));

        Expect::exception(AnnotationWrongTypeArgumentException::class)
            ->withMessageContaining(Address::class . '.street')
            ->withMessageContaining('not a string');

        (new Configurator($reader))->initFields(new EntitySchema(), new \ReflectionClass(Address::class));
    }

    public function prefersTheInverseGivenToTheRelation(): void
    {
        $entity = new EntitySchema();
        $entity->setClass(Post::class);

        $this->configurator()->initRelations($entity, new \ReflectionClass(Post::class));

        Assert::same($entity->getRelations()->get('comments')->getInverseName(), 'post');
    }

    public function resolvesTheCollectionClassOfARelation(): void
    {
        $entity = new EntitySchema();
        $entity->setClass(Post::class);

        $this->configurator()->initRelations($entity, new \ReflectionClass(Post::class));

        Assert::same($entity->getRelations()->get('comments')->getOptions()->get('collection'), Comments::class);
    }

    public function keepsTheInnerKeyOfAPropertyForeignKey(): void
    {
        $entity = new EntitySchema();
        $entity->setClass(Post::class);

        $this->configurator()->initForeignKeys(new Entity(), $entity, new \ReflectionClass(Post::class));

        $keys = \iterator_to_array($entity->getForeignKeys(), false);
        Assert::count($keys, 1);
        Assert::same($keys[0]->getTarget(), Author::class);
        Assert::same($keys[0]->getInnerColumns(), ['author_ref']);
    }

    public function makesANullableColumnDefaultToNull(): void
    {
        $field = $this->field(new Column(type: 'string', nullable: true));

        Assert::true($field->getOptions()->get(ColumnOption::OPT_NULLABLE));
        Assert::true($field->getOptions()->has(ColumnOption::OPT_DEFAULT));
        Assert::null($field->getOptions()->get(ColumnOption::OPT_DEFAULT));
    }

    public function leavesANotNullableColumnWithoutDefault(): void
    {
        $field = $this->field(new Column(type: 'string'));

        Assert::false($field->getOptions()->has(ColumnOption::OPT_NULLABLE));
        Assert::false($field->getOptions()->has(ColumnOption::OPT_DEFAULT));
    }

    public function castsTheDefaultWhenAsked(): void
    {
        $field = $this->field(new Column(type: 'string', default: 'x', castDefault: true));

        Assert::true($field->getOptions()->get(ColumnOption::OPT_CAST_DEFAULT));
    }

    public function leavesTheDefaultUncastByDefault(): void
    {
        $field = $this->field(new Column(type: 'string', default: 'x'));

        Assert::false($field->getOptions()->has(ColumnOption::OPT_CAST_DEFAULT));
    }

    public function copiesTheDatabaseSpecificAttributes(): void
    {
        $field = $this->field(new Column(type: 'integer', unsigned: true, zerofill: true));

        Assert::same(\iterator_to_array($field->getAttributes()), ['unsigned' => true, 'zerofill' => true]);
    }

    #[DataSet(['serial', GeneratedField::ON_INSERT])]
    #[DataSet(['bigserial', GeneratedField::ON_INSERT])]
    #[DataSet(['smallserial', GeneratedField::ON_INSERT])]
    #[DataSet(['integer', null])]
    public function generatesSerialColumnsOnInsert(string $type, ?int $generated): void
    {
        $field = $this->field(new Column(type: $type));

        Assert::same($field->getGenerated(), $generated);
    }

    public function resolvesANameWrittenAsAPath(): void
    {
        $name = $this->configurator()->resolveName('/Sub/Comments', new \ReflectionClass(Post::class));

        Assert::same($name, Comments::class);
    }

    public function resolvesANameRelativeToTheGlobalNamespace(): void
    {
        $name = $this->configurator()->resolveName('/ArrayObject', new \ReflectionClass(\stdClass::class));

        Assert::same($name, 'ArrayObject');
    }

    private function field(Column $column): Field
    {
        return $this->configurator()->initField('value', $column, new \ReflectionClass(Post::class), '');
    }

    private function throwingReader(\Throwable $error): Reader
    {
        return new class($error) extends Reader {
            public function __construct(private readonly \Throwable $error) {}

            public function getClassMetadata(\ReflectionClass $class, ?string $name = null): iterable
            {
                return [];
            }

            public function getFunctionMetadata(\ReflectionFunctionAbstract $function, ?string $name = null): iterable
            {
                return [];
            }

            public function getPropertyMetadata(\ReflectionProperty $property, ?string $name = null): iterable
            {
                throw $this->error;
            }

            public function getConstantMetadata(\ReflectionClassConstant $constant, ?string $name = null): iterable
            {
                return [];
            }

            public function getParameterMetadata(\ReflectionParameter $parameter, ?string $name = null): iterable
            {
                return [];
            }
        };
    }

    private function configurator(): Configurator
    {
        return new Configurator(new AttributeReader());
    }
}
