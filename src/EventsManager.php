<?php

declare(strict_types=1);

namespace AriAva\Events;

use AriAva\Autowire\AutowireProxy;
use AriAva\Events\Attribute\EventListenerAttribute;
use AriAva\Events\Attribute\EventListenerAutowire;

final class EventsManager
{
    public function __construct(
        private readonly EventListener $listener,
        private readonly EventDispatcher $eventDispatcher,
        private array $paths = [])
    {}

    public function addPath(string $path): void
    {
        if ($this->contains($path)) {
            return;
        }

        $this->paths[$path] = $path;
    }

    public function dispatch(object $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function contains(string $path): bool
    {
        return array_key_exists($path, $this->paths);
    }

    /**
     * @throws \ReflectionException
     */
    public function autowire(): void
    {
        foreach ($this->paths as $path) {
            $this->autowirePath($path);
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function autowirePath(string $path): void
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        /** @var \SplFileInfo $info */
        foreach ($iterator as $info) {
            if ($info->isDir() && ($info->getFilename() === '.' || $info->getFilename() === '..')) {
                continue;
            }

            if ($info->isDir()) {
                $this->autowirePath($info->getPathname());

                continue;
            }

            $autowireProxy = new AutowireProxy($info, EventListenerAutowire::class);
            if (false === $autowireProxy->canAutowire()) {
                continue;
            }

            if (null === $autowireProxy->getReflection()) {
                continue;
            }

            $attributes = new EventListenerAttribute($autowireProxy->getNamespace());
            $eventId = $attributes->eventId();
            $this->listener->add($eventId, $autowireProxy->getNamespace(), $autowireProxy->getReflection());
        }
    }

    public function getListener(): EventListener
    {
        return $this->listener;
    }

    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }
}
