<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Tokenizer;

use Spiral\Tokenizer\Exception\LocatorException;
use Spiral\Tokenizer\Reflection\ReflectionInvocation;

/**
 * Can locate invocations in a specified directory. Can only find simple invocations!
 *
 * Potentially this class have to be rewritten in order to use new PHP API and AST tree, for now it
 * still relies on legacy token based parser.
 */
final class InvocationLocator extends AbstractLocator implements InvocationsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getInvocations(\ReflectionFunctionAbstract $function): array
    {
        $result = [];
        foreach ($this->availableInvocations($function->getName()) as $invocation) {
            if ($this->isTargeted($invocation, $function)) {
                $result[] = $invocation;
            }
        }

        return $result;
    }

    /**
     * Invocations available in finder scope.
     *
     * @param string $signature Method or function signature (name), for pre-filtering.
     * @return ReflectionInvocation[]|\Generator
     */
    protected function availableInvocations(string $signature = ''): \Generator
    {
        $signature = strtolower(trim($signature, '\\'));
        foreach ($this->availableReflections() as $reflection) {
            foreach ($reflection->getInvocations() as $invocation) {
                if (!empty($signature)
                    && strtolower(trim($invocation->getName(), '\\')) != $signature
                ) {
                    continue;
                }

                yield $invocation;
            }
        }
    }

    /**
     * @param ReflectionInvocation        $invocation
     * @param \ReflectionFunctionAbstract $function
     *
     * @return bool
     */
    protected function isTargeted(ReflectionInvocation $invocation, \ReflectionFunctionAbstract $function): bool
    {
        if ($function instanceof \ReflectionFunction) {
            return !$invocation->isMethod();
        }

        try {
            $reflection = $this->classReflection($invocation->getClass());
        } catch (LocatorException $e) {
            return false;
        }

        /**
         * @var \ReflectionMethod $function
         */
        $target = $function->getDeclaringClass();

        if ($target->isTrait()) {
            //Let's compare traits
            return in_array($target->getName(), $this->fetchTraits($invocation->getClass()));
        }

        return $reflection->getName() == $target->getName() || $reflection->isSubclassOf($target);
    }
}
