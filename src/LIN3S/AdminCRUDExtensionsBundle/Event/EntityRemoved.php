<?php

/*
 * This file is part of the Admin CRUD Extensions Bundle.
 *
 * Copyright (c) 2017 - present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminCRUDExtensionsBundle\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * @author Beñat Espiña <benatespina@gmail.com>
 */
final class EntityRemoved extends SymfonyEvent implements Event
{
    private $entityId;
    private $entityType;

    public function __construct($entityId, $entityType)
    {
        $this->entityId = $entityId;
        $this->entityType = $entityType;
    }

    public function entityId()
    {
        return $this->entityId;
    }

    public function entityType()
    {
        return $this->entityType;
    }

    public static function name()
    {
        return 'lin3s_admin_entity_removed';
    }
}
