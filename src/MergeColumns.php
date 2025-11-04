<?php

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Utils\EntityUtils;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\Reader as DoctrineReader;
use Spiral\Attributes\ReaderInterface;

/**
 * Copy column definitions from Mapper/Repository to Entity.
 */
final class MergeColumns implements GeneratorInterface
{
    private readonly ReaderInterface $reader;
    private readonly Configurator $generator;
    private readonly EntityUtils $utils;

    public function __construct(DoctrineReader|ReaderInterface|null $reader = null, ?EntityUtils $utils = null)
    {
        $this->reader = ReaderFactory::create($reader);
        $this->generator = new Configurator($this->reader);
        $this->utils = $utils ?? new EntityUtils($this->reader);
    }

    public function run(Registry $registry): Registry
    {
        foreach ($registry as $e) {
            if ($e->getClass() === null || !$registry->hasTable($e)) {
                continue;
            }

            $this->copy($e, $e->getClass());

            // copy from related classes
            $this->copy($e, $e->getMapper());
            $this->copy($e, $e->getRepository());
            $this->copy($e, $e->getSource());
            $this->copy($e, $e->getScope());

            foreach ($registry->getChildren($e) as $child) {
                $this->copy($child, $child->getClass());
            }
        }

        return $registry;
    }

    /**
     * @param class-string|null  $class
     */
    private function copy(EntitySchema $e, ?string $class): void
    {
        if ($class === null) {
            return;
        }

        try {
            $class = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return;
        }

        try {
            $columns = [];
            foreach ($this->utils->findParents($class->getName()) as $parent) {
                $columns = \array_merge($columns, $this->getColumns($parent));
            }
            $columns = \array_merge($columns, $this->getColumns($class));
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), (int) $e->getCode(), $e);
        }

        $columns = \array_filter(
            $columns,
            static fn(string $field): bool => !$e->getFields()->has($field),
            \ARRAY_FILTER_USE_KEY,
        );

        if ($columns === []) {
            return;
        }

        // additional columns (mapped to local fields automatically)
        $this->generator->initColumns($e, $columns, $class);
    }

    private function getColumns(\ReflectionClass $class): array
    {
        $columns = [];

        $columnName = static function (Column $column): string {
            $name = $column->getProperty() ?? $column->getColumn();
            \assert(!empty($name));

            return $name;
        };

        /** @var Table|null $table */
        $table = $this->reader->firstClassMetadata($class, Table::class);
        foreach ($table === null ? [] : $table->getColumns() as $name => $column) {
            if (\is_numeric($name)) {
                $name = $columnName($column);
            }
            $columns[$name] = $column;
        }

        foreach ($this->reader->getClassMetadata($class, Column::class) as $column) {
            $columns[$columnName($column)] = $column;
        }

        return $columns;
    }
}
