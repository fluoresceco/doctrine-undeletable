<?php

declare(strict_types=1);

namespace Fluoresce\DoctrineUndeletable\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Entity annotation for undeletable behaviour
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Undeletable extends Annotation
{
}
