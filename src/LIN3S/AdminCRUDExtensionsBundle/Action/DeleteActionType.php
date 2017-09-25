<?php

/*
 * This file is part of the Admin CRUD Extensions Bundle.
 *
 * Copyright (c) 2017 - present LIN3S <info@lin3s.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LIN3S\AdminCRUDExtensionsBundle\Action;

use Doctrine\Common\Persistence\ObjectManager;
use LIN3S\AdminBundle\Configuration\Model\Entity;
use LIN3S\AdminBundle\Configuration\Type\ActionType;
use LIN3S\AdminCRUDExtensionsBundle\Event\EntityRemoved;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Beñat Espiña <benatespina@gmail.com>
 */
final class DeleteActionType implements ActionType
{
    use EntityId;

    private $session;
    private $twig;
    private $urlGenerator;
    private $dispatcher;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        \Twig_Environment $twig,
        Session $session,
        EventDispatcherInterface $dispatcher
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    public function execute($entity, Entity $config, Request $request, $options = null)
    {
        $id = (string) $this->getEntityId($entity, $config);
        $repository = $config->repository();
        $entity = $repository->find($config, $id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $repository->remove($entity);

        $this->dispatcher->dispatch(EntityRemoved::name(), new EntityRemoved($id, $config->className()));

        $this->session->getFlashBag()->add(
            'lin3s_admin_success',
            sprintf('%s id removed successfully', $id)
        );

        return new RedirectResponse(
            $this->urlGenerator->generate('lin3s_admin_list', [
                'entity' => $config->name(),
            ])
        );
    }
}
