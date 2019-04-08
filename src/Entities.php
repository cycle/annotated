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
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Exception\RelationException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Doctrine\Common\Inflector\Inflector;
use Spiral\Annotations\Parser;
use Spiral\Tokenizer\ClassesInterface;

/**
 * Generates ORM schema based on annotated classes.
 */
final class Entities implements GeneratorInterface
{
    /** @var ClassesInterface */
    private $locator;

    /** @var Parser */
    private $parser;

    /** @var Generator */
    private $generator;

    /**
     * @param ClassesInterface $locator
     * @param Parser|null      $parser
     */
    public function __construct(ClassesInterface $locator, Parser $parser = null)
    {
        $this->locator = $locator;
        $this->parser = $parser ?? Generator::getDefaultParser();
        $this->generator = new Generator($this->parser);
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        /** @var EntitySchema[] $children */
        $children = [];
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

            $e = $this->generator->initEntity($ea, $class);

            // columns
            $this->generator->initFields($e, $class);

            // relations
            $this->generator->initRelations($e, $class);

            if ($this->hasParent($registry, $e->getClass())) {
                $children[] = $e;
                continue;
            }

            // register entity (OR find parent)
            $registry->register($e);

            $registry->linkTable(
                $e,
                $ea->getDatabase(),
                $ea->getTable() ?? $this->tableName($e->getRole())
            );
        }

        foreach ($children as $e) {
            $registry->registerChild($registry->getEntity($this->findParent($registry, $e->getClass())), $e);
        }

        return $this->normalizeNames($registry);
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    protected function normalizeNames(Registry $registry): Registry
    {
        // resolve all the relation target names into roles
        foreach ($this->locator->getClasses() as $class) {
            if (!$registry->hasEntity($class->getName())) {
                continue;
            }

            $e = $registry->getEntity($class->getName());

            // relations
            foreach ($e->getRelations() as $name => $r) {
                try {
                    $r->setTarget($this->resolveTarget($registry, $r->getTarget()));

                    if ($r->getOptions()->has('though')) {
                        $r->getOptions()->set(
                            'though',
                            $this->resolveTarget($registry, $r->getOptions()->get('though'))
                        );
                    }
                } catch (RegistryException $ex) {
                    throw new RelationException(
                        sprintf("Unable to resolve `%s`.`%s` relation target (not found or invalid)",
                            $e->getRole(),
                            $name
                        ),
                        $ex->getCode(),
                        $ex
                    );
                }
            }
        }

        return $registry;
    }

    /**
     * @param Registry $registry
     * @param string   $name
     * @return string|null
     */
    protected function resolveTarget(Registry $registry, string $name): ?string
    {
        if (is_null($name) || interface_exists($name, true)) {
            // do not resolve interfaces
            return $name;
        }

        return $registry->getEntity($name)->getRole();
    }

    /**
     * @param string $role
     * @return string
     */
    protected function tableName(string $role): string
    {
        return Inflector::pluralize(Inflector::tableize($role));
    }

    /**
     * @param Registry $registry
     * @param string   $class
     * @return bool
     */
    protected function hasParent(Registry $registry, string $class): bool
    {
        return $this->findParent($registry, $class) !== null;
    }

    /**
     * @param Registry $registry
     * @param string   $class
     * @return string|null
     */
    protected function findParent(Registry $registry, string $class): ?string
    {
        $parents = class_parents($class);
        foreach (array_reverse($parents) as $parent) {
            $class = new \ReflectionClass($parent);
            if ($class->getDocComment() === false) {
                continue;
            }

            $ann = $this->parser->parse($class->getDocComment());
            if (isset($ann[Entity::NAME])) {
                return $parent;
            }
        }

        return null;
    }
}