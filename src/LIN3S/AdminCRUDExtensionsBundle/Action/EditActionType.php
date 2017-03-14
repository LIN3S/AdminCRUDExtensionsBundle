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
use LIN3S\AdminBundle\Form\FormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Edit action type class.
 *
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class EditActionType implements ActionType
{
    use EntityId;

    /**
     * The form handler.
     *
     * @var FormHandler
     */
    private $formHandler;

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
     * DeleteActionType constructor.
     *
     * @param FormHandler       $formHandler The form handler,
     * @param ObjectManager     $manager     The manager
     * @param \Twig_Environment $twig        The twig instance
     * @param Session           $session     The session
     */
    public function __construct(
        FormHandler $formHandler,
        ObjectManager $manager,
        \Twig_Environment $twig,
        Session $session
    ) {
        $this->formHandler = $formHandler;
        $this->manager = $manager;
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, Entity $config, Request $request, $options = null)
    {
        if (!isset($options['form'])) {
            throw new \InvalidArgumentException(
                sprintf('EditActionType requires a form class as an option')
            );
        }

        $id = $this->getEntityId($entity, $config);
        $manager = $this->manager->getRepository($config->className());
        $entity = $manager->find($id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $form = $this->formHandler->handleForm(
            $options['form'], $entity, $request
        );

        if ($form->isValid()) {
            $this->session->getFlashBag()->add(
                'lin3s_admin_success',
                sprintf('%s edited successfully', $config->name())
            );
        } elseif ($form->isSubmitted()) {
            $this->session->getFlashBag()->add(
                'lin3s_admin_error',
                sprintf('Errors while saving %s. Please check all fields and try again', $config->name())
            );
        }

        return new Response(
            $this->twig->render('Lin3sAdminBundle:Admin:form.html.twig', [
                'entity'       => $entity,
                'entityConfig' => $config,
                'form'         => $form->createView(),
            ])
        );
    }
}
