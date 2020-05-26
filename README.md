# Doctrine Undeletable

This library provides an `Undeletable` annotation for Doctrine entities.

When added to an entity class, this annotation causes Doctrine to throw an
exception when attempting to delete an entity.

## Installation

Open a command console, enter your project directory and execute the following
command to download the latest stable version of this bundle:

```bash
$ composer require fluoresce/doctrine-undeletable
```

This command requires you to have Composer installed globally, as explained in
the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the
Composer documentation.

## Documentation

### Configuration

#### Symfony

Register the Doctrine event subscriber as a service. In `services.yml` this
would look as follows.

```yaml
services:
    fluoresce.listener.undeletable:
        class: Fluoresce\DoctrineUndeletable\EventListener\UndeletableSubscriber
        arguments: ["@annotation_reader"]
        tags:
            - { name: doctrine.event_subscriber, connection: default }
```

### Basic Usage

This example shows a `Book` ORM entity which should never be deleted.

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use Fluoresce\DoctrineUndeletable\Mapping\Undeletable;

/**
 * @ORM\Entity
 * @Undeletable
 */
class Book
{
    …
}
```

When you attempt to delete a `Book`, an exception will be thrown.

```php
$book = $entityManager->find('Book', 123);
$entityManager->remove($book);

try {
    $entityManager->flush();
} catch (Fluoresce\DoctrineUndeletable\Exception\UndeletableObjectException $e) {
    // $book was not deleted from the database
}
```
