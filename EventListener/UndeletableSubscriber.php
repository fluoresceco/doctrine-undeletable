<?php

declare(strict_types=1);

namespace Fluoresce\DoctrineUndeletable\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Fluoresce\DoctrineUndeletable\Exception\UndeletableObjectException;
use Fluoresce\DoctrineUndeletable\Mapping\Undeletable;

final class UndeletableSubscriber implements EventSubscriber
{
    const ANNOTATION_CLASS = Undeletable::class;

    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            'onFlush',
        ];
    }

    /**
     * @throws UndeletableObjectException if attempting to flush an Undeletable object
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $object) {
            if ($this->classIsUndeletable(\get_class($object))) {
                throw new UndeletableObjectException();
            }
        }
    }

    /**
     * Check whether a class is flagged as undeletable
     *
     * @param class-string $class
     */
    private function classIsUndeletable(string $class): bool
    {
        /** @var array<class-string,bool> */
        static $cache = [];

        if (!array_key_exists($class, $cache)) {
            $refl = new \ReflectionClass($class);
            $cache[$class] = (null !== $this->reader->getClassAnnotation($refl, self::ANNOTATION_CLASS));
        }

        return $cache[$class];
    }
}
