<?php

declare (strict_types = 1);

namespace AriAva\Events\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class EventListenerAutowire
{
    public function __construct(private string $eventId)
    {
    }

    public function eventId(): string
    {
        return $this->eventId;
    }
}
