<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\ORM\Relation;
use Spiral\Annotations\Parser;

/**
 * @Annotation
 * @Target("ANNOTATION")
 */
final class Inverse
{
    protected const NAME   = 'inverse';
    protected const SCHEMA = [
        'type'  => Parser::STRING,
        'name'  => Parser::STRING,
        'as'    => Parser::STRING, // alias to name
        'load'  => Parser::STRING,
        'fetch' => Parser::STRING, // alias to load
    ];

    /** @var string */
    public $type;

    /** @var string */
    public $name;

    /** @var string */
    public $load;

    /**
     * @inheritdoc
     */
    public function setAttribute(string $name, $value)
    {
        if ($name == "as") {
            $name = "name";
        }

        if ($name == "fetch") {
            $name = "load";
        }

        parent::setAttribute($name, $value);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getType() !== null && $this->getRelation() !== null;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getRelation(): ?string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getLoadMethod(): ?int
    {
        switch ($this->load) {
            case 'eager':
            case Relation::LOAD_EAGER:
                return Relation::LOAD_EAGER;
            case 'promise':
            case 'lazy':
            case Relation::LOAD_PROMISE:
                return Relation::LOAD_PROMISE;
            default:
                return null;
        }
    }
}