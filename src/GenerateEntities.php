<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
class GenerateEntities implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var Parser */
    private $parser;

    /**
     * @param ClassesInterface $locator
     * @param Parser           $parser
     */
    public function __construct(ClassesInterface $locator, Parser $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        foreach ($this->locator->getClasses() as $class) {
            if ($class->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($class->getDocComment());
            if (!isset($ann[Entity::NAME])) {
                continue;
            }

            /** @var Entity $ea */
            $ea = $ann[Entity::NAME];

            $e = $this->initEntity($class, $ea);

            // columns
            $this->initFields($class, $e);

            // relations

            // register entity
            $registry->register($e);

            $registry->linkTable($e, $ea->getDatabase(), $this->resolveTable($e->getRole(), $ea));
        }

        return $registry;
    }

    /**
     * @param \ReflectionClass $class
     * @param Entity           $ann
     * @return EntitySchema
     */
    protected function initEntity(\ReflectionClass $class, Entity $ann): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($ann->getRole() ?? Inflector::camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($class, $ann->getMapper()));
        $e->setRepository($this->resolveName($class, $ann->getRepository()));
        $e->setSource($this->resolveName($class, $ann->getSource()));
        $e->setConstrain($this->resolveName($class, $ann->getConstrain()));

        return $e;
    }

    /**
     * @param \ReflectionClass $class
     * @param EntitySchema     $entity
     */
    protected function initFields(\ReflectionClass $class, EntitySchema $entity)
    {
        foreach ($class->getProperties() as $property) {
            if ($property->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($property->getDocComment());
            if (!isset($ann[Column::NAME])) {
                continue;
            }

            $entity->getFields()->set($property->getName(), $this->defineField($property, $ann[Column::NAME]));
        }
    }

    /**
     * @param \ReflectionProperty $property
     * @param Column              $column
     * @return Field
     */
    protected function defineField(\ReflectionProperty $property, Column $column): Field
    {
        if ($column->getType() === null) {
            throw new AnnotationException(
                "Column type definition is required on `{$property->getName()}`"
            );
        }

        $field = new Field();

        $field->setType($column->getType());
        $field->setColumn($column->getColumn() ?? Inflector::tableize($property->getName()));
        $field->setPrimary($column->isPrimary());
        $field->setTypecast($column->getTypecast());

        if ($column->isNullable()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_NULLABLE, true);
        }

        if ($column->hasDefault()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_DEFAULT, $column->getDefault());
        }

        if ($column->isCastedDefault()) {
            $field->getOptions()->set(\Cycle\Schema\Table\Column::OPT_CAST_DEFAULT, true);
        }

        return $field;
    }

    /**
     * @param string $role
     * @param Entity $ann
     * @return string
     */
    protected function resolveTable(string $role, Entity $ann): string
    {
        if ($ann->getTable() !== null) {
            return $ann->getTable();
        }

        return Inflector::pluralize(Inflector::tableize($role));
    }

    /**
     * Resolve class or role name relative to the current class.
     *
     * @param \ReflectionClass $class
     * @param string           $name
     * @return string
     */
    protected function resolveName(\ReflectionClass $class, ?string $name): ?string
    {
        if (is_null($name) || class_exists($name, true)) {
            return $name;
        }

        $resolved = sprintf(
            "%s\\%s",
            $class->getNamespaceName(),
            ltrim(str_replace('/', '\\', $name), '\\')
        );

        if (class_exists($resolved, true)) {
            return $resolved;
        }

        return $name;
    }
}