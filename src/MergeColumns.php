<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Exception\AnnotationException;
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
    /** @var ReaderInterface */
    private $reader;

    /** @var Configurator */
    private $generator;

    /**
     * @param ReaderInterface|DoctrineReader|null $reader
     */
    public function __construct($reader = null)
    {
        $this->reader = ReaderFactory::create($reader);
        $this->generator = new Configurator($this->reader);
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
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
            $this->copy($e, $e->getConstrain());

            foreach ($registry->getChildren($e) as $child) {
                $this->copy($e, $child->getClass());
            }
        }

        return $registry;
    }

    /**
     * @param EntitySchema $e
     * @param string|null  $class
     */
    protected function copy(EntitySchema $e, ?string $class): void
    {
        if ($class === null) {
            return;
        }

        try {
            $class = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return;
        }

        try {
            /** @var Table|null $tableMeta */
            $tableMeta = $this->reader->firstClassMetadata($class, Table::class);
            /** @var Column[] $columnsMeta */
            $columnsMeta = $this->reader->getClassMetadata($class, Column::class);
        } catch (\Exception $e) {
            throw new AnnotationException($e->getMessage(), $e->getCode(), $e);
        }

        $columns = $tableMeta === null ? [] : $tableMeta->getColumns();
        foreach ($columnsMeta as $column) {
            $columns[] = $column;
        }

        if ($columns === []) {
            return;
        }

        // additional columns (mapped to local fields automatically)
        $this->generator->initColumns($e, $columns, $class);
    }
}
