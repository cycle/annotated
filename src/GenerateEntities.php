<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Schema\Definition\Entity as EntitySchema;
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
        foreach ($this->locator->getClasses() as $reflection) {
            if ($reflection->getDocComment() === false) {
                continue;
            }

            $head = $this->parser->parse($reflection->getDocComment());
            if (!isset($head[Entity::NAME])) {
                continue;
            }

            /** @var Entity $ann */
            $ann = $head[Entity::NAME];

            $e = $this->initEntity($reflection, $ann);

            // register entity
            $registry->register($e);

            $registry->linkTable($e, $ann->getDatabase(), $this->resolveTable($e->getRole(), $ann));
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
     * @param string $role
     * @param Entity $ann
     * @return string
     */
    protected function resolveTable(string $role, Entity $ann): string
    {
        if ($ann->getTable() !== null) {
            return $ann->getTable();
        }

        return $role;
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