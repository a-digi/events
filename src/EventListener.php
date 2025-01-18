<?php

declare(strict_types=1);

namespace AriAva\Events;

use Psr\EventDispatcher\ListenerProviderInterface;

final class EventListener implements ListenerProviderInterface
{
    public function __construct(private array $eventListeners = [])
    {
    }

    public function add(string $eventId, string $dispatcherId, \ReflectionClass $listener): void
    {
        if ($this->contains($eventId, $dispatcherId)) {
            return;
        }

        if (false === array_key_exists($eventId, $this->eventListeners)) {
            $this->eventListeners[$eventId] = [];
        }

        $this->eventListeners[$eventId][$dispatcherId] = $listener;
    }

    public function contains(string $eventId, string $dispatcherId): bool
    {
        if (false === array_key_exists($eventId, $this->eventListeners)) {
            return false;
        }

        return array_key_exists($dispatcherId, $this->eventListeners[$eventId]);
    }

    public function getListenersForEvent(object $event): iterable
    {
        if (false === method_exists($event, 'getId')) {
            throw new \InvalidArgumentException('The events method must implement getId() method.');
        }

        if (false === array_key_exists($event->getId(), $this->eventListeners)) {
            return [];
        }

        return $this->eventListeners[$event->getId()];
    }
}
