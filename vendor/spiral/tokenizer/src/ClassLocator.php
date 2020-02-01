<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Tokenizer\Exception\LocatorException;

/**
 * Can locate classes in a specified directory.
 */
final class ClassLocator extends AbstractLocator implements ClassesInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClasses($target = null): array
    {
        if (!empty($target) && (is_object($target) || is_string($target))) {
            $target = new \ReflectionClass($target);
        }

        $result = [];
        foreach ($this->availableClasses() as $class) {
            try {
                $reflection = $this->classReflection($class);
            } catch (LocatorException $e) {
                //Ignoring
                continue;
            }

            if (!$this->isTargeted($reflection, $target) || $reflection->isInterface()) {
                continue;
            }

            $result[$reflection->getName()] = $reflection;
        }

        return $result;
    }

    /**
     * Classes available in finder scope.
     *
     * @return array
     */
    protected function availableClasses(): array
    {
        $classes = [];

        foreach ($this->availableReflections() as $reflection) {
            $classes = array_merge($classes, $reflection->getClasses());
        }

        return $classes;
    }

    /**
     * Check if given class targeted by locator.
     *
     * @param \ReflectionClass      $class
     * @param \ReflectionClass|null $target
     * @return bool
     */
    protected function isTargeted(\ReflectionClass $class, \ReflectionClass $target = null): bool
    {
        if (empty($target)) {
            return true;
        }

        if (!$target->isTrait()) {
            //Target is interface or class
            return $class->isSubclassOf($target) || $class->getName() == $target->getName();
        }

        //Checking using traits
        return in_array($target->getName(), $this->fetchTraits($class->getName()));
    }
}
