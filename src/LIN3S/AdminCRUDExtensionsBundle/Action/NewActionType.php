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

use LIN3S\AdminBundle\Configuration\Model\Entity;
use LIN3S\AdminBundle\Configuration\Type\ActionType;
use LIN3S\AdminBundle\Form\FormHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * New action type class.
 *
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class NewActionType implements ActionType
{
    use EntityId;

    /**
     * The form handler.
     *
     * @var FormHandler
     */
    private $formHandler;

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
     * The URL generator.
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param FormHandler           $formHandler  The form handler
     * @param \Twig_Environment     $twig         The twig instance
     * @param Session               $session      The session
     * @param UrlGeneratorInterface $urlGenerator The URL generator
     */
    public function __construct(
        FormHandler $formHandler,
        \Twig_Environment $twig,
        Session $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->formHandler = $formHandler;
        $this->twig = $twig;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, Entity $config, Request $request, $options = null)
    {
        if (!isset($options['form'])) {
            throw new \InvalidArgumentException(
                sprintf('NewActionType requires a form class as an option')
            );
        }

        $form = $this->formHandler->handleForm(
            $options['form'], $entity, $request
        );

        if ($form->isValid()) {
            $this->session->getFlashBag()->add(
                'lin3s_admin_success',
                sprintf('%s created successfully', $config->name())
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('lin3s_admin_custom', [
                    'action' => isset($options['redirectAction']) ? $options['redirectAction'] : 'edit',
                    'entity' => $config->name(),
                    'id'     => $form->getData()->id(),
                ])
            );
        } elseif ($form->isSubmitted()) {
            $this->session->getFlashBag()->add(
                'lin3s_admin_error',
                sprintf('Errors while creating %s. Please check all fields and try again', $config->name())
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
