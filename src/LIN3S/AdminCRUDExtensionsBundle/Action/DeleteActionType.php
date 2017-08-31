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
 * Delete action type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
final class DeleteActionType implements ActionType
{
    use EntityId;

    /**
     * The manager.
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * The session.
     *
     * @var Session
     */
    private $session;

    /**
     * The Twig instance.
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * The url generator.
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * DeleteActionType constructor.
     *
     * @param UrlGeneratorInterface    $urlGenerator The url generator
     * @param ObjectManager            $manager      The manager
     * @param \Twig_Environment        $twig         The twig instance
     * @param Session                  $session      The session
     * @param EventDispatcherInterface $dispatcher   The event dispatcher
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ObjectManager $manager,
        \Twig_Environment $twig,
        Session $session,
        EventDispatcherInterface $dispatcher
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->manager = $manager;
        $this->twig = $twig;
        $this->session = $session;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, Entity $config, Request $request, $options = null)
    {
        $id = (string) $this->getEntityId($entity, $config);
        $repository = $this->manager->getRepository($config->className());
        $entity = $repository->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->manager->remove($entity);
        $this->manager->flush();

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
