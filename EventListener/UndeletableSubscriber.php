<?php

namespace Fluoresce\DoctrineUndeletable\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Fluoresce\DoctrineUndeletable\Exception\UndeletableObjectException;

/**
 * @author Jaik Dean <jaik@fluoresce.co>
 */
class UndeletableSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    const ANNOTATION_CLASS = 'Fluoresce\\DoctrineUndeletable\\Mapping\\Undeletable';

    /**
     * Annotation reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'onFlush',
        );
    }

    /**
     * If it’s a Undeletable object, throw an exception
     *
     * @param OnFlushEventArgs $args
     *
     * @return void
     * @throws UndeletableObjectException
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $object) {
            if ($this->classIsUndeletable(get_class($object))) {
                throw new UndeletableObjectException();
            }
        }
    }

    /**
     * Check whether a class is flagged as undeletable
     *
     * @param string $class
     *
     * @return bool
     */
    protected function classIsUndeletable($class)
    {
        static $cache = array();

        if (!array_key_exists($class, $cache)) {
            $refl = new \ReflectionClass($class);
            $cache[$class] = (null !== $this->reader->getClassAnnotation($refl, self::ANNOTATION_CLASS));
        }

        return $cache[$class];
    }
}
