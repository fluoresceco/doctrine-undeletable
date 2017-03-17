<?php

namespace Fluoresce\DoctrineUndeletable\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Entity annotation for undeletable behaviour
 *
 * @author Jaik Dean <jaik@fluoresce.co>
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Undeletable extends Annotation
{
}
