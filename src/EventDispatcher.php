<?php

declare(strict_types=1);

namespace AriAva\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use AriAva\Contracts\Events\EventListenerInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private array $initializedListeners;

    public function __construct(private readonly EventListener $eventListener)
    {
        $this->initializedListeners = [];
    }

    /**
     * @throws \ReflectionException
     */
    public function dispatch(object $event): void
    {
        $listeners = $this->eventListener->getListenersForEvent($event);
        if (0 === iterator_count($listeners)) {
            return;
        }

        /** @var \ReflectionClass $listener */
        foreach ($listeners as $listener) {
            /** @var EventListenerInterface $eventListener */
            if (array_key_exists($listener->getName(), $this->initializedListeners)) {
                $eventListener = $this->initializedListeners[$listener->getName()];
                $eventListener->process($event);

                continue;
            }

            $eventListener = $listener->newInstance();
            $this->initializedListeners[$listener->getName()] = $eventListener;

            $eventListener->process($event);
        }
    }
}
