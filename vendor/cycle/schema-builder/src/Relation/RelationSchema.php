<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Relation;

use Cycle\ORM\Relation;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Exception\RegistryException;
use Cycle\Schema\Registry;
use Cycle\Schema\RelationInterface;

/**
 * Defines relation options, renders needed columns and other options.
 */
abstract class RelationSchema implements RelationInterface
{
    // relation rendering options
    public const INDEX_CREATE     = 1001;
    public const FK_CREATE        = 1002;
    public const FK_ACTION        = 1003;
    public const INVERSE          = 1005;
    public const MORPH_KEY_LENGTH = 1009;

    // options to be excluded from generated schema (helpers)
    protected const EXCLUDE = [self::FK_CREATE, self::FK_ACTION, self::INDEX_CREATE];

    // exported relation type
    protected const RELATION_TYPE = null;

    // name of all required relation options
    protected const RELATION_SCHEMA = [];

    /** @var string */
    protected $source;

    /** @var string */
    protected $target;

    /** @var OptionSchema */
    protected $options;

    /**
     * @inheritdoc
     */
    public function withContext(string $name, string $source, string $target, OptionSchema $options): RelationInterface
    {
        $relation = clone $this;
        $relation->source = $source;
        $relation->target = $target;

        $relation->options = $options->withTemplate(static::RELATION_SCHEMA)->withContext([
            'relation'    => $name,
            'source:role' => $source,
            'target:role' => $target,
        ]);

        return $relation;
    }

    /**
     * @param Registry $registry
     */
    public function compute(Registry $registry): void
    {
        $this->options = $this->options->withContext([
            'source:primaryKey' => $this->getPrimary($registry->getEntity($this->source))
        ]);

        if ($registry->hasEntity($this->target)) {
            $this->options = $this->options->withContext([
                'target:primaryKey' => $this->getPrimary($registry->getEntity($this->target))
            ]);
        }
    }

    /**
     * @return array
     */
    public function packSchema(): array
    {
        $schema = [];

        foreach (static::RELATION_SCHEMA as $option => $template) {
            if (in_array($option, static::EXCLUDE)) {
                continue;
            }

            $schema[$option] = $this->options->get($option);
        }

        // load option is not required in schema
        unset($schema[Relation::LOAD]);

        return [
            Relation::TYPE   => static::RELATION_TYPE,
            Relation::TARGET => $this->target,
            Relation::LOAD   => $this->getLoadMethod(),
            Relation::SCHEMA => $schema
        ];
    }

    /**
     * @return int|null
     */
    protected function getLoadMethod(): ?int
    {
        if (!$this->options->has(Relation::LOAD)) {
            return null;
        }

        switch ($this->options->get(Relation::LOAD)) {
            case 'eager':
            case Relation::LOAD_EAGER:
                return Relation::LOAD_EAGER;
            default:
                return Relation::LOAD_PROMISE;
        }
    }

    /**
     * @return OptionSchema
     */
    protected function getOptions(): OptionSchema
    {
        return $this->options;
    }

    /**
     * @param Entity $entity
     * @return string
     *
     * @throws RegistryException
     */
    protected function getPrimary(Entity $entity): string
    {
        foreach ($entity->getFields() as $name => $field) {
            if ($field->isPrimary()) {
                return $name;
            }
        }

        throw new RegistryException("Entity `{$entity->getRole()}` must have defined primary key");
    }
}
