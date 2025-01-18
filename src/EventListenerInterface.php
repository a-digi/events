<?php

declare(strict_types=1);

namespace AriAva\Events;

interface EventListenerInterface
{
    public function getEventId(): string;
    public function process(mixed $arguments);
    public function getDispatcherId(): string;
}
