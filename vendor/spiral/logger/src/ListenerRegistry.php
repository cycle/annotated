<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Logger;

/**
 * Contains all log listeners.
 */
final class ListenerRegistry implements ListenerRegistryInterface
{
    /** @var callable[] */
    private $listeners = [];

    /**
     * @param callable $listener
     */
    public function addListener(callable $listener): void
    {
        if (!array_search($listener, $this->listeners, true)) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * @param callable $listener
     */
    public function removeListener(callable $listener): void
    {
        $key = array_search($listener, $this->listeners, true);
        if ($key !== null) {
            unset($this->listeners[$key]);
        }
    }

    /**
     * @return callable[]
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }
}
