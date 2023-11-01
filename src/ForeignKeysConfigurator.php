<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\ForeignKey;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;

/**
 * @internal
 */
final class ForeignKeysConfigurator
{
    /**
     * @var array<array{
     *     entity: EntitySchema,
     *     foreignKey: ForeignKey
     *     property?: \ReflectionProperty,
     * }>
     */
    private array $foreignKeys = [];

    public function __construct(
        private EntityUtils $utils,
        private ReaderInterface $reader,
    ) {
    }

    public function addFromEntity(EntitySchema $entity, Entity $annotation): void
    {
        foreach ($annotation->getForeignKeys() as $foreignKey) {
            $this->add($entity, $foreignKey);
        }
    }

    public function addFromClass(EntitySchema $entity, \ReflectionClass $class): void
    {
        try {
            /** @var ForeignKey $ann */
            $ann = $this->reader->firstClassMetadata($class, ForeignKey::class);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        if ($ann !== null) {
            $this->add($entity, $ann);
        }

        foreach ($class->getProperties() as $property) {
            try {
                /** @var ForeignKey $ann */
                $ann = $this->reader->firstPropertyMetadata($property, ForeignKey::class);
            } catch (\Exception $e) {
                throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($ann !== null) {
                $this->add($entity, $ann, $property);
            }
        }
    }

    public function configure(Registry $registry): void
    {
        foreach ($this->foreignKeys as $foreignKey) {
            $attribute = $foreignKey['foreignKey'];
            $entity = $foreignKey['entity'];
            $property = $foreignKey['property'];

            $target = $this->utils->resolveTarget($registry, $attribute);
            \assert(!empty($target), 'Unable to resolve foreign key target entity.');
            $targetEntity = $registry->getEntity($target);

            $registry->getTableSchema($entity)
                ->foreignKey(
                    $this->getInnerColumns($entity, $attribute->getInnerKey(), $property),
                    $foreignKey->indexCreate
                )
                ->references(
                    $registry->getTable($targetEntity),
                    $this->getOuterColumns($attribute->getOuterKey(), $targetEntity)
                )
                ->onUpdate($attribute->action)
                ->onDelete($attribute->action);
        }
    }

    /**
     * @param non-empty-string $key
     *
     * @return non-empty-string
     */
    private function getColumn(EntitySchema $entity, string $key): string
    {
        return match (true) {
            $entity->getFields()->has($key) => $entity->getFields()->get($key)->getColumn(),
            $entity->getFields()->hasColumn($key) => $key,
            default => throw new AnnotationException('Unable to resolve column name.'),
        };
    }

    /**
     * @param array<non-empty-string>|null $innerKeys
     *
     * @return array<non-empty-string>
     *
     * @throws AnnotationException
     */
    private function getInnerColumns(
        EntitySchema $entity,
        ?array $innerKeys = null,
        ?\ReflectionProperty $property = null
    ): array {
        if ($innerKeys === null && $property === null) {
            throw new AnnotationException('Unable to resolve foreign key column.');
        }

        if ($innerKeys === null) {
            $innerKeys = [$property->getName()];
        }

        $columns = [];
        foreach ($innerKeys as $innerKey) {
            $columns[] = $this->getColumn($entity, $innerKey);
        }

        return $columns;
    }

    /**
     * @param array<non-empty-string> $outerKeys
     *
     * @return array<non-empty-string>
     *
     * @throws AnnotationException
     */
    private function getOuterColumns(array $outerKeys, EntitySchema $target): array
    {
        $columns = [];
        foreach ($outerKeys as $outerKey) {
            $columns[] = $this->getColumn($target, $outerKey);
        }

        return $columns;
    }

    private function add(EntitySchema $entity, ForeignKey $foreignKey, ?\ReflectionProperty $property = null): void
    {
        $this->foreignKeys[] = [
            'entity' => $entity,
            'foreignKey' => $foreignKey,
            'property' => $property,
        ];
    }
}
