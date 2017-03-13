<?php

/*
 * This file is part of the Admin Bundle.
 *
 * Copyright (c) 2015-2016 LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminCRUDExtensionsBundle;

use LIN3S\AdminCRUDExtensionsBundle\DependencyInjection\Lin3sAdminCRUDExtensionsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * AdminCRUDExtensionsBundle's kernel class.
 *
 * @author Gorka Laucirica <gorka.lauzirka@gmail.com>
 */
class Lin3sAdminCRUDExtensionsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new Lin3sAdminCRUDExtensionsExtension();
    }
}
