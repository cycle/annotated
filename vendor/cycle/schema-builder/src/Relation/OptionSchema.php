<?php

/**
 * Cycle ORM Schema Builder.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Schema\Relation;

use Cycle\Schema\Exception\OptionException;

/**
 * Calculate missing option values using template and relation context.
 */
final class OptionSchema
{
    /** @var array */
    private $aliases = [];

    /** @var array */
    private $options = [];

    /** @var array */
    private $template = [];

    /** @var array */
    private $context = [];

    /**
     * @param array $aliases
     */
    public function __construct(array $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        $result = [];

        foreach ($this->template as $option => $value) {
            $value = $this->get($option);

            $alias = array_search($option, $this->aliases, true);
            $result[$alias] = $value;
        }

        return $result;
    }

    /**
     * Create new option set with user provided options.
     *
     * @param iterable $options
     * @return OptionSchema
     */
    public function withOptions(iterable $options): self
    {
        $r = clone $this;

        foreach ($options as $name => $value) {
            if (!array_key_exists($name, $r->aliases) && !array_key_exists($name, $r->template)) {
                throw new OptionException("Undefined relation option `{$name}`");
            }

            $r->options[$name] = $value;
        }

        return $r;
    }

    /**
     * Create new option set with option rendering template. Template expect to allocate
     * relation options only in a integer constants.
     *
     * @param array $template
     * @return OptionSchema
     */
    public function withTemplate(array $template): self
    {
        $r = clone $this;
        $r->template = $template;

        return $r;
    }

    /**
     * Create new option set with relation context values (i.e. relation name, target name and etc).
     *
     * @param array $context
     * @return OptionSchema
     */
    public function withContext(array $context): self
    {
        $r = clone $this;
        $r->context += $context;

        return $r;
    }

    /**
     * Check if option has been defined.
     *
     * @param int $option
     * @return bool
     */
    public function has(int $option): bool
    {
        return array_key_exists($option, $this->template);
    }

    /**
     * Get calculated option value.
     *
     * @param int $option
     * @return mixed
     */
    public function get(int $option)
    {
        if (!$this->has($option)) {
            throw new OptionException("Undefined relation option `{$option}`");
        }

        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        // user defined value
        foreach ($this->aliases as $alias => $targetOption) {
            if ($targetOption === $option && isset($this->options[$alias])) {
                return $this->options[$alias];
            }
        }

        // non template value
        $value = $this->template[$option];
        if (!is_string($value)) {
            return $value;
        }

        return $this->calculate($option, $value);
    }

    /**
     * Calculate option value using templating.
     *
     * @param int    $option
     * @param string $value
     * @return string
     */
    private function calculate(int $option, string $value): string
    {
        foreach ($this->context as $name => $ctxValue) {
            $value = $this->injectValue($name, $ctxValue, $value);
        }

        foreach ($this->aliases as $name => $targetOption) {
            if ($option !== $targetOption) {
                $value = $this->injectOption($name, $targetOption, $value);
            }
        }

        return $value;
    }

    /**
     * @param string $name
     * @param int    $option
     * @param string $target
     * @return string
     */
    private function injectOption(string $name, int $option, string $target): string
    {
        if (strpos($target, "{{$name}}") === false) {
            return $target;
        }

        return str_replace("{{$name}}", $this->get($option), $target);
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $target
     * @return string
     */
    private function injectValue(string $name, string $value, string $target): string
    {
        return str_replace("{{$name}}", $value, $target);
    }
}
