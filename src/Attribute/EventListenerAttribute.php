<?php

declare (strict_types = 1);

namespace AriAva\Events\Attribute;

final readonly class EventListenerAttribute
{
    public function __construct(private string $instance)
    {
    }

    /**
     * @throws \ReflectionException
     */
    public function eventId(): string|null
    {
        $reflect = new \ReflectionClass($this->instance);
        $attributes = $reflect->getAttributes(EventListenerAutowire::class, \ReflectionAttribute::IS_INSTANCEOF);
        if (0 === count($attributes)) {
            throw new \RuntimeException('Missing arguments in class attribute');
        }

        if (1 < count($attributes)) {
            throw new \InvalidArgumentException('Only one event listener attribute is allowed');
        }

        $attribute = current($attributes);
        $instance = $attribute->newInstance();

        return $instance->eventId();
    }
}
