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
use Cycle\Annotated\Annotation\Relation as RelationAnnotation;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Schema\Definition\Entity as EntitySchema;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Generator\SyncTables;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Annotations\Parser;

final class Generator
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param Entity           $ann
     * @param \ReflectionClass $class
     * @return EntitySchema
     */
    public function initEntity(Entity $ann, \ReflectionClass $class): EntitySchema
    {
        $e = new EntitySchema();
        $e->setClass($class->getName());

        $e->setRole($ann->getRole() ?? Inflector::camelize($class->getShortName()));

        // representing classes
        $e->setMapper($this->resolveName($ann->getMapper(), $class));
        $e->setRepository($this->resolveName($ann->getRepository(), $class));
        $e->setSource($this->resolveName($ann->getSource(), $class));
        $e->setConstrain($this->resolveName($ann->getConstrain(), $class));

        if ($ann->isReadonlySchema()) {
            $e->getOptions()->set(SyncTables::READONLY_SCHEMA, true);
        }

        return $e;
    }

    /**
     * @param EntitySchema     $entity
     * @param \ReflectionClass $class
     */
    public function initFields(EntitySchema $entity, \ReflectionClass $class)
    {
        foreach ($class->getProperties() as $property) {
            if ($property->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($property->getDocComment());
            if (!isset($ann[Column::NAME])) {
                continue;
            }

            $entity->getFields()->set(
                $property->getName(),
                $this->initField(
                    $property->getName(),
                    $ann[Column::NAME],
                    $class
                )
            );
        }
    }

    /**
     * @param EntitySchema     $entity
     * @param \ReflectionClass $class
     */
    public function initRelations(EntitySchema $entity, \ReflectionClass $class)
    {
        foreach ($class->getProperties() as $property) {
            if ($property->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($property->getDocComment());

            foreach ($ann as $ra) {
                if (!$ra instanceof RelationAnnotation\RelationInterface) {
                    continue;
                }

                if ($ra->getTarget() === null) {
                    throw new AnnotationException(
                        "Relation target definition is required on `{$entity->getClass()}`"
                    );
                }

                $relation = new Relation();
                $relation->setTarget($this->resolveName($ra->getTarget(), $class));
                $relation->setType($ra->getName());

                if ($ra->isInversed()) {
                    $relation->setInverse($ra->getInverseName(), $ra->getInverseType());
                }

                foreach ($ra->getOptions() as $option => $value) {
                    if ($option === "though") {
                        $value = $this->resolveName($value, $class);
                    }

                    $relation->getOptions()->set($option, $value);
                }

                // need relation definition
                $entity->getRelations()->set($property->getName(), $relation);
            }
        }
    }

    /**
     * @param EntitySchema     $entity
     * @param Column[]         $columns
     * @param \ReflectionClass $class
     */
    public function initColumns(EntitySchema $entity, array $columns, \ReflectionClass $class)
    {
        foreach ($columns as $name => $column) {
            if ($column->getColumn() === null && is_numeric($name)) {
                throw new AnnotationException(
                    "Column name definition is required on `{$entity->getClass()}`"
                );
            }

            if ($column->getType() === null) {
                throw new AnnotationException(
                    "Column type definition is required on `{$entity->getClass()}`"
                );
            }

            $entity->getFields()->set(
                $name ?? $column->getColumn(),
                $this->initField($column->getColumn() ?? $name, $column, $class)
            );
        }
    }

    /**
     * @param string           $name
     * @param Column           $column
     * @param \ReflectionClass $class
     * @return Field
     */
    public function initField(string $name, Column $column, \ReflectionClass $class): Field
    {
        if ($column->getType() === null) {
            throw new AnnotationException(
                "Column type definition is required on `{$class->getName()}`.`{$name}`"
            );
        }

        $field = new Field();

        $field->setType($column->getType());
        $field->setColumn($column->getColumn() ?? Inflector::tableize($name));
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
     * Resolve class or role name relative to the current class.
     *
     * @param string           $name
     * @param \ReflectionClass $class
     * @return string
     */
    public function resolveName(?string $name, \ReflectionClass $class): ?string
    {
        if (is_null($name) || class_exists($name, true) || interface_exists($name, true)) {
            return $name;
        }

        $resolved = sprintf(
            "%s\\%s",
            $class->getNamespaceName(),
            ltrim(str_replace('/', '\\', $name), '\\')
        );

        if (class_exists($resolved, true) || interface_exists($resolved, true)) {
            return $resolved;
        }

        return $name;
    }

    /**
     * @return Parser
     */
    public static function defaultParser(): Parser
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new Table\Index());

        // embedded relations
        $p->register(new RelationAnnotation\BelongsTo());
        $p->register(new RelationAnnotation\HasOne());
        $p->register(new RelationAnnotation\HasMany());
        $p->register(new RelationAnnotation\RefersTo());
        $p->register(new RelationAnnotation\ManyToMany());
        $p->register(new RelationAnnotation\Morphed\BelongsToMorphed());
        $p->register(new RelationAnnotation\Morphed\MorphedHasOne());
        $p->register(new RelationAnnotation\Morphed\MorphedHasMany());

        return $p;
    }
}